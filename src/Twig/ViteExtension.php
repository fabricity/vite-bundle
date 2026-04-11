<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Twig;

use Fabricity\Bundle\ViteBundle\Vite\DevServer;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class ViteExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly ?DevServer $server = null,
    ) {
    }

    public function getGlobals(): array
    {
        $available = $this->server?->available() ?? false;

        return [
            'vite' => [
                'dev' => $available,
                'devClient' => $this->server?->clientUrl,
            ],
        ];
    }
}
