<?php

namespace HalilCosdu\Ollama;

use Exception;
use HalilCosdu\Ollama\Services\OllamaService;
use HalilCosdu\Ollama\Traits\MakesHttpRequests;

class Ollama
{
    use MakesHttpRequests;

    protected OllamaService $ollamaService;

    protected string $selectedModel;

    protected string $model;

    protected string $prompt;

    protected string $format = 'json';

    protected array $options;

    protected bool $stream = false;

    protected bool $raw = false;

    protected string $agent;

    protected ?string $image = null;

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
        $this->setBaseModel();
    }

    protected function setBaseModel(): void
    {
        $this->model = config('ollama.model');
    }

    public function agent(string $agent): static
    {
        $this->agent = $agent;

        return $this;
    }

    public function getAgent(): string
    {
        return $this->agent;
    }

    public function prompt(string $prompt): static
    {
        $this->prompt = $prompt;

        return $this;
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function model(string $model): static
    {
        $this->selectedModel = $model;
        $this->model = $model;

        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function options(array $options = []): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function stream(bool $stream = false): static
    {
        $this->stream = $stream;

        return $this;
    }

    public function getStream(): bool
    {
        return $this->stream;
    }

    public function raw(bool $raw): static
    {
        $this->raw = $raw;

        return $this;
    }

    public function getRaw(): bool
    {
        return $this->raw;
    }

    public function models(): array
    {
        return $this->ollamaService->listLocalModels();
    }

    public function show()
    {
        return $this->ollamaService->showModelInformation($this->selectedModel);
    }

    public function copy(string $destination): static
    {
        $this->ollamaService->copyModel($this->selectedModel, $destination);

        return $this;
    }

    public function delete(): static
    {
        $this->ollamaService->deleteModel($this->selectedModel);

        return $this;
    }

    public function pull(): static
    {
        $this->ollamaService->pullModel($this->selectedModel);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function image(string $imagePath): static
    {
        if (! file_exists($imagePath)) {
            throw new Exception("Image file does not exist: $imagePath");
        }

        $this->image = base64_encode(file_get_contents($imagePath));

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function embeddings(string $prompt)
    {
        return $this->ollamaService->generateEmbeddings($this->selectedModel, $prompt);
    }

    public function ask()
    {
        $data = [
            'model' => $this->getModel(),
            'system' => $this->getAgent(),
            'prompt' => $this->getPrompt(),
            'format' => $this->getFormat(),
            'options' => $this->getOptions(),
            'stream' => $this->getStream(),
            'raw' => $this->getRaw(),
        ];

        if ($this->image) {
            $data['images'] = [$this->getImage()];
        }

        return $this->sendRequest('/api/generate', $data);
    }

    public function chat(array $conversation)
    {
        return $this->sendRequest('/api/chat', [
            'model' => $this->getModel(),
            'messages' => $conversation,
            'format' => $this->getFormat(),
            'options' => $this->getOptions(),
            'stream' => $this->getStream(),
        ]);
    }
}
