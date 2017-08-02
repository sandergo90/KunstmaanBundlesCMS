<?php

namespace Kunstmaan\AdminBundle\GraphQL\Type;

use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class UserMutationType.
 */
class UserMutationType extends AbstractInputObjectType
{
    public function build($config)
    {
        $config->addFields([
            'username' => new NonNullType(new StringType()),
            'email' => new StringType(),
            'enabled' => new BooleanType(),
            'plainPassword' => new StringType(),
            'roles' => new ListType(new StringType())
        ]);
    }
}

