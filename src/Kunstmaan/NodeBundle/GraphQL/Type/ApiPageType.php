<?php

namespace Kunstmaan\NodeBundle\GraphQL\Type;

use Kunstmaan\NodeBundle\GraphQL\Union\UnionPageType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class ApiPageType.
 */
class ApiPageType extends AbstractObjectType
{
    /**
     * @var array
     */
    private $fields;

    /**
     * AbstractPageType constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;

        parent::__construct();
    }

    public function build($config)
    {
        $config->addFields([
            'type' => new StringType(),
            'node' => new NodeType(),
            'nodeTranslation' => new NodeTranslationType(),
            'nodeVersion' => new NodeVersionType(),
//            'page' => new UnionPageType($this->fields)
        ]);
    }
}

