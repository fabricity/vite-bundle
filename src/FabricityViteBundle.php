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

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

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
                ->arrayNode('builds')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('build_dir')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('manifest_path')
                                ->defaultValue('.vite/manifest.json')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('server')
                    ->cannotBeEmpty()
                    ->defaultValue('http://localhost:5173')
                ->end()
            ->end()
        ;
    }

    /**
     * @param array{
     *     public_dir: string,
     *     builds: array<string, array{build_dir: string, manifest_path: string}>,
     *     server: string
     * } $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        $container->services()
            ->get(Server::class)
                ->arg('$url', $config['server'])
        ;

        $services = $container->services();
        foreach ($config['builds'] as $name => $build) {
            $manifestId = 'fabricity_vite.manifest.'.$name;
            $strategyId = 'fabricity_vite.version_strategy.'.$name;

            $services
                ->set($manifestId, Manifest::class)
                    ->arg('$publicDir', $config['public_dir'])
                    ->arg('$buildDir', $build['build_dir'])
                    ->arg('$manifestPath', $build['manifest_path'])
                    ->public()
                ->set($strategyId, ViteVersionStrategy::class)
                    ->arg('$manifest', service($manifestId))
                    ->public()
            ;
        }
    }
}
