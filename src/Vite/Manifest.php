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
            '%s/%s/.vite/manifest.json',
            rtrim($this->publicDir, '/'),
            trim($this->buildDir, '/')
        );

        if (!file_exists($fullPath)) {
            return $this->manifest = [];
        }

        try {
            $contents = file_get_contents($fullPath);
            if (false === $contents) {
                return $this->manifest = [];
            }

            $decoded = json_decode($contents, true, 512, \JSON_THROW_ON_ERROR);

            if (!\is_array($decoded)) {
                return $this->manifest = [];
            }

            /* @var array<string, array{file: string}> $decoded */
            return $this->manifest = $decoded;
        } catch (\JsonException) {
            return $this->manifest = [];
        }
    }
}
