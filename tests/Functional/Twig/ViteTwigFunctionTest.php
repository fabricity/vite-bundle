<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Tests\Functional\Twig;

use Fabricity\Bundle\ViteBundle\Tests\Functional\ContainerTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ViteTwigFunctionTest extends KernelTestCase
{
    use ContainerTrait;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    #[DataProvider('availabilityProvider')]
    public function testViteDevAvailability(array|callable $responses, string $expected): void
    {
        static::getContainer()->set(HttpClientInterface::class, new MockHttpClient($responses));

        self::assertSame(
            expected: $expected,
            actual: $this->renderTemplate('{{ vite.server ? "running" : "not running" }}')
        );
    }

    public function testTwigAsset(): void
    {
        self::assertSame(
            expected: '/build/assets/main-abc123.js',
            actual: $this->renderTemplate('{{ asset("src/main.js", "frontend") }}')
        );
        self::assertSame(
            expected: '/backend/assets/main-xyz789.js',
            actual: $this->renderTemplate('{{ asset("src/main.js", "backend") }}')
        );
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
