<?php

namespace Kunstmaan\NodeBundle\GraphQL\Query\Page;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Entity\GraphQLInterface;
use Kunstmaan\ApiBundle\Helper\GraphQLHelper;
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
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQLBundle\Field\AbstractContainerAwareField;

/**
 * Class ListPagePartsType.
 */
class ListPagePartsType extends AbstractContainerAwareField
{
    /**
     * @var ClassMetadata
     */
    private $entity;

    /**
     * @var GraphQLHelper
     */
    private $helper;

    /**
     * CreatePagesMutation constructor.
     *
     * @param ClassMetadata $entity
     * @param GraphQLHelper $helper
     */
    public function __construct(ClassMetadata $entity, GraphQLHelper $helper)
    {
        $this->entity = $entity;
        $this->helper = $helper;

        parent::__construct();
    }

    /**
     * @return ClassMetadata
     */
    public function getEntity()
    {
        return $this->entity;
    }

    public function getName()
    {
        return 'list'.$this->getEntity()->getReflectionClass()->getShortName();
    }

    public function build(FieldConfig $config)
    {
        $config
            ->addArguments([
                'locale' => new NonNullType(new StringType()),
                'nodeId' => new IdType(),
            ]);
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        $repository = $this->container->get('doctrine')->getRepository('KunstmaanNodeBundle:NodeTranslation');

        /** @var QueryBuilder $qb */
        $qb = $repository->getOnlineNodeTranslationsQueryBuilder($args['locale']);
        if (isset($args['nodeId'])) {
            $qb
                ->andWhere('n.id = :nodeId')
                ->setParameter('nodeId', $args['nodeId']);
        }

        $qb->andWhere('n.refEntityName = :refEntityName')
            ->setParameter('refEntityName', $info->getField()->getEntity()->getName());

        $nodeTranslations = $qb->getQuery()->getResult();
        $data = $this->container->get('kunstmaan_api.service.data_transformer')->transformMultiple($nodeTranslations);

        return $data;
    }

    /**
     * @return AbstractObjectType|AbstractType
     */
    public function getType()
    {
        return new ListType(new ApiPageType($this->helper->getArguments($this->entity), $this->entity->getReflectionClass()->getShortName()));
    }
}