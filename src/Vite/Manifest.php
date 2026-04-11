<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Vite;

class Manifest
{
    /** @var array<string, array{file: string, css?: list<string>}> */
    private array $manifest = [];
    private bool $isLoaded = false;

    public function __construct(
        private readonly string $publicDir,
        private readonly string $buildDir,
        private readonly string $manifestPath = '.vite/manifest.json',
    ) {
    }

    public function get(string $path): string
    {
        $trimmedPath = ltrim($path, '/');
        $manifest = $this->getManifest();

        if (isset($manifest[$trimmedPath])) {
            return rtrim($this->buildDir, '/').'/'.$manifest[$trimmedPath]['file'];
        }

        if (str_ends_with($trimmedPath, '.css')) {
            $jsPath = substr($trimmedPath, 0, -4).'.js';
            if (isset($manifest[$jsPath]['css'][0])) {
                return rtrim($this->buildDir, '/').'/'.$manifest[$jsPath]['css'][0];
            }
        }

        return rtrim($this->buildDir, '/').'/'.$trimmedPath;
    }

    /**
     * @return array<string, array{file: string, css?: list<string>}>
     */
    private function getManifest(): array
    {
        if ($this->isLoaded) {
            return $this->manifest;
        }

        $fullPath = \sprintf(
            '%s/%s/%s',
            rtrim($this->publicDir, '/'),
            trim($this->buildDir, '/'),
            ltrim($this->manifestPath, '/')
        );

        $this->isLoaded = true;

        if (!file_exists($fullPath)) {
            return $this->manifest = [];
        }

        try {
            $contents = (string) @file_get_contents($fullPath);
            /** @var array<string, array{file: string, css?: list<string>}> $decoded */
            $decoded = json_decode($contents, true, 512, \JSON_THROW_ON_ERROR);
            $this->manifest = $decoded;
        } catch (\JsonException|\ValueError) {
            $this->manifest = [];
        }

        return $this->manifest;
    }
}
