<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Tests\Functional\Twig;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

class ViteDevTwigFunctionTest extends KernelTestCase
{
    #[DataProvider('availabilityProvider')]
    public function testViteDevAvailability(array|callable $responses, string $expected): void {
        self::bootKernel();

        static::getContainer()->set(HttpClientInterface::class, new MockHttpClient($responses));

        /** @var Environment $twig */
        $twig = static::getContainer()->get(Environment::class);

        $output = $twig
            ->createTemplate('{{ vite_dev() ? "running" : "not running" }}')
            ->render();

        self::assertSame($expected, $output);
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
