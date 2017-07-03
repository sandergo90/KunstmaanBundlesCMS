<?php

namespace Kunstmaan\NodeBundle\GraphQL\Type;

use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class NodeVersionType.
 */
class NodeVersionType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields([
            'id' => new IdType(),
            'owner' => new StringType(),
            'refId' => new StringType(),
            'refEntityName' => new StringType(),
        ]);
    }
}

