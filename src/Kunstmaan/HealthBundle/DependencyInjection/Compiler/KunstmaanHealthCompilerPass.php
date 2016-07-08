<?php

namespace Kunstmaan\HealthBundle\DependencyInjection\Compiler;

use Kunstmaan\ConfigBundle\Entity\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class KunstmaanHealthCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('kunstmaan_health.deadlink_finder')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_health.deadlink_finder');

        foreach ($container->findTaggedServiceIds('kunstmaan_health.link_source') as $id => $def) {
            $definition->addMethodCall('addLinkSource', array(new Reference($id)));
        }
    }
}
