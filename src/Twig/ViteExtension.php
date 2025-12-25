<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Twig;

use Fabricity\Bundle\ViteBundle\Vite\Server;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class ViteExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly Server $devServer,
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'vite' => [
                'server' => $this->devServer->available(),
            ],
        ];
    }
}
