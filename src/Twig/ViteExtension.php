<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ViteExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_dev', [ViteRuntime::class, 'dev'], ['is_safe' => ['html']]),
        ];
    }
}
