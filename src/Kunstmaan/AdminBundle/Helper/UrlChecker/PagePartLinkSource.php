<?php

namespace Kunstmaan\AdminBundle\Helper\UrlChecker;

use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\KernelInterface;

class PagePartLinkSource implements LinkSourceInterface
{
    /** @var PagePartUrlExtractor */
    protected $urlExtractor;

    /** @var ManagerRegistry */
    protected $registry;

    /** @var KernelInterface */
    protected $kernel;

    /** @var EntityManager $em */
    protected $em;

    /** @var FormFactory */
    protected $formFactory;

    public function __construct(PagePartUrlExtractor $urlExtractor, EntityManager $em, ManagerRegistry $registry, KernelInterface $kernel, FormFactory $formFactory)
    {
        $this->em = $em;
        $this->registry = $registry;
        $this->kernel = $kernel;
        $this->formFactory = $formFactory;
        $this->urlExtractor = $urlExtractor;
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

            $types = [];
            foreach ($pagepartrefs as $pagepartref) {
                $types[$pagepartref->getPagePartEntityname()][] = $pagepartref->getPagePartId();
            }

            // Fetch all the pageparts (only one query per pagepart type)
            $pageParts = [];
            foreach ($types as $classname => $ids) {
                $result = $this->em->getRepository($classname)->findBy(array('id' => $ids));
                $pageParts = array_merge($pageParts, $result);
            }

            foreach ($pageParts as $pagePart) {
                $urls = $this->urlExtractor->extractFields($pagePart, $fields);

                if ($urls) {
                    try {
                        /** @var NodeTranslation $nodeTranslation |null */
                        $nodeTranslation = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->createQueryBuilder('nt')
                            ->join('nt.node', 'n')
                            ->join('nt.publicNodeVersion', 'nv')
                            ->where('nv.refId = :refId')
                            ->andWhere('nv.refEntityName = :refName')
                            ->setParameter('refId', $pagePart->getPageId())
                            ->setParameter('refName', $pagePart->getPageEntityname())
                            ->getQuery()
                            ->getOneOrNullResult();

                        foreach ($urls as $url) {
                            $links[] = new Link($url, array('entity' => $nodeTranslation->getId()));
                        }

                    } catch (NonUniqueResultException $e) {}
                }
            }
        }

        return $links;
    }
}