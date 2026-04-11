<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Tests\Functional;

use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

trait ContainerTrait
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
}
