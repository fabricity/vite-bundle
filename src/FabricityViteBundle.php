<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle;

use Fabricity\Bundle\ViteBundle\Asset\ViteVersionStrategy;
use Fabricity\Bundle\ViteBundle\Vite\Manifest;
use Fabricity\Bundle\ViteBundle\Vite\Server;
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
                ->scalarNode('public_dir')
                    ->cannotBeEmpty()
                    ->defaultValue('%kernel.project_dir%/public')
                ->end()
                ->scalarNode('build_dir')
                    ->cannotBeEmpty()
                    ->defaultValue('build')
                ->end()
                ->scalarNode('server')
                    ->cannotBeEmpty()
                    ->defaultValue('http://localhost:5173')
                ->end()
            ->end()
        ;
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('framework', [
            'assets' => [
                'version_strategy' => ViteVersionStrategy::class,
            ],
        ]);
    }

    /**
     * @param array{
     *     public_dir: string,
     *     build_dir: string,
     *     server: string
     * } $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        $container->services()
            ->get(Manifest::class)
                ->arg('$publicDir', $config['public_dir'])
                ->arg('$buildDir', $config['build_dir'])
            ->get(Server::class)
                ->arg('$url', $config['server'])
        ;
    }
}
