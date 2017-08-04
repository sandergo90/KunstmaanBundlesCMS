<?php

namespace Kunstmaan\ApiBundle\GraphQL;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Config\Schema\SchemaConfig;

class Schema extends AbstractSchema
{
    /**
     * @var QueryType
     */
    private $queryType;

    /**
     * @var MutationType
     */
    private $mutationType;

    /**
     * Schema constructor.
     *
     * @param QueryType    $queryType
     * @param MutationType $mutationType
     */
    public function __construct(QueryType $queryType, MutationType $mutationType)
    {
        $this->queryType = $queryType;
        $this->mutationType = $mutationType;

        parent::__construct();
    }

    public function build(SchemaConfig $config)
    {
        $config
            ->setQuery($this->queryType)
            ->setMutation($this->mutationType);
    }
}

