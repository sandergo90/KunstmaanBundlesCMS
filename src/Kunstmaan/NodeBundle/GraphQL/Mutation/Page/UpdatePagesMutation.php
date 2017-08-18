<?php

namespace Kunstmaan\NodeBundle\GraphQL\Mutation\Page;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\ApiBundle\Helper\GraphQLHelper;
use Kunstmaan\NodeBundle\GraphQL\Type\AbstractPageType;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

/**
 * Class UpdatePagesMutation.
 */
class UpdatePagesMutation extends AbstractContainerAwareField
{
    /**
     * @var ClassMetadata
     */
    private $entity;

    /**
     * @var GraphQLHelper
     */
    private $helper;

    /**
     * CreatePagesMutation constructor.
     *
     * @param ClassMetadata $entity
     * @param GraphQLHelper $helper
     */
    public function __construct(ClassMetadata $entity, GraphQLHelper $helper)
    {
        $this->entity = $entity;
        $this->helper = $helper;

        parent::__construct();
    }

    public function getName()
    {
        return 'update'.$this->entity->getReflectionClass()->getShortName();
    }

    public function build(FieldConfig $config)
    {
        $config
            ->addArguments([
                'id' => new NonNullType(new IdType())
            ]);

        $arguments = $this->helper->getArguments($this->entity);

        $config->addArguments($arguments);

    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $container = $info->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');
        $name = $this->getEntity()->getName();

        $entity = $em->getRepository($name)->find($args['id']);

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($args as $name => $value) {
            if ($accessor->isWritable($entity, $name)) {
                $accessor->setValue($entity, $name, $value);
            }
        }

        $em->flush();

        return $entity;
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new AbstractPageType($this->helper->getFieldTypes($this->entity), $this->entity->getReflectionClass()->getShortName());
    }
}