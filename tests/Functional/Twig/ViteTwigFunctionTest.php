<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Tests\Functional\Twig;

use Fabricity\Bundle\ViteBundle\Tests\Functional\FunctionalTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ViteTwigFunctionTest extends KernelTestCase
{
    use FunctionalTrait;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    #[DataProvider('availabilityProvider')]
    public function testViteDevAvailability(array|callable $responses, string $expected): void
    {
        $template = '{{ vite.server ? "running" : "not running" }}';

        static::getContainer()->set(HttpClientInterface::class, new MockHttpClient($responses));
        self::assertSame($expected, $this->renderTemplate($template));
    }

    #[DataProvider('assetsProvider')]
    public function testAssets(string $package, string $path, string $expected): void
    {
        $template = \sprintf('{{ asset("%s", "%s") }}', $path, $package);

        self::assertSame($expected, $this->renderTemplate($template));
    }

    public static function availabilityProvider(): iterable
    {
        yield 'running' => [
            [new MockResponse(info: ['http_code' => 200])],
            'running',
        ];

        yield 'not running (404)' => [
            [new MockResponse(info: ['http_code' => 404])],
            'not running',
        ];

        yield 'error' => [
            static fn () => throw new TransportException('Connection failed'),
            'not running',
        ];
    }
}
