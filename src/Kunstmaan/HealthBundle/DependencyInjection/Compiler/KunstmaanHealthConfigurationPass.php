<?php

namespace Kunstmaan\HealthBundle\DependencyInjection\Compiler;

use Kunstmaan\ConfigBundle\Entity\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KunstmaanHealthConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }
}
