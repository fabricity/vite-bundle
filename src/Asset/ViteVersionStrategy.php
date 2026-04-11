<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Asset;

use Fabricity\Bundle\ViteBundle\Vite\Manifest;
use Fabricity\Bundle\ViteBundle\Vite\Server;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class ViteVersionStrategy implements VersionStrategyInterface
{
    public function __construct(
        private readonly Manifest $manifest,
        private readonly ?Server $server = null,
    ) {
    }

    public function getVersion(string $path): string
    {
        return $this->applyVersion($path);
    }

    public function applyVersion(string $path): string
    {
        if (!str_ends_with($path, '.css') && $this->server?->available()) {
            return rtrim($this->server->url, '/').'/'.ltrim($path, '/');
        }

        return $this->manifest->get($path);
    }
}
