<?php

namespace HalilCosdu\Ollama\Services;

use HalilCosdu\Ollama\Traits\MakesHttpRequests;

class OllamaService
{
    use MakesHttpRequests;

    protected string $baseUrl;

    public function __construct()
    {
        $this->setBaseUrl();
    }

    protected function setBaseUrl(): void
    {
        $this->baseUrl = config('ollama.url');
    }

    public function listLocalModels()
    {
        return $this->request('/api/tags', [], 'get');
    }

    public function showModelInformation(string $modelName)
    {
        return $this->request('/api/show', ['name' => $modelName]);
    }

    public function copyModel(string $source, string $destination)
    {
        return $this->request('/api/copy', [
            'source' => $source,
            'destination' => $destination,
        ]);
    }

    public function deleteModel(string $modelName)
    {
        return $this->request('/api/delete', ['name' => $modelName], 'delete');
    }

    public function pullModel(string $modelName)
    {
        return $this->request('/api/pull', ['name' => $modelName]);
    }

    public function generateEmbeddings(string $modelName, string $prompt)
    {
        return $this->request('/api/embeddings', [
            'model' => $modelName,
            'prompt' => $prompt,
        ]);
    }
}
