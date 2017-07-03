<?php

namespace Kunstmaan\AdminBundle\GraphQL\Type;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class UserType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields([
            'id' => new IdType(),
            'username' => new StringType(),
            'email' => new StringType()
        ]);
    }
}

