<?php

namespace Kunstmaan\ApiBundle\Service\Transformers;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\ApiBundle\Model\ApiPage;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;

/**
 * Class PageTransformer
 */
class PageTransformer implements TransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PageTransformer constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * This function will determine if the DataTransformer is eligible for transformation
     *
     * @param $object
     *
     * @return bool
     */
    public function canTransform($object)
    {
        return $object instanceof NodeTranslation;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     *
     * @return ApiPage
     */
    public function transform($nodeTranslation)
    {
        $apiPage = new ApiPage();
        $apiPage->setNodeTranslation($nodeTranslation);
        $apiPage->setNode($nodeTranslation->getNode());
        $apiPage->setNodeVersion($nodeTranslation->getPublicNodeVersion());
        $page = $nodeTranslation->getRef($this->em);
        $apiPage->setPage($page);

        return $apiPage;
    }
}