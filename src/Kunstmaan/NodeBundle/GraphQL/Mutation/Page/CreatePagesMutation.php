<?php

namespace Kunstmaan\NodeBundle\GraphQL\Mutation\Page;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\NodeBundle\GraphQL\Type\AbstractPageType;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

/**
 * Class CreatePagesMutation.
 */
class CreatePagesMutation extends AbstractContainerAwareField
{
    /**
     * @var ClassMetadata
     */
    private $entity;

    /**
     * @var array
     */
    private $fields;

    /**
     * UpdatePagesMutation constructor.
     *
     * @param ClassMetadata $entity
     * @param array         $fields
     */
    public function __construct(ClassMetadata $entity, array $fields)
    {
        $this->entity = $entity;
        $this->fields = $fields;

        parent::__construct();
    }

    /**
     * @return ClassMetadata
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function getName()
    {
        return 'create'.$this->getEntity()->getReflectionClass()->getShortName();
    }

    public function build(FieldConfig $config)
    {
        foreach ($this->getFields() as $name => $properties) {
            if (!isset($properties['id'])) {
                switch ($properties['type']) {
                    case Type::BIGINT:
                        if ($this->fieldIsNullable($properties)) {
                            $argumentType = new IntType();
                        } else {
                            $argumentType = new NonNullType(new IntType());
                        }
                        break;
                    default:
                        if ($this->fieldIsNullable($properties)) {
                            $argumentType = new StringType();
                        } else {
                            $argumentType = new NonNullType(new StringType());
                        }
                        break;
                }
                $config->addArgument($name, $argumentType);
            }
        }
    }

    /**
     * @param $properties
     *
     * @return bool
     */
    private function fieldIsNullable($properties)
    {
        return isset($properties['nullable']) && $properties['nullable'] === true;
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $container = $info->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');
        $name = $this->getEntity()->getName();

        $entity = new $name();

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($args as $name => $value) {
            $accessor->setValue($entity, $name, $value);
        }

        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new AbstractPageType($this->fields, $this->entity->getReflectionClass()->getShortName());
    }
}