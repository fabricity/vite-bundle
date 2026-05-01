<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Tests\Unit\Asset;

use Fabricity\Bundle\ViteBundle\Asset\ViteVersionStrategy;
use Fabricity\Bundle\ViteBundle\Vite\DevServer;
use Fabricity\Bundle\ViteBundle\Vite\Manifest;
use PHPUnit\Framework\TestCase;

class ViteVersionStrategyTest extends TestCase
{
    public function testApplyVersionUsesManifestWhenNoDevServer(): void
    {
        $manifest = $this->createMock(Manifest::class);
        $manifest->expects(self::once())
            ->method('get')
            ->with('src/main.js')
            ->willReturn('build/assets/main-abc123.js');

        $strategy = new ViteVersionStrategy($manifest);

        self::assertSame('build/assets/main-abc123.js', $strategy->applyVersion('src/main.js'));
    }

    public function testApplyVersionUsesDevServerUrlForJsWhenAvailable(): void
    {
        $manifest = $this->createMock(Manifest::class);
        $manifest->expects(self::never())->method('get');

        $server = $this->createStub(DevServer::class);
        $server->method('available')->willReturn(true);
        $server->method('assetUrl')->with('src/main.js')->willReturn('http://localhost:5173/src/main.js');

        $strategy = new ViteVersionStrategy($manifest, $server);

        self::assertSame('http://localhost:5173/src/main.js', $strategy->applyVersion('src/main.js'));
    }

    public function testApplyVersionUsesManifestForCssEvenWhenDevServerAvailable(): void
    {
        $manifest = $this->createMock(Manifest::class);
        $manifest->expects(self::once())
            ->method('get')
            ->with('src/main.css')
            ->willReturn('build/assets/main-abc123.css');

        $server = $this->createMock(DevServer::class);
        $server->expects(self::never())->method('available');

        $strategy = new ViteVersionStrategy($manifest, $server);

        self::assertSame('build/assets/main-abc123.css', $strategy->applyVersion('src/main.css'));
    }

    public function testApplyVersionUsesManifestWhenDevServerUnavailable(): void
    {
        $manifest = $this->createMock(Manifest::class);
        $manifest->expects(self::once())
            ->method('get')
            ->with('src/main.js')
            ->willReturn('build/assets/main-abc123.js');

        $server = $this->createStub(DevServer::class);
        $server->method('available')->willReturn(false);

        $strategy = new ViteVersionStrategy($manifest, $server);

        self::assertSame('build/assets/main-abc123.js', $strategy->applyVersion('src/main.js'));
    }

    public function testGetVersionDelegatesToApplyVersion(): void
    {
        $manifest = $this->createStub(Manifest::class);
        $manifest->method('get')->willReturn('build/assets/main-abc123.js');

        $strategy = new ViteVersionStrategy($manifest);

        self::assertSame($strategy->applyVersion('src/main.js'), $strategy->getVersion('src/main.js'));
    }
}
