<?php

namespace HalilCosdu\Ollama\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;

trait MakesHttpRequests
{
    /**
     * @throws GuzzleException
     */
    protected function request(string $urlSuffix, array $data, string $method = 'post')
    {
        $url = config('ollama.url').$urlSuffix;

        if (! empty($data['stream']) && $data['stream'] === true) {
            return $this->streamingClient()->request($method, $url, [
                'json' => $data,
                'stream' => true,
                'timeout' => config('ollama.connection.timeout'),
            ]);
        } else {
            $response = Http::timeout(config('ollama.connection.timeout'))->$method($url, $data);

            return $response->json();
        }
    }

    /**
     * Resolve the Guzzle client used for streaming responses. Bound into the
     * container under `GuzzleHttp\ClientInterface` so tests can substitute a
     * client backed by a MockHandler; falls back to a fresh client otherwise.
     */
    protected function streamingClient(): ClientInterface
    {
        if ($this->getContainer()->bound(ClientInterface::class)) {
            return $this->getContainer()->make(ClientInterface::class);
        }

        return new Client;
    }

    /**
     * Resolve the application container. Extracted so it can be mocked in tests.
     */
    protected function getContainer()
    {
        return app();
    }
}
