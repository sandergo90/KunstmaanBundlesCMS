<?php

namespace Kunstmaan\ApiBundle\GraphQL;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Config\Schema\SchemaConfig;

class Schema extends AbstractSchema
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $bundles;

    /**
     * Schema constructor.
     *
     * @param EntityManagerInterface $em
     * @param array                  $bundles
     */
    public function __construct(EntityManagerInterface $em, array $bundles)
    {
        $this->em = $em;
        $this->bundles = $bundles;

        parent::__construct([]);
    }


    /**
     * @return array
     */
    private function getEntities()
    {
        $entities = [];
        $meta = $this->em->getMetadataFactory()->getAllMetadata();

        foreach ($this->bundles as $bundle) {
            foreach ($meta as $classMetadata) {
                if (strpos($classMetadata->getName(), $bundle) !== false) {
                    $entities[] = $classMetadata;
                }
            }
        }

        return $entities;
    }


    public function build(SchemaConfig $config)
    {
        $config
            ->setQuery(new QueryType($this->getEntities()))
            ->setMutation(new MutationType($this->getEntities()));
    }
}

