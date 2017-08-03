<?php

namespace Kunstmaan\ApiBundle\GraphQL;

use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\AdminBundle\GraphQL\Mutation\User\UsersMutation;
use Kunstmaan\NodeBundle\GraphQL\Mutation\Page\CreatePagesMutation;
use Kunstmaan\NodeBundle\GraphQL\Mutation\Page\UpdatePagesMutation;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class MutationType.
 */
class MutationType extends AbstractObjectType
{
    /**
     * @var array
     */
    private $entities;

    /**
     * MutationType constructor.
     *
     * @param array $entities
     */
    public function __construct(array $entities)
    {
        parent::__construct();

        $this->entities = $entities;
    }


    /**
     * @param ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config->addFields([
            new UsersMutation(),
        ]);

        /** @var ClassMetadata $entity */
        foreach ($this->entities as $entity) {
            $fields = $this->getFieldTypes($entity);
            $config->addField(new CreatePagesMutation($entity, $fields));
            $config->addField(new UpdatePagesMutation($entity, $fields));
        }
    }

    /**
     * @param ClassMetadata $entity
     *
     * @return array
     */
    private function getFieldTypes(ClassMetadata $entity)
    {
        $fields = [];

        foreach ($entity->getFieldNames() as $fieldName) {
            $properties = $entity->getFieldMapping($fieldName);
            $fields[$fieldName] = $properties;
        }

        return $fields;
    }
}