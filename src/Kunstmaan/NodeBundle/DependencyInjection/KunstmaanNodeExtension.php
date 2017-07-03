<?php

namespace Kunstmaan\NodeBundle\DependencyInjection;

use Kunstmaan\AdminBundle\Entity\GraphQLInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KunstmaanNodeExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            ['KunstmaanNodeBundle:Form:formWidgets.html.twig']
        ));

        $container->setDefinition('kunstmaan_node.pages_configuration', new Definition(
            'Kunstmaan\NodeBundle\Helper\PagesConfiguration', [$config['pages']]
        ));

        $container->setParameter('kunstmaan_node.show_add_homepage', $config['show_add_homepage']);
        $container->setParameter('kunstmaan_node.lock_check_interval', $config['lock']['check_interval']);
        $container->setParameter('kunstmaan_node.lock_threshold', $config['lock']['threshold']);
        $container->setParameter('kunstmaan_node.lock_enabled', $config['lock']['enabled']);

        $this->setGraphQLTypes($container);

        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $cmfRoutingExtraConfig['chain']['routers_by_id']['router.default'] = 100;
        $cmfRoutingExtraConfig['chain']['replace_symfony_router'] = true;
        $container->prependExtensionConfig('cmf_routing', $cmfRoutingExtraConfig);

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        // set twig global params
        $twigConfig['globals']['nodebundleisactive'] = true;
        $twigConfig['globals']['publish_later_stepping'] = $config['publish_later_stepping'];
        $twigConfig['globals']['unpublish_later_stepping'] = $config['unpublish_later_stepping'];
        $container->prependExtensionConfig('twig', $twigConfig);
    }

    /**
     * Set a container parameter with the available graphQL page types
     *
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function setGraphQLTypes(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        $types = [];
        $pages = [];

        foreach ($bundles as $bundle) {
            $bundleReflection = new \ReflectionClass($bundle);
            $path = dirname($bundleReflection->getFileName()).'/Entity/Pages';

            if (file_exists($path)) {
                $finder = new Finder();
                $finder->files()->in($path);

                /** @var SplFileInfo $file */
                foreach ($finder as $file) {
                    $baseName = $file->getBasename('.php');
                    $path = '\\'.$bundleReflection->getNamespaceName().'\\Entity\\Pages\\'.$baseName;
                    $object = new $path();

                    if ($object instanceof GraphQLInterface && method_exists($object, 'getGraphQlType')) {
                        $types[] = $object->getGraphQlType();
                        $pages[] = get_class($object);
                    }
                }
            }
        }

        $container->setParameter('kunstmaan_node.graphql_types', $types);
        $container->setParameter('kunstmaan_node.graphql_pages', $pages);
    }
}
