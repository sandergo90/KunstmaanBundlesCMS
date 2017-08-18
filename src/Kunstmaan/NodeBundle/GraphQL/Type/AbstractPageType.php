<?php

namespace Kunstmaan\NodeBundle\GraphQL\Type;

use Doctrine\DBAL\Types\Type;
use Kunstmaan\NodeBundle\GraphQL\Union\UnionPageType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class AbstractPageType.
 */
class AbstractPageType extends AbstractObjectType
{
    /**
     * AbstractPageType constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function build($config)
    {
    }
}

