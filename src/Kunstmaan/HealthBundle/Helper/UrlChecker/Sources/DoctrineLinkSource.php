<?php

namespace Kunstmaan\HealthBundle\Helper\UrlChecker\Sources;

use Doctrine\Common\Persistence\ObjectManager;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Extractors\UrlExtractor;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces\LinkSourceInterface;
use Kunstmaan\HealthBundle\Helper\UrlChecker\Interfaces\UrlExtractorInterface;
use Kunstmaan\HealthBundle\Model\Link;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DoctrineLinkSource implements LinkSourceInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $className;
    /**
     * @var array
     */
    protected $criteria;
    /**
     * @var array
     */
    protected $properties;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @var UrlExtractorInterface
     */
    protected $urlExtractor;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var string
     */
    protected $extra;

    function __construct(RegistryInterface $doctrine, $className, $route, array $properties, UrlExtractorInterface $urlExtractor, $criteria = array(), $extra = null)
    {
        $this->manager = $doctrine->getManager();
        $this->className = $className;
        $this->properties = $properties;
        $this->criteria = $criteria;
        $this->propertyAccessor = new PropertyAccessor();
        $this->urlExtractor = $urlExtractor ?: new UrlExtractor();
        $this->route = $route;
        $this->extra = $extra;
    }

    public function getLinks()
    {
        $collection = $this->manager->getRepository($this->className)->findBy($this->criteria);
        $classMetaData = $this->manager->getClassMetadata($this->className);
        $links = array();
        foreach ($collection as $entity) {
            foreach ($this->properties as $property) {
                $text = $this->propertyAccessor->getValue($entity, $property);
                if (null == $text) {
                    continue;
                }
                $id = implode('#', $classMetaData->getIdentifierValues($entity));
                $urls = $this->urlExtractor->extract($text);
                foreach ($urls as $url) {
                    $links[] = new Link($url, $this->route, array('id' => $id), $this->extra);
                }
            }
        }

        return $links;
    }
}