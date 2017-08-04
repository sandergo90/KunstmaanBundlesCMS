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
     * @var array
     */
    private $fields;

    /**
     * AbstractPageType constructor.
     *
     * @param array $fields
     * @param array $config
     */
    public function __construct(array $fields, $config)
    {
        parent::__construct($config);

        $this->fields = $fields;
    }

    public function build($config)
    {
        $config->addFields($this->fields);
    }
}

