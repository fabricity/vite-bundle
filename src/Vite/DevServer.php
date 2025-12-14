<?php

declare(strict_types=1);

namespace Fabricity\Bundle\ViteBundle\Vite;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DevServer
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $devServerUrl,
    ) {
    }

    public function available(): bool
    {
        try {
            $response = $this->httpClient->request('GET', $this->devServerUrl.'/@vite/client', ['timeout' => 2]);

            return Response::HTTP_OK === $response->getStatusCode();
        } catch (\Throwable) {
            return false;
        }
    }
}
