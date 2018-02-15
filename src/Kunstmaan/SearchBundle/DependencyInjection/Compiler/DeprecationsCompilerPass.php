<?php

namespace Kunstmaan\SearchBundle\DependencyInjection\Compiler;

use Kunstmaan\SearchBundle\Command\DeleteIndexCommand;
use Kunstmaan\SearchBundle\Command\PopulateIndexCommand;
use Kunstmaan\SearchBundle\Command\SetupIndexCommand;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;
use Kunstmaan\SearchBundle\Provider\ElasticaProvider;
use Kunstmaan\SearchBundle\Provider\SearchProviderChain;
use Kunstmaan\SearchBundle\Search\LanguageAnalysisFactory;
use Kunstmaan\SearchBundle\Search\Search;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\SearchBundle\DependencyInjection\Compiler
 */
class DeprecationsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_search.search', Search::class],
                ['kunstmaan_search.search.factory.analysis', LanguageAnalysisFactory::class],
                ['kunstmaan_search.search_provider_chain', SearchProviderChain::class],
                ['kunstmaan_search.search_provider.elastica', ElasticaProvider::class],
                ['kunstmaan_search.search_configuration_chain', SearchConfigurationChain::class],
                ['kunstmaan_search.command.setup', SetupIndexCommand::class],
                ['kunstmaan_search.command.delete', DeleteIndexCommand::class],
                ['kunstmaan_search.command.populate', PopulateIndexCommand::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_search.search_configuration_chain.class', SearchConfigurationChain::class],
                ['kunstmaan_search.search_provider_chain.class', SearchProviderChain::class],
                ['kunstmaan_search.search.class', Search::class],
                ['kunstmaan_search.search_provider.elastica.class', ElasticaProvider::class],
                ['kunstmaan_search.search.factory.analysis.class', LanguageAnalysisFactory::class],
            ],
            true
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     * @param bool             $parametered
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations, $parametered = false)
    {
        foreach ($deprecations as $deprecation) {
            // Don't allow service with same name as class.
            if ($parametered && $container->getParameter($deprecation[0]) === $deprecation[1]) {
                continue;
            }

            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }

            if ($parametered) {
                $class = $container->getParameter($deprecation[0]);
                $definition->setClass($class);
                $definition->setDeprecated(
                    true,
                    'Override service class with "%service_id%" is deprecated since KunstmaanSearchBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanSearchBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}
