<?php

namespace Kunstmaan\NodeBundle\GraphQL\Type;

use Doctrine\DBAL\Types\Type;
use Kunstmaan\NodeBundle\GraphQL\Union\UnionPageType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class AbstractPageType.
 */
class AbstractPageType extends AbstractObjectType
{
    /**
     * @var array
     */
    private $fields;

    /**
     * @var string
     */
    private $name;

    /**
     * AbstractPageType constructor.
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

    public function getName()
    {
        return $this->name;
    }

    public function build($config)
    {
        foreach ($this->fields as $name => $properties) {
            switch ($properties['type']) {
                case Type::BIGINT:
                    $fieldType = new IntType();
                    break;
                default:
                    $fieldType = new StringType();
                    break;
            }
            $config->addField($name, $fieldType);
        }
    }
}

