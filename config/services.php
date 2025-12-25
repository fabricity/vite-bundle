<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Fabricity\Bundle\ViteBundle\Twig\ViteExtension;
use Fabricity\Bundle\ViteBundle\Vite\Server;
use Symfony\Contracts\HttpClient\HttpClientInterface;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('fabricity_vite.server', Server::class)
            ->arg('$httpClient', service(HttpClientInterface::class))
            ->arg('$url', abstract_arg('defined by bundle configuration'))

        ->set('fabricity_vite.twig.extension', ViteExtension::class)
            ->args([service('fabricity_vite.dev_server')])
            ->tag('twig.extension')
    ;
};
