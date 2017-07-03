<?php

namespace Kunstmaan\NodeBundle\GraphQL\Query\Node;

use Kunstmaan\NodeBundle\GraphQL\Type\NodeType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

/**
 * Class NodesField.
 */
class NodesField extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        $config
            ->addArguments([
                'id' => new IdType(),
                'internalName' => new StringType(),
            ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $repository = $this->container->get('doctrine')->getRepository('KunstmaanNodeBundle:Node');

        return $repository->findBy($args);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ListType(new NodeType());
    }
}