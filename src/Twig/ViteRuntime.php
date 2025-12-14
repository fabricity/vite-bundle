<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Twig;

use Fabricity\Bundle\ViteBundle\Vite\DevServer;
use Twig\Extension\RuntimeExtensionInterface;

class ViteRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly DevServer $devServer,
    ) {
    }

    public function dev(): bool
    {
        return $this->devServer->available();
    }
}
