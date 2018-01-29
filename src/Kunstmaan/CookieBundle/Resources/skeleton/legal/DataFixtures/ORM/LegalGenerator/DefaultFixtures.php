<?php

namespace {{ namespace }}\DataFixtures\ORM\LegalGenerator;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kunstmaan\CookieBundle\Entity\Cookie;
use Kunstmaan\CookieBundle\Entity\CookieType;
use Kunstmaan\MediaBundle\Helper\Services\MediaCreatorService;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\PagePartBundle\Helper\Services\PagePartCreatorService;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use {{ namespace }}\Entity\Pages\LegalFolderPage;
use {{ namespace }}\Entity\Pages\LegalPage;

/**
 * Class DefaultFixtures
 */
class DefaultFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    // Username that is used for creating pages
    const ADMIN_USERNAME = 'admin';

    /** @var ContainerInterface */
    private $container;

    /** @var ObjectManager */
    private $manager;

    /** @var PageCreatorService */
    private $pageCreator;

    /** @var MediaCreatorService */
    private $mediaCreator;

    /** @var PagePartCreatorService */
    private $pagePartCreator;

    /** @var array */
    private $requiredLocales;

    /** @var Slugifier */
    private $slugifier;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->pageCreator = $this->container->get('kunstmaan_node.page_creator_service');
        $this->mediaCreator = $this->container->get('kunstmaan_media.media_creator_service');
        $this->pagePartCreator = $this->container->get('kunstmaan_pageparts.pagepart_creator_service');
        $this->translator = $this->container->get('translator.default');
        $this->slugifier = $this->container->get('kunstmaan_utilities.slugifier');
        $this->requiredLocales = explode('|', $this->container->getParameter('requiredlocales'));

        $this->createLegalPages();
        $this->createCookieTypes();
    }

    /**
     * Create legal pages
     */
    private function createLegalPages()
    {
        $legalFolderPage = new LegalFolderPage();
        $legalFolderPage->setTitle('Legal');

        $translations = [];
        foreach ($this->requiredLocales as $locale) {
            $translations[] = [
                'language' => $locale,
                'callback' => function (LegalFolderPage $page, NodeTranslation $translation, $seo) {
                    $translation->setTitle('Legal');
                },
            ];
        }

        $options = [
            'parent' => $this->manager->getRepository('KunstmaanNodeBundle:Node')->findOneBy(['internalName' => 'homepage']),
            'page_internal_name' => 'legal',
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => self::ADMIN_USERNAME,
        ];

        $legalFolderNode = $this->pageCreator->createPage($legalFolderPage, $translations, $options);

        $node = $this->createLegalPage($legalFolderNode, $this->translator->trans('kuma.cookie.fixtures.contact.title'), 'legal_contact', 3);
        $this->addContactPageParts($node);

        $node = $this->createLegalPage($legalFolderNode, $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.title'), 'legal_cookie_preferences', 2);
        $this->addCookiePreferencesPageParts($node);

        $node = $this->createLegalPage($legalFolderNode, $this->translator->trans('kuma.cookie.fixtures.privacy_policy.title'), 'legal_privacy_policy', 1);
        $this->addPrivacyPolicyPageParts($node);
    }

    /**
     * Creates the cookie types
     */
    public function createCookieTypes()
    {
        $cookieTypes = ['functional_cookie', 'analyzing_cookie', 'marketing_cookie'];

        foreach ($cookieTypes as $cookieType) {
            $type = new CookieType();
            $type->setInternalName($this->translator->trans('kuma.cookie.fixtures.cookie_types.'.$cookieType.'.internal_name'));
            $type->setName($this->translator->trans('kuma.cookie.fixtures.cookie_types.'.$cookieType.'.name'));
            $type->setShortDescription($this->translator->trans('kuma.cookie.fixtures.cookie_types.'.$cookieType.'.short_description'));
            $type->setLongDescription($this->translator->trans('kuma.cookie.fixtures.cookie_types.'.$cookieType.'.long_description'));
            if ($cookieType === 'functional_cookie') {
                $type->setAlwaysOn(true);
            }

            $this->manager->persist($type);
            $this->createCookies($type);
        }

        $this->manager->flush();
    }

    /**
     * @param CookieType $cookieType
     */
    public function createCookies(CookieType $cookieType)
    {
        $cookie = new Cookie();
        $cookie->setName($this->translator->trans('kuma.cookie.fixtures.cookies.'.$cookieType->getInternalName().'.name'));
        $cookie->setDescription($this->translator->trans('kuma.cookie.fixtures.cookies.'.$cookieType->getInternalName().'.description'));
        $cookie->setType($cookieType);

        $this->manager->persist($cookie);
    }

    /**
     * @param Node $node
     */
    private function addPrivacyPolicyPageParts(Node $node)
    {
        foreach ($this->requiredLocales as $locale) {
            $pageparts = [];

            $pageparts['legal_header'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.privacy_policy.text.1'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                [
                    'setNiv' => 2,
                    'setTitle' => $this->translator->trans('kuma.cookie.fixtures.privacy_policy.headers.1'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.privacy_policy.text.2'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                [
                    'setNiv' => 2,
                    'setTitle' => $this->translator->trans('kuma.cookie.fixtures.privacy_policy.headers.2'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.privacy_policy.text.3'),
                ]
            );

            $this->pagePartCreator->addPagePartsToPage($node, $pageparts, $locale);
        }
    }

    /**
     * @param Node $node
     */
    private function addCookiePreferencesPageParts(Node $node)
    {
        foreach ($this->requiredLocales as $locale) {
            $pageparts = [];

            $folder = $this->manager->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(['rel' => 'image']);
            $imgDir = __DIR__.'/../../../Resources/ui/img/legal/';

            $icon = $this->mediaCreator->createFile($imgDir.'cookie.svg', $folder->getId());
            $pageparts['legal_header'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\LegalCenteredIconPagePart',
                [
                    'setIcon' => $icon,
                ]
            );
            $pageparts['legal_header'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.text.1'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                [
                    'setNiv' => 2,
                    'setTitle' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.headers.1'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.text.2'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\LegalTipPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.tip.1'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\LegalIconTextPagePart',
                [
                    'setTitle' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.icon_text.1.title'),
                    'setSubtitle' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.icon_text.1.subtitle'),
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.icon_text.1.text'),
                    'setIcon' => $this->mediaCreator->createFile($imgDir.'label.svg', $folder->getId()),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                [
                    'setNiv' => 2,
                    'setTitle' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.headers.2'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\LegalCookiesPagePart', []
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\LegalIconTextPagePart',
                [
                    'setTitle' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.icon_text.2.title'),
                    'setSubtitle' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.icon_text.2.subtitle'),
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.icon_text.2.text'),
                    'setIcon' => $this->mediaCreator->createFile($imgDir.'cookie_monster.svg', $folder->getId()),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                [
                    'setNiv' => 2,
                    'setTitle' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.headers.3'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.text.3'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                [
                    'setNiv' => 2,
                    'setTitle' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.headers.4'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.text.4'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                [
                    'setNiv' => 2,
                    'setTitle' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.headers.5'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.text.5'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\LegalTipPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.tip.2'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                [
                    'setNiv' => 2,
                    'setTitle' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.headers.6'),
                ]
            );
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.cookie_preferences.text.6'),
                ]
            );

            $this->pagePartCreator->addPagePartsToPage($node, $pageparts, $locale);
        }
    }

    /**
     * @param Node $node
     */
    private function addContactPageParts(Node $node)
    {
        foreach ($this->requiredLocales as $locale) {
            $pageparts = [];

            $pageparts['legal_header'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.contact.text.1'),
                ]
            );
            $pageparts['legal_header'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.contact.text.2'),
                ]
            );
            $pageparts['legal_header'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.contact.text.3'),
                ]
            );

            $this->pagePartCreator->addPagePartsToPage($node, $pageparts, $locale);
        }
    }

    /**
     * @param Node   $parent
     * @param string $title
     * @param string $internalName
     * @param int    $weight
     *
     * @return Node
     */
    private function createLegalPage(Node $parent, string $title, string $internalName, int $weight)
    {
        $legalPage = new LegalPage();
        $legalPage->setTitle($title);

        $translations = [];
        foreach ($this->requiredLocales as $locale) {
            $translations[] = [
                'language' => $locale,
                'callback' => function (LegalPage $page, NodeTranslation $translation, $seo) use ($title, $weight) {
                    $translation->setTitle($title);
                    $translation->setSlug($this->slugifier->slugify($title));
                    $translation->setWeight($weight);
                },
            ];
        }

        $options = [
            'parent' => $parent,
            'page_internal_name' => $internalName,
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => self::ADMIN_USERNAME,
        ];

        return $this->pageCreator->createPage($legalPage, $translations, $options);
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 52;
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}