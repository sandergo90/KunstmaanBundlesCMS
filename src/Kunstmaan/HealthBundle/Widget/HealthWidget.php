<?php

namespace Kunstmaan\HealthBundle\Widget;

use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HealthWidget
{
    /** @var string */
    public $name;

    /**
     * @var string $controller
     */
    private $controller;

    /**
     * @param string $name
     * @param string $controller
     */
    function __construct($name, $controller)
    {
        $this->name = $name;
        $this->controller = $controller;
    }

    public function resolvedController()
    {
        $annotationReader = new AnnotationReader();
        $reflectionMethod = new \ReflectionMethod($this->controller, 'widgetAction');
        $methodAnnotations = $annotationReader->getMethodAnnotations($reflectionMethod);
        foreach ($methodAnnotations as $annotation) {
            if ($annotation instanceof Route) {
                if (empty($annotation)) {
                    throw new \Exception("The name is not configured in the annotation");
                }
                /** @var \Sensio\Bundle\FrameworkExtraBundle\Configuration\Route $annotation */
                return $annotation->getName();
            }
        }
        throw new \Exception("There is no route annotation");
    }
}
