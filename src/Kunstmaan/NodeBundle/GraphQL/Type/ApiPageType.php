<?php

namespace Kunstmaan\NodeBundle\GraphQL\Type;

use Kunstmaan\NodeBundle\GraphQL\Union\UnionPageType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class ApiPageType.
 */
class ApiPageType extends AbstractObjectType
{
    /**
     * @var string
     */
    private $name;

    /**
     * ApiPageType constructor.
     *
     * @param array  $fields
     * @param string $name
     */
    public function __construct(array $fields, $name)
    {
        $this->name = $name;

        parent::__construct(['fields' => $this->buildFields($fields), 'name' => 'API'.$this->name]);
    }

    /**
     * @param $fields
     *
     * @return array
     */
    public function buildFields($fields)
    {
        return [
            'type' => new StringType(),
            'node' => new NodeType(),
            'nodeTranslation' => new NodeTranslationType(),
            'nodeVersion' => new NodeVersionType(),
            'page' => new AbstractPageType(['name' => $this->name, 'fields' => $fields]),
            'pageParts' => [
                'name' => 'pageParts',
                'type' => new ListType(new StringType()),
                'resolve' => ['@kunstmaan_api.helper.pagepart_resolver', 'resolve']
            ]
        ];
    }

    public function build($config)
    {
        return;
    }
}

