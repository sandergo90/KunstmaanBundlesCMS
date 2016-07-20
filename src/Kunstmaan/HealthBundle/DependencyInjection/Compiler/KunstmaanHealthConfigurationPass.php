<?php

namespace Kunstmaan\HealthBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KunstmaanHealthConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $backendConfiguration = $container->getParameter('kunstmaan_health');

        if (empty($backendConfiguration['bundles'])) {
            throw new \RuntimeException('You need to provide at least one bundle name for this bundle to work.');
        }

        $bundles = $container->getParameter('kernel.bundles');

        // Check if bundle exists.
        foreach ($backendConfiguration['bundles'] as $class) {
            if (!array_key_exists($class, $bundles)) {
                throw new \InvalidArgumentException(sprintf('Bundle "%s" does not exist', $class));
            }
        }

        return $backendConfiguration;
    }
}
