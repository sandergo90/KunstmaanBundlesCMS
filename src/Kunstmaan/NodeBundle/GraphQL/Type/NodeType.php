<?php

namespace Kunstmaan\NodeBundle\GraphQL\Type;

use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class NodeType.
 */
class NodeType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields([
            'id' => new IdType(),
            'refEntityName' => new StringType(),
            'hiddenFromNav' => new BooleanType(),
            'internalName' => new StringType(),
            'nodeTranslations' => new ListType(new NodeTranslationType()),
            'children' => new ListType(new NodeType()),
            'parent' => new NodeType()
        ]);
    }
}

