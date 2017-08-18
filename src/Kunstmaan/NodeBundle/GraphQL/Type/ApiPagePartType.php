<?php

namespace Kunstmaan\NodeBundle\GraphQL\Type;

use Kunstmaan\NodeBundle\GraphQL\Union\UnionPageType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class ApiPagePartType.
 */
class ApiPagePartType extends AbstractObjectType
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

        parent::__construct(['fields' => $this->buildFields($fields), 'name' => 'PagePart'.$this->name]);
    }

    /**
     * @param $fields
     *
     * @return array
     */
    public function buildFields($fields)
    {
        return [
            'context' => new StringType(),
        ];
    }

    public function build($config)
    {
        return;
    }
}

