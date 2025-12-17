<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class FabricityViteBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('dev_server')->defaultValue('http://localhost:5174')
            ->end()
        ;
    }

    /**
     * @param array{ dev_server: string } $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        $container->services()
            ->get('fabricity_vite.dev_server')
                ->arg('$devServerUrl', $config['dev_server'])
        ;
    }
}
