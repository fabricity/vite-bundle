<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public function __construct()
    {
        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Fabricity\Bundle\ViteBundle\FabricityViteBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function ($container) {
            $container->loadFromExtension('framework', [
                'test' => true,
                'secret' => 'f4br1c1ty',
                'assets' => [
                    'packages' => [
                        'frontend' => [
                            'version_strategy' => 'fabricity_vite.version_strategy.frontend',
                        ],
                        'backend' => [
                            'version_strategy' => 'fabricity_vite.version_strategy.backend',
                        ],
                    ],
                ],
            ]);
            $container->loadFromExtension('fabricity_vite', [
                'public_dir' => __DIR__.'/fixtures/public',
                'server' => 'http://localhost:5432',
                'builds' => [
                    'frontend' => ['build_dir' => 'frontend'],
                    'backend' => ['build_dir' => 'backend'],
                ],
            ]);
        });
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/cache'.spl_object_hash($this);
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/logs'.spl_object_hash($this);
    }
}
