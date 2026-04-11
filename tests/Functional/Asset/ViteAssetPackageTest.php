<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Tests\Functional\Asset;

use Fabricity\Bundle\ViteBundle\Tests\Functional\ContainerTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ViteAssetPackageTest extends KernelTestCase
{
    use ContainerTrait;

    public function testFrontendPackages(): void
    {
        $packages = $this->getPackages();

        $frontend = $packages->getPackage('frontend');
        $this->assertNotNull($frontend);

        $this->assertSame(
            expected: '/build/assets/main-abc123.js',
            actual: $frontend->getUrl('src/main.js')
        );
    }

    public function testBackendPackages(): void
    {
        $packages = $this->getPackages();

        $package = $packages->getPackage('backend');
        $this->assertNotNull($package);

        $this->assertSame(
            expected: '/backend/assets/main-xyz789.js',
            actual: $package->getUrl('src/main.js')
        );
    }
}
