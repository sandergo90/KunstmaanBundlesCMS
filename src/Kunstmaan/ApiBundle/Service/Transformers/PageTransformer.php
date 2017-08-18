<?php

namespace Kunstmaan\ApiBundle\Service\Transformers;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\ApiBundle\Model\ApiPage;
use Kunstmaan\ApiBundle\Service\DataTransformerService;
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
     * @var DataTransformerService
     */
    private $dataTransformer;

    /**
     * PageTransformer constructor.
     *
     * @param EntityManagerInterface $em
     * @param DataTransformerService $dataTransformer
     */
    public function __construct(EntityManagerInterface $em, DataTransformerService $dataTransformer)
    {
        $this->em = $em;
        $this->dataTransformer = $dataTransformer;
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

        // Add pageparts
//        $apiPage = $this->dataTransformer->transform($apiPage);

        return $apiPage;
    }
}