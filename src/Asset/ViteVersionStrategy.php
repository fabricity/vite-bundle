<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Asset;

use Fabricity\Bundle\ViteBundle\Vite\DevServer;
use Fabricity\Bundle\ViteBundle\Vite\Manifest;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class ViteVersionStrategy implements VersionStrategyInterface
{
    public function __construct(
        private readonly Manifest $manifest,
        private readonly ?DevServer $server = null,
    ) {
    }

    public function getVersion(string $path): string
    {
        return $this->applyVersion($path);
    }

    public function applyVersion(string $path): string
    {
        if (!str_ends_with($path, '.css') && $this->server?->available()) {
            return $this->server->assetUrl($path);
        }

        return $this->manifest->get($path);
    }
}
