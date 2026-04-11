<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Tests\Functional;

use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

trait HelperTrait
{
    abstract protected static function getContainer(): ContainerInterface;

    protected function getTwig(): Environment
    {
        return static::getContainer()->get(Environment::class);
    }

    protected function getPackages(): Packages
    {
        return static::getContainer()->get('assets.packages');
    }

    protected function renderTemplate(string $template, array $context = []): string
    {
        return $this->getTwig()->createTemplate($template)->render($context);
    }

    public static function assetsProvider(): array
    {
        return [
            ['backend', 'src/main.js', '/backend/assets/main-xyz789.js'],
            ['backend', 'src/main.css', '/backend/assets/main-xyz789.css'],
            ['frontend', 'src/main.js', '/frontend/assets/main-abc123.js'],
            ['frontend', 'src/main.css', '/frontend/assets/main-abc123.css'],
        ];
    }
}
