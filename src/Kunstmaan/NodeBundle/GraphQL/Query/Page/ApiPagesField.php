<?php

namespace Kunstmaan\NodeBundle\GraphQL\Query\Page;

use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Entity\GraphQLInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\GraphQL\Type\NodeType;
use Kunstmaan\NodeBundle\GraphQL\Type\ApiPageType;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

/**
 * Class ApiPagesField.
 */
class ApiPagesField extends AbstractContainerAwareField
{
    public function build(FieldConfig $config)
    {
        $config
            ->addArguments([
                'locale' => new StringType(),
                'nodeId' => new IdType(),
            ]);
    }

    public function getName()
    {
        return 'pages';
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $repository = $this->container->get('doctrine')->getRepository('KunstmaanNodeBundle:NodeTranslation');

        /** @var QueryBuilder $qb */
        $qb = $repository->getOnlineNodeTranslationsQueryBuilder($args['locale']);
        $qb
            ->andWhere('n.id = :nodeId')
            ->setParameter('nodeId', $args['nodeId']);

        $pageTypes = $this->container->getParameter('kunstmaan_node.graphql_pages');

        $qb->andWhere('n.refEntityName IN(:refEntityNames)')
            ->setParameter('refEntityNames', $pageTypes);

        $nodeTranslation = $qb->getQuery()->getOneOrNullResult();

        if (!$nodeTranslation instanceof NodeTranslation) {
            throw new NotFoundHttpException('Nodetranslation not found');
        }

        $data = $this->container->get('kunstmaan_api.service.data_transformer')->transform($nodeTranslation);

        return $data;
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ApiPageType();
    }
}