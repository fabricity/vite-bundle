<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Vite;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DevServer
{
    public readonly string $clientUrl;

    private readonly string $url;
    private ?bool $available = null;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        string $url,
    ) {
        $this->url = rtrim($url, '/');
        $this->clientUrl = $this->url.'/@vite/client';
    }

    public function available(): bool
    {
        if (null !== $this->available) {
            return $this->available;
        }

        try {
            $response = $this->httpClient->request('GET', $this->clientUrl, ['timeout' => 2]);

            return $this->available = Response::HTTP_OK === $response->getStatusCode();
        } catch (\Throwable) {
            return $this->available = false;
        }
    }

    public function assetUrl(string $path): string
    {
        return $this->url.'/'.ltrim($path, '/');
    }
}
