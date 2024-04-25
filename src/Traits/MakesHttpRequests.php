<?php

namespace HalilCosdu\Ollama\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;

trait MakesHttpRequests
{
    /**
     * @throws GuzzleException
     */
    protected function request(string $urlSuffix, array $data, string $method = 'post')
    {
        $url = config('ollama.url') . $urlSuffix;

        if (!empty($data['stream']) && $data['stream'] === true) {
            $client = new Client;
            return $client->request($method, $url, [
                'json' => $data,
                'stream' => true,
                'timeout' => config('ollama.connection.timeout'),
            ]);
        } else {
            $response = Http::timeout(config('ollama.connection.timeout'))->$method($url, $data);
            return $response->json();
        }
    }
}
