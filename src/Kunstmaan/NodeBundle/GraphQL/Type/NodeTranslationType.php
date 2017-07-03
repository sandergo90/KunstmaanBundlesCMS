<?php

namespace Kunstmaan\NodeBundle\GraphQL\Type;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class NodeTranslationType.
 */
class NodeTranslationType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields([
            'id' => new IdType(),
            'lang' => new StringType(),
            'online' => new BooleanType(),
            'title' => new StringType(),
            'weight' => new IntType(),
            'publicNodeVersion' => new NodeVersionType()
        ]);
    }
}

