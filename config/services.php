<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Fabricity\Bundle\ViteBundle\Asset\ViteVersionStrategy;
use Fabricity\Bundle\ViteBundle\Twig\ViteExtension;
use Fabricity\Bundle\ViteBundle\Vite\Manifest;
use Fabricity\Bundle\ViteBundle\Vite\Server;
use Symfony\Contracts\HttpClient\HttpClientInterface;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
            ->private()

        ->set(ViteVersionStrategy::class)
            ->arg('$manifest', service(Manifest::class))

        ->set(Manifest::class)
            ->arg('$publicDir', abstract_arg('defined by bundle configuration'))
            ->arg('$buildDir', abstract_arg('defined by bundle configuration'))
        ->set(Server::class)
            ->arg('$httpClient', service(HttpClientInterface::class))
            ->arg('$url', abstract_arg('defined by bundle configuration'))

        ->set(ViteExtension::class)
            ->args([service(Server::class)])
            ->tag('twig.extension')
    ;
};
