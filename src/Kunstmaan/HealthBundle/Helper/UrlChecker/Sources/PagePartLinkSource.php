<?php

namespace Kunstmaan\HealthBundle\Helper\UrlChecker\Sources;

use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Extractors\PagePartUrlExtractor;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces\LinkSourceInterface;
use Kunstmaan\HealthBundle\Model\Link;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\KernelInterface;

class PagePartLinkSource implements LinkSourceInterface
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

    public function __construct(PagePartUrlExtractor $urlExtractor, EntityManagerInterface $em, ManagerRegistry $registry, KernelInterface $kernel, FormFactory $formFactory)
    {
        $this->urlExtractor = $urlExtractor;
        $this->em = $em;
        $this->registry = $registry;
        $this->kernel = $kernel;
        $this->formFactory = $formFactory;
    }

    public function getLinks()
    {
        $manager = new DisconnectedMetadataFactory($this->registry);
        $bundle = $this->kernel->getBundle('AcertaCommonBundle');
        $metadata = $manager->getBundleMetadata($bundle);

        $entities = [];
        $links = [];

        /** @var ClassMetadata $meta */
        foreach ($metadata->getMetadata() as $meta) {
            $ref = new \ReflectionClass($meta->getName());

            if ($ref->hasMethod('getDefaultAdminType')) {
                $adminType = call_user_func(array($ref->name, 'getDefaultAdminType'));

                if ($adminType) {
                    $refAdminType = new \ReflectionClass($adminType);

                    if ($refAdminType->hasMethod('buildForm')) {
                        /** @var Form $form */
                        $form = $this->formFactory->create($refAdminType->getName());

                        if (!empty($form->all())) {
                            /** @var Form $child */
                            foreach ($form->all() as $child) {

                                $type = $child->getConfig()->getType()->getInnerType();

                                if (get_class($type) == URLChooserType::class) {
                                    $entities[$meta->getName()][] = $child->getName();
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

            $pageParts = [];
            foreach ($pagepartrefs as $pagepartref) {
                $result = $this->em->getRepository($pagepartref->getPagePartEntityname())->findOneBy(array('id' => $pagepartref->getPagePartId()));
                $pageParts[] = [
                    'ref' => $pagepartref,
                    'entity' => $result
                ];
            }

            foreach ($pageParts as $pagePart) {
                /** @var PagePartRef $ref */
                $ref = $pagePart['ref'];
                /** @var PagePartInterface $entity */
                $entity = $pagePart['entity'];

                $urls = $this->urlExtractor->extractFields($entity, $fields);

                if ($urls) {
                    try {
                        /** @var NodeTranslation $nodeTranslation |null */
                        $nodeTranslation = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->createQueryBuilder('nt')
                            ->join('nt.node', 'n')
                            ->join('nt.publicNodeVersion', 'nv')
                            ->where('nv.refId = :refId')
                            ->andWhere('nv.refEntityName = :refName')
                            ->andWhere('n.deleted = 0')
                            ->setParameter('refId', $ref->getPageId())
                            ->setParameter('refName', $ref->getPageEntityname())
                            ->getQuery()
                            ->getOneOrNullResult();

                        if ($nodeTranslation) {
                            foreach ($urls as $url) {
                                $links[] = new Link($url, 'KunstmaanNodeBundle_nodes_edit', array('id' => $nodeTranslation->getNode()->getId()));
                            }
                        }

                    } catch (NonUniqueResultException $e) {
                    }
                }
            }
        }

        return $links;
    }
}