<?php
/**
 * Created by PhpStorm.
 * User: kodeus
 * Date: 19/07/16
 * Time: 16:01
 */

namespace Kunstmaan\HealthBundle\Helper\UrlChecker\Sources;


use Acerta\IWSBundle\Form\PageParts\PartnersPagePartAdminType;
use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Extractors\PagePartUrlExtractor;
use Kunstmaan\NodeBundle\Helper\URLHelper;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\KernelInterface;

class PagePartCollector
{
    /** @var PagePartUrlExtractor */
    protected $urlExtractor;

    /** @var EntityManagerInterface $em */
    protected $em;

    /** @var ManagerRegistry */
    protected $registry;

    /** @var KernelInterface */
    protected $kernel;

    /** @var FormFactory */
    protected $formFactory;

    /** @var URLHelper */
    protected $urlHelper;

    /** @var array */
    protected $configuration;

    public function __construct(PagePartUrlExtractor $urlExtractor, EntityManagerInterface $em, ManagerRegistry $registry, KernelInterface $kernel, FormFactory $formFactory, URLHelper $urlHelper, $configuration = [])
    {
        $this->urlExtractor = $urlExtractor;
        $this->em = $em;
        $this->registry = $registry;
        $this->kernel = $kernel;
        $this->formFactory = $formFactory;
        $this->urlHelper = $urlHelper;
        $this->configuration = $configuration;
    }

    public function getPageParts($formType)
    {
        $pageParts = [];
        $manager = new DisconnectedMetadataFactory($this->registry);

        foreach ($this->configuration['bundles'] as $name) {
            $bundle = $this->kernel->getBundle($name);
            $metadata = $manager->getBundleMetadata($bundle);

            $entities = [];

            /** @var ClassMetadata $meta */
            foreach ($metadata->getMetadata() as $meta) {
                $ref = new \ReflectionClass($meta->getName());

                if ($ref->hasMethod('getDefaultAdminType')) {
                    $adminTypeMethod = new \ReflectionMethod($ref->name, 'getDefaultAdminType');
                    $adminType = $adminTypeMethod->invoke(new $ref->name);

                    if ($adminType) {
                        $refAdminType = new \ReflectionClass($adminType);

                        if ($refAdminType->hasMethod('buildForm')) {
                            /** @var Form $form */
                            $form = $this->formFactory->create($refAdminType->getName(), null);

                            if (!empty($form->all())) {
                                /** @var Form $child */
                                foreach ($form->all() as $child) {
                                    $type = $child->getConfig()->getType()->getInnerType();

                                    if (is_array($formType)) {
                                        if (in_array(get_class($type), $formType)) {
                                            $entities[$meta->getName()][] = $child->getName();
                                        }
                                    } else {
                                        if (get_class($type) == $formType) {
                                            $entities[$meta->getName()][] = $child->getName();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            foreach ($entities as $ref => $fields) {
                $pagepartrefs = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->findBy(
                    array(
                        'pagePartEntityname' => $ref,
                    )
                );


                foreach ($pagepartrefs as $pagepartref) {
                    $result = $this->em->getRepository($pagepartref->getPagePartEntityname())->findOneBy(array('id' => $pagepartref->getPagePartId()));
                    $pageParts[] = [
                        'ref' => $pagepartref,
                        'entity' => $result,
                        'fields' => $fields
                    ];
                }
            }
        }

        return $pageParts;
    }
}