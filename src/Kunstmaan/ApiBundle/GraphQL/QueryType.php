<?php

namespace Kunstmaan\ApiBundle\GraphQL;

use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\AdminBundle\GraphQL\Query\User\UsersField;
use Kunstmaan\NodeBundle\GraphQL\Query\Node\NodesField;
use Kunstmaan\NodeBundle\GraphQL\Query\NodeTranslation\NodeTranslationsField;
use Kunstmaan\NodeBundle\GraphQL\Query\Page\ApiPagesField;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class QueryType.
 */
class QueryType extends AbstractObjectType
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
            new UsersField(),
            new NodesField(),
            new NodeTranslationsField(),
        ]);

        /** @var ClassMetadata $entity */
        foreach ($this->entities as $entity) {
            $fields = $this->getFieldTypes($entity);
            $config->addField(new ApiPagesField($entity, $fields));
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