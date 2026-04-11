<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Asset;

use Fabricity\Bundle\ViteBundle\Vite\Manifest;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class ViteVersionStrategy implements VersionStrategyInterface
{
    public function __construct(
        private readonly Manifest $manifest,
    ) {
    }

    public function getVersion(string $path): string
    {
        return $this->applyVersion($path);
    }

    public function applyVersion(string $path): string
    {
        return $this->manifest->get($path);
    }
}
