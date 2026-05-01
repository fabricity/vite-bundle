<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Tests\Unit\Vite;

use Fabricity\Bundle\ViteBundle\Vite\Manifest;
use PHPUnit\Framework\TestCase;

class ManifestTest extends TestCase
{
    private string $publicDir;

    protected function setUp(): void
    {
        $this->publicDir = sys_get_temp_dir().'/vite-manifest-test-'.uniqid();
        mkdir($this->publicDir.'/build/.vite', 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->publicDir);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach ((array) scandir($dir) as $item) {
            if ('.' === $item || '..' === $item) {
                continue;
            }

            $path = $dir.'/'.$item;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }

    private function writeManifest(mixed $data, string $buildDir = 'build'): void
    {
        file_put_contents(
            $this->publicDir.'/'.$buildDir.'/.vite/manifest.json',
            \is_string($data) ? $data : json_encode($data)
        );
    }

    public function testGetResolvesFileFromManifest(): void
    {
        $this->writeManifest(['src/main.js' => ['file' => 'assets/main-abc123.js']]);

        $manifest = new Manifest($this->publicDir, 'build');

        self::assertSame('build/assets/main-abc123.js', $manifest->get('src/main.js'));
    }

    public function testGetStripsLeadingSlash(): void
    {
        $this->writeManifest(['src/main.js' => ['file' => 'assets/main-abc123.js']]);

        $manifest = new Manifest($this->publicDir, 'build');

        self::assertSame('build/assets/main-abc123.js', $manifest->get('/src/main.js'));
    }

    public function testGetResolvesCssFromJsEntryWithCssArray(): void
    {
        $this->writeManifest([
            'src/main.js' => [
                'file' => 'assets/main-abc123.js',
                'css' => ['assets/main-abc123.css'],
            ],
        ]);

        $manifest = new Manifest($this->publicDir, 'build');

        self::assertSame('build/assets/main-abc123.css', $manifest->get('src/main.css'));
    }

    public function testGetFallsBackToPathWhenCssEntryHasNoJsCounterpart(): void
    {
        $this->writeManifest(['src/main.js' => ['file' => 'assets/main-abc123.js']]);

        $manifest = new Manifest($this->publicDir, 'build');

        self::assertSame('build/src/main.css', $manifest->get('src/main.css'));
    }

    public function testGetFallsBackToPathWhenEntryNotInManifest(): void
    {
        $this->writeManifest([]);

        $manifest = new Manifest($this->publicDir, 'build');

        self::assertSame('build/src/unknown.js', $manifest->get('src/unknown.js'));
    }

    public function testGetFallsBackToPathWhenManifestFileDoesNotExist(): void
    {
        $manifest = new Manifest($this->publicDir, 'missing-build');

        self::assertSame('missing-build/src/main.js', $manifest->get('src/main.js'));
    }

    public function testGetFallsBackToPathWhenManifestJsonIsInvalid(): void
    {
        $this->writeManifest('not-valid-json{{{');

        $manifest = new Manifest($this->publicDir, 'build');

        self::assertSame('build/src/main.js', $manifest->get('src/main.js'));
    }

    public function testManifestIsLoadedOnlyOnce(): void
    {
        $this->writeManifest(['src/main.js' => ['file' => 'assets/main-abc123.js']]);

        $manifest = new Manifest($this->publicDir, 'build');
        $manifest->get('src/main.js');

        $this->writeManifest(['src/main.js' => ['file' => 'assets/main-other.js']]);

        self::assertSame('build/assets/main-abc123.js', $manifest->get('src/main.js'));
    }
}
