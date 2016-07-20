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
        // Add link sources.
        if (!$container->hasDefinition('kunstmaan_health.deadlink_finder')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_health.deadlink_finder');

        foreach ($container->findTaggedServiceIds('kunstmaan_health.link_source') as $id => $def) {
            $definition->addMethodCall('addLinkSource', array(new Reference($id)));
        }
        foreach ($container->findTaggedServiceIds('kunstmaan_health.doctrine_link_source') as $id => $def) {
            $definition->addMethodCall('addLinkSource', array(new Reference($id)));
        }

        // Add health widgets
        if (!$container->hasDefinition('kunstmaan_health.manager.widgets')) {
            return;
        }

        $definition = $container->getDefinition('kunstmaan_health.manager.widgets');

        foreach ($container->findTaggedServiceIds('kunstmaan_health.widget') as $id => $tags) {
            foreach ($tags as $tag) {
                if (!empty($tag['method'])) {
                    $widget = array(new Reference($id), $tag['method']);
                } else {
                    $widget = new Reference($id);
                }
                $definition->addMethodCall('addWidget', array($widget));
            }
        }
    }
}
