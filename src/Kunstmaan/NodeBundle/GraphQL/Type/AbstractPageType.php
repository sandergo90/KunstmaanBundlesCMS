<?php

namespace Kunstmaan\NodeBundle\GraphQL\Type;

use Kunstmaan\NodeBundle\GraphQL\Union\UnionPageType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class AbstractPageType.
 */
class AbstractPageType extends AbstractObjectType
{
    public function build($config)
    {
        $config->addFields([
            'id' => new IdType(),
            'title' => new StringType(),
            'pageTitle' => new StringType(),
        ]);
    }
}

