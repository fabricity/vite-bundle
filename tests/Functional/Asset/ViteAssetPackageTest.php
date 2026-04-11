<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Tests\Functional\Asset;

use Fabricity\Bundle\ViteBundle\Tests\Functional\FunctionalTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ViteAssetPackageTest extends KernelTestCase
{
    use FunctionalTrait;

    public function testPackages(): void
    {
        $packages = $this->getPackages();
        $this->assertNotNull($packages->getPackage('frontend'));
        $this->assertNotNull($packages->getPackage('backend'));
    }

    #[DataProvider('assetsProvider')]
    public function testAssets(string $package, string $path, string $expected): void
    {
        $package = $this->getPackages()->getPackage($package);

        $this->assertSame($expected, $package->getUrl($path));
    }
}
