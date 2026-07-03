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

    /*
     * https://github.com/ollama/ollama/blob/main/docs/api.md#generate-embeddings
     * POST /api/embed — replaces the deprecated /api/embeddings; accepts a
     * single string or an array of strings as input.
     */
    public function createEmbed(string $modelName, string|array $input)
    {
        return $this->request('/api/embed', [
            'model' => $modelName,
            'input' => $input,
        ]);
    }

    /*
     * https://github.com/ollama/ollama/blob/main/docs/api.md#list-running-models
     * GET /api/ps — lists models currently loaded into memory.
     */
    public function listRunningModels()
    {
        return $this->request('/api/ps', [], 'get');
    }

    /*
     * GET /api/version — returns the running Ollama server version.
     */
    public function version()
    {
        return $this->request('/api/version', [], 'get');
    }

    /*
     * https://github.com/ollama/ollama/blob/main/docs/api.md#push-a-model
     * POST /api/push — push a model to a registry.
     */
    public function pushModel(string $modelName)
    {
        return $this->request('/api/push', ['name' => $modelName]);
    }

    /*
     * https://github.com/ollama/ollama/blob/main/docs/api.md#create-a-model
     * POST /api/create — create a model from a Modelfile.
     */
    public function createModel(string $modelName, string $modelfile)
    {
        return $this->request('/api/create', [
            'name' => $modelName,
            'modelfile' => $modelfile,
        ]);
    }
}
