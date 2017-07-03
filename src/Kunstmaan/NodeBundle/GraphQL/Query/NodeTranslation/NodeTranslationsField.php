<?php

namespace Kunstmaan\NodeBundle\GraphQL\Query\NodeTranslation;

use Kunstmaan\NodeBundle\GraphQL\Type\NodeTranslationType;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

/**
 * Class NodeTranslationsField.
 */
class NodeTranslationsField extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        $config
            ->addArguments([
                'id' => new IdType(),
                'lang' => new StringType(),
            ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $repository = $this->container->get('doctrine')->getRepository('KunstmaanNodeBundle:NodeTranslation');

        return $repository->findBy($args);
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ListType(new NodeTranslationType());
    }
}