<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Vite;

class Manifest
{
    /** @var array<string, array{file: string}>|null */
    private ?array $manifest = null;

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

        $file = $manifest[$trimmedPath]['file'] ?? $trimmedPath;

        return rtrim($this->buildDir, '/').'/'.$file;
    }

    /**
     * @return array<string, array{file: string}>
     */
    private function getManifest(): array
    {
        if (null !== $this->manifest) {
            return $this->manifest;
        }

        $fullPath = \sprintf(
            '%s/%s/%s',
            rtrim($this->publicDir, '/'),
            trim($this->buildDir, '/'),
            ltrim($this->manifestPath, '/')
        );

        if (!file_exists($fullPath)) {
            return $this->manifest = [];
        }

        try {
            $contents = file_get_contents($fullPath);
            if (false === $contents) {
                return $this->manifest = [];
            }

            /** @var array<string, array{file: string}> $decoded */
            $decoded = json_decode($contents, true, 512, \JSON_THROW_ON_ERROR);
            $this->manifest = $decoded;

            return $this->manifest;
        } catch (\JsonException) {
            return $this->manifest = [];
        }
    }
}
