<?php

namespace HalilCosdu\Ollama;

use Exception;
use Generator;
use HalilCosdu\Ollama\Services\OllamaService;
use HalilCosdu\Ollama\Traits\MakesHttpRequests;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Ollama implements Arrayable, Jsonable
{
    use MakesHttpRequests;

    protected OllamaService $ollamaService;

    protected string $selectedModel;

    protected string $model;

    protected string $prompt = '';

    protected string|array $format = 'json';

    protected array $options = [];

    protected bool $stream = false;

    protected bool $raw = false;

    protected string $agent = '';

    protected ?string $image = null;

    protected string $keepAlive = '5m';

    protected array $tools = [];

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
        $this->setBaseModel();
    }

    protected function setBaseModel(): void
    {
        $this->model = config('ollama.model');
        $this->selectedModel = config('ollama.model');
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

    public function getModel(): string
    {
        return $this->model;
    }

    public function format(string|array $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat(): string|array
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

    /**
     * @deprecated Use embed() instead — this calls the deprecated
     *             POST /api/embeddings endpoint. Will be removed in v2.0.
     */
    public function embeddings(string $prompt)
    {
        return $this->ollamaService->generateEmbeddings($this->selectedModel, $prompt);
    }

    /**
     * Generate embeddings using the newer POST /api/embed endpoint.
     * Prefer this over embeddings(), which targets the deprecated
     * /api/embeddings endpoint.
     *
     * @param  string|array<string>  $input
     */
    public function embed(string|array $input)
    {
        return $this->ollamaService->createEmbed($this->selectedModel, $input);
    }

    /**
     * List models currently loaded into memory (running models).
     */
    public function ps()
    {
        return $this->ollamaService->listRunningModels();
    }

    /**
     * Get the running Ollama server version.
     */
    public function version()
    {
        return $this->ollamaService->version();
    }

    /**
     * Push the selected model to a registry.
     */
    public function push(): static
    {
        $this->ollamaService->pushModel($this->selectedModel);

        return $this;
    }

    /**
     * Create a model from a Modelfile.
     */
    public function create(string $modelfile): static
    {
        $this->ollamaService->createModel($this->selectedModel, $modelfile);

        return $this;
    }

    public function getKeepAlive(): string
    {
        return $this->keepAlive;
    }

    public function keepAlive(string $keepAlive): static
    {
        $this->keepAlive = $keepAlive;

        return $this;
    }

    public function tools(array $tools): static
    {
        $this->tools = $tools;

        return $this;
    }

    public function getTools(): array
    {
        return $this->tools;
    }

    public function ask()
    {
        return $this->request('/api/generate', $this->generatePayload());
    }

    /** @return Generator<int, array<string, mixed>> */
    public function streamAsk(): Generator
    {
        return $this->streamRequest('/api/generate', $this->generatePayload());
    }

    public function chat(array $conversation)
    {
        return $this->request('/api/chat', $this->chatPayload($conversation));
    }

    /**
     * Stream decoded chat chunks as they arrive from Ollama.
     *
     * @param  array<int, array<string, mixed>>  $conversation
     * @return Generator<int, array<string, mixed>>
     */
    public function streamChat(array $conversation): Generator
    {
        return $this->streamRequest('/api/chat', $this->chatPayload($conversation));
    }

    /** @return array<string, mixed> */
    private function generatePayload(): array
    {
        $data = [
            'model' => $this->getModel(),
            'system' => $this->getAgent(),
            'prompt' => $this->getPrompt(),
            'format' => $this->getFormat(),
            'options' => $this->getOptions(),
            'stream' => $this->getStream(),
            'raw' => $this->getRaw(),
            'keep_alive' => $this->getKeepAlive(),
        ];

        if ($this->image) {
            $data['images'] = [$this->getImage()];
        }

        return $data;
    }

    /** @param array<int, array<string, mixed>> $conversation
     * @return array<string, mixed>
     */
    private function chatPayload(array $conversation): array
    {
        $payload = [
            'model' => $this->getModel(),
            'messages' => $conversation,
            'format' => $this->getFormat(),
            'options' => $this->getOptions(),
            'stream' => $this->getStream(),
            'keep_alive' => $this->getKeepAlive(),
        ];

        if (! empty($this->tools)) {
            $payload['tools'] = $this->getTools();
        }

        return $payload;
    }

    public function toArray(): array
    {
        return [
            'model' => $this->getModel(),
            'prompt' => $this->getPrompt(),
            'format' => $this->getFormat(),
            'options' => $this->getOptions(),
            'stream' => $this->getStream(),
            'raw' => $this->getRaw(),
            'keep_alive' => $this->getKeepAlive(),
        ];
    }

    public function toJson($options = 0): false|string
    {
        return json_encode($this->toArray(), $options);
    }
}
