<?php

namespace Kunstmaan\NodeBundle\GraphQL\Mutation\Page;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Kunstmaan\ApiBundle\Helper\GraphQLHelper;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\GraphQL\Type\AbstractPageType;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
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
        return 'create'.$this->entity->getReflectionClass()->getShortName();
    }

    public function build(FieldConfig $config)
    {
        $arguments = $this->helper->getArguments($this->entity);

        $config->addArguments($arguments);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $container = $info->getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');
        $name = $this->entity->getName();

        $entity = new $name();

        $accessor = PropertyAccess::createPropertyAccessor();
        $fields = $this->helper->getFields($this->entity);

        foreach ($args as $name => $value) {
            if ($accessor->isWritable($entity, $name)) {
                switch ($fields[$name]['type']) {
                    case ClassMetadataInfo::ONE_TO_MANY:
                    case ClassMetadataInfo::MANY_TO_MANY:
                        $targetEntities = [];
                        foreach ($value as $val) {
                            $targetEntities[] = $em->getRepository($fields[$name]['targetEntity'])->find($val);
                        }
                        $accessor->setValue($entity, $name, $targetEntities);
                        break;
                    case ClassMetadataInfo::MANY_TO_ONE:
                        $targetEntity = $em->getRepository($fields[$name]['targetEntity'])->find($value);
                        $accessor->setValue($entity, $name, $targetEntity);
                        break;
                    default:
                        $accessor->setValue($entity, $name, $value);
                        break;
                }
            }
            if ($entity instanceof HasNodeInterface) {
                $this->helper->createPage($entity);
            }
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
        $config = [
//            'name' => $this->entity->getReflectionClass()->getShortName()
            'name' => 'test'
        ];
        return new AbstractPageType($this->helper->getArguments($this->entity), $config);
    }
}