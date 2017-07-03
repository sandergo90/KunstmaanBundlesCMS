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
    private $types = [];

    public function getTypes()
    {
        $types = [new AbstractPageType()];

        if (!empty($this->types)) {
            foreach ($this->types as $type) {
                $types[] = new $type();
            }
        }

        return $types;
    }

    public function resolveType($object, ResolveInfo $resolveInfo = null)
    {
        $this->types = $resolveInfo->getContainer()->getParameter('kunstmaan_node.graphql_types');

        if ($object instanceof GraphQLInterface && method_exists($object, 'getGraphQlType')) {
            $class = $object->getGraphQlType();
            return new $class();
        }

        return new AbstractPageType();
    }
}

