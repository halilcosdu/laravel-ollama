<?php

namespace HalilCosdu\Ollama\Traits;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use HalilCosdu\Ollama\Exceptions\OllamaStreamException;
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
     * Stream and decode Ollama's newline-delimited JSON response.
     *
     * @return Generator<int, array<string, mixed>>
     *
     * @throws GuzzleException
     * @throws OllamaStreamException
     */
    protected function streamRequest(string $urlSuffix, array $data): Generator
    {
        $data['stream'] = true;
        $response = $this->streamingClient()->request('post', config('ollama.url').$urlSuffix, [
            'json' => $data,
            'stream' => true,
            'timeout' => config('ollama.connection.timeout'),
        ]);

        $body = $response->getBody();
        $buffer = '';

        while (! $body->eof()) {
            $buffer .= $body->read(8192);

            while (($newline = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $newline);
                $buffer = substr($buffer, $newline + 1);

                if (($chunk = $this->decodeStreamChunk($line)) !== null) {
                    yield $chunk;
                }
            }
        }

        if (($chunk = $this->decodeStreamChunk($buffer)) !== null) {
            yield $chunk;
        }
    }

    /** @return array<string, mixed>|null */
    private function decodeStreamChunk(string $line): ?array
    {
        $line = trim($line);

        if ($line === '') {
            return null;
        }

        $chunk = json_decode($line, true);

        if (! is_array($chunk)) {
            throw new OllamaStreamException('Ollama returned an invalid NDJSON stream chunk.');
        }

        if (isset($chunk['error'])) {
            throw new OllamaStreamException((string) $chunk['error']);
        }

        return $chunk;
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
