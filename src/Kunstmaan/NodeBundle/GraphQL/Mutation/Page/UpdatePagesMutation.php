<?php

namespace Kunstmaan\NodeBundle\GraphQL\Mutation\Page;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\GraphQL\Type\UserMutationType;
use Kunstmaan\AdminBundle\GraphQL\Type\UserType;
use Kunstmaan\NodeBundle\GraphQL\Type\AbstractPageType;
use Kunstmaan\NodeBundle\GraphQL\Union\UnionPageType;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
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
        return 'update'.$this->entity->getReflectionClass()->getShortName();
    }

    public function build(FieldConfig $config)
    {
        $config
            ->addArguments([
                'id' => new IdType()
            ]);

        foreach ($this->fields as $name => $properties) {
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
//        $container = $info->getContainer();
//        /** @var EntityManagerInterface $em */
//        $em = $container->get('doctrine.orm.entity_manager');
//        $userFields = $args['user'];
//
//        $user = $em->getRepository('KunstmaanAdminBundle:User')->findOneBy([
//            'username' => $userFields['username']
//        ]);
//
//        if (!$user) {
//            $user = new User();
//        }
//
//        $accessor = PropertyAccess::createPropertyAccessor();
//
//        foreach ($userFields as $name => $value) {
//            $accessor->setValue($user, $name, $value);
//        }
//
//        $em->persist($user);
//        $em->flush();
//
//        return $user;
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new AbstractPageType($this->fields, $this->getEntity()->getReflectionClass()->getShortName());
    }
}