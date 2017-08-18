<?php

namespace Kunstmaan\NodeBundle\GraphQL\Union;

use Kunstmaan\AdminBundle\Entity\GraphQLInterface;
use Kunstmaan\NodeBundle\GraphQL\Type\AbstractPageType;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\Union\AbstractUnionType;

/**
 * Class UnionPageType.
 */
class UnionPageType extends AbstractUnionType
{
    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $types = [];

    /**
     * @var string
     */
    private $name;

    /**
     * UnionPageType constructor.
     *
     * @param array  $fields
     * @param string $name
     */
    public function __construct(array $fields, $name)
    {
        $this->fields = $fields;
        $this->name = $name;

        parent::__construct();
    }


    public function getTypes()
    {
        $config = [
            'name' => $this->name,
        ];
        $types = [new AbstractPageType($this->fields, $config)];

        if (!empty($this->types)) {
            foreach ($this->types as $type) {
                $types[] = new $type();
            }
        }

        return $types;
    }

    public function resolveType($object, ResolveInfo $resolveInfo = null)
    {
        $config = [
            'name' => $this->name,
        ];

        return new AbstractPageType($this->fields, $config);
    }
}

