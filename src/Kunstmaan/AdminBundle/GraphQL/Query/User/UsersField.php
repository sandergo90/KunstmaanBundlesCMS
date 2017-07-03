<?php

namespace Kunstmaan\AdminBundle\GraphQL\Query\User;

use Kunstmaan\AdminBundle\GraphQL\Type\UserType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

/**
 * Class UsersField.
 */
class UsersField extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        $config
            ->addArguments([
                'id' => new IdType(),
            ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $repository = $this->container->get('doctrine')->getRepository('KunstmaanAdminBundle:User');

        return $repository->findBy($args);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ListType(new UserType());
    }
}