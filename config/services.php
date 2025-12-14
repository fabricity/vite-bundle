<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Fabricity\Bundle\ViteBundle\Twig\ViteExtension;
use Fabricity\Bundle\ViteBundle\Twig\ViteRuntime;
use Fabricity\Bundle\ViteBundle\Vite\DevServer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('fabricity_vite.dev_server', DevServer::class)
            ->arg('$httpClient', service(HttpClientInterface::class))
            ->arg('$devServerUrl', abstract_arg('defined by bundle configuration'))

        ->set('fabricity_vite.twig.extension', ViteExtension::class)
            ->tag('twig.extension')

        ->set('fabricity_vite.twig.runtime', ViteRuntime::class)
            ->args([service('fabricity_vite.dev_server')])
            ->tag('twig.runtime')
    ;
};
