<?php

namespace HalilCosdu\Ollama\Traits;

use Illuminate\Support\Facades\Http;

trait MakesHttpRequests
{
    protected function request(string $urlSuffix, array $data, string $method = 'post')
    {
        $ollamaUrl = config('ollama.url').$urlSuffix;
        $timeout = config('ollama.connection.timeout');

        $options = [
            'json' => $data,
            'timeout' => $timeout,
        ];

        if (! empty($data['stream']) && $data['stream'] === true) {
            $options['stream'] = true;
        }

        $response = Http::timeout($timeout)->$method($ollamaUrl, $options);

        return ! empty($data['stream']) && $data['stream'] === true ? $response : $response->json();
    }
}
