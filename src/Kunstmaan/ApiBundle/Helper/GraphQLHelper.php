<?php

namespace Kunstmaan\ApiBundle\Helper;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeBundle\GraphQL\Type\AbstractFieldsType;
use Kunstmaan\NodeBundle\GraphQL\Type\AbstractInputType;
use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Steylaerts\WebsiteBundle\Entity\Pages\ContentPage;
use Steylaerts\WebsiteBundle\Entity\Pages\HomePage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class GraphQLHelper
 */
class GraphQLHelper
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $bundles;

    /**
     * @var array
     */
    private $entities;

    /**
     * @var PageCreatorService
     */
    private $pageCreatorService;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * GraphQLHelper constructor.
     *
     * @param EntityManagerInterface   $em
     * @param array                    $bundles
     * @param PageCreatorService       $pageCreatorService
     * @param RequestStack             $requestStack
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManagerInterface $em, array $bundles, PageCreatorService $pageCreatorService, RequestStack $requestStack, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->bundles = $bundles;
        $this->pageCreatorService = $pageCreatorService;
        $this->request = $requestStack->getCurrentRequest();
        $this->dispatcher = $dispatcher;
    }


    public function getEntities()
    {
        if (empty($this->entities)) {
            $meta = $this->em->getMetadataFactory()->getAllMetadata();

            foreach ($this->bundles as $bundle) {
                foreach ($meta as $classMetadata) {
                    if (strpos($classMetadata->getName(), $bundle) !== false && stripos($classMetadata->getName(), 'PagePart') === false) {
                        $this->entities[$classMetadata->getName()] = $classMetadata;
                    }
                }
            }
        }
        return $this->entities;
    }

    /**
     * @param ClassMetadata $entity
     *
     * @return array
     */
    public function getFields(ClassMetadata $entity)
    {
        $fields = [];

        foreach ($entity->getFieldNames() as $fieldName) {
            $properties = $entity->getFieldMapping($fieldName);
            $fields[$fieldName] = $properties;
        }

        foreach ($entity->getAssociationNames() as $associationName) {
            $properties = $entity->getAssociationMapping($associationName);
            $fields[$associationName] = $properties;
        }

        return $fields;
    }

    /**
     * @param ClassMetadata $entity
     *
     * @return array
     */
    public function getArguments(ClassMetadata $entity)
    {
        $fields = $this->getFields($entity);
        $arguments = [];

        foreach ($fields as $name => $properties) {
            if (!isset($properties['id'])) {
                switch ($properties['type']) {
                    case Type::BIGINT:
                    case Type::INTEGER:
                        if ($this->fieldIsNullable($properties)) {
                            $arguments[$name] = new IntType();
                        } else {
                            $arguments[$name] = new NonNullType(new IntType());
                        }
                        break;
                    case Type::BOOLEAN:
                        if ($this->fieldIsNullable($properties)) {
                            $arguments[$name] = new BooleanType();
                        } else {
                            $arguments[$name] = new NonNullType(new BooleanType());
                        }
                        break;
                    case Type::DATETIME:
                    case Type::DATE:
                        if ($this->fieldIsNullable($properties)) {
                            $arguments[$name] = new DateTimeType();
                        } else {
                            $arguments[$name] = new NonNullType(new DateTimeType());
                        }
                        break;
//                    case ClassMetadataInfo::ONE_TO_MANY:
//                        if ($this->associationIsNullable($properties)) {
//                            $arguments[$name.'Ids'] = new ListType(new IdType());
//                            // Retrieve fields of target entity.
//                            $targetEntityMetaData = $this->entities[$properties['targetEntity']];
//                            $targetArguments = $this->getArguments($targetEntityMetaData);
//                            $arguments[$name] = new ListType(new AbstractInputType($targetArguments, $targetEntityMetaData->getReflectionClass()->getShortName()));
//                        } else {
//                            $arguments[$name.'Ids'] = new ListType(new NonNullType(new IdType()));
//                            // Retrieve fields of target entity.
//                            $targetEntityMetaData = $this->entities[$properties['targetEntity']];
//                            $targetArguments = $this->getArguments($targetEntityMetaData);
//                            $arguments[$name] = new ListType(new NonNullType(new AbstractInputType($targetArguments, $targetEntityMetaData->getReflectionClass()->getShortName())));
//                        }
//                        break;
                    case ClassMetadataInfo::MANY_TO_ONE:
                        if ($this->associationIsNullable($properties)) {
                            $arguments[$name] = new IdType();
                        } else {
                            $arguments[$name] = new NonNullType(new IdType());
                        }
                        // Retrieve fields of target entity.
                        if ($entity->getName() == HomePage::class) {
                            $targetEntityMetaData = $this->entities[$properties['targetEntity']];
                            $targetArguments = $this->getArguments($targetEntityMetaData);
                            $arguments[$name] = new AbstractFieldsType($targetArguments, $targetEntityMetaData->getReflectionClass()->getShortName());
                        }
                        break;
//                    case ClassMetadataInfo::MANY_TO_MANY:
//                        if ($this->associationIsNullable($properties)) {
//                            $arguments[$name] = new ListType(new IdType());
//                            // Retrieve fields of target entity.
////                            $targetEntityMetaData = $this->entities[$properties['targetEntity']];
////                            $targetArguments = $this->getArguments($targetEntityMetaData);
////                            $arguments[$name] = new ListType(new AbstractInputType($targetArguments, $targetEntityMetaData->getReflectionClass()->getShortName()));
//                        } else {
//                            $arguments[$name] = new ListType(new NonNullType(new IdType()));
//                            // Retrieve fields of target entity.
////                            $targetEntityMetaData = $this->entities[$properties['targetEntity']];
////                            $targetArguments = $this->getArguments($targetEntityMetaData);
////                            $arguments[$name] = new ListType(new NonNullType(new AbstractInputType($targetArguments, $targetEntityMetaData->getReflectionClass()->getShortName())));
//                        }
//                        break;
                    default:
                        if ($this->fieldIsNullable($properties)) {
                            $arguments[$name] = new StringType();
                        } else {
                            $arguments[$name] = new NonNullType(new StringType());
                        }
                        break;
                }
            }
        }

        return $arguments;
    }

    /**
     * @param $properties
     *
     * @return bool
     */
    private function fieldIsNullable($properties)
    {
        return isset($properties['nullable']) && $properties['nullable'] === true;
    }

    /**
     * @param $properties
     *
     * @return bool
     */
    private function associationIsNullable($properties)
    {
        return isset($properties['joinColumns']) && (!isset($properties['joinColumns'][0]['nullable']) || $properties['joinColumns'][0]['nullable'] === true);
    }

    public function createPage(HasNodeInterface $page)
    {
        $translations[] = ['language' => $this->request->getLocale(), 'callback' => function (HasNodeInterface $page, NodeTranslation $translation, $seo) {
            $translation->setTitle($page->getTitle());
        }];

        $options = [
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => 'admin',
        ];

        /** @var Node $node */
        $node = $this->pageCreatorService->createPage($page, $translations, $options);

        $this->dispatcher->dispatch(
            Events::ADD_NODE,
            new NodeEvent(
                $node, $node->getNodeTranslation($this->request->getLocale()), $node->getNodeTranslation($this->request->getLocale())->getPublicNodeVersion(), $page
            )
        );
    }
}