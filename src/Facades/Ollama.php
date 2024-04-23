<?php

namespace HalilCosdu\Ollama\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HalilCosdu\Ollama\Ollama
 *
 * @method static \HalilCosdu\Ollama\Ollama agent(string $agent): static
 * @method static \HalilCosdu\Ollama\Ollama getAgent(): string
 * @method static \HalilCosdu\Ollama\Ollama prompt(string $prompt): static
 * @method static \HalilCosdu\Ollama\Ollama getPrompt(): string
 * @method static \HalilCosdu\Ollama\Ollama model(string $model): static
 * @method static \HalilCosdu\Ollama\Ollama getModel()
 * @method static \HalilCosdu\Ollama\Ollama format(string $format): static
 * @method static \HalilCosdu\Ollama\Ollama getFormat(): string
 * @method static \HalilCosdu\Ollama\Ollama options(array $options = []): static
 * @method static \HalilCosdu\Ollama\Ollama getOptions(): array
 * @method static \HalilCosdu\Ollama\Ollama stream(bool $stream = false): static
 * @method static \HalilCosdu\Ollama\Ollama getStream(): bool
 * @method static \HalilCosdu\Ollama\Ollama raw(bool $raw): static
 * @method static \HalilCosdu\Ollama\Ollama getRaw(): bool
 * @method static \HalilCosdu\Ollama\Ollama models(): array
 * @method static \HalilCosdu\Ollama\Ollama show()
 * @method static \HalilCosdu\Ollama\Ollama copy(string $destination): static
 * @method static \HalilCosdu\Ollama\Ollama delete(): static
 * @method static \HalilCosdu\Ollama\Ollama pull(): static
 * @method static \HalilCosdu\Ollama\Ollama image(string $imagePath): static
 * @method static \HalilCosdu\Ollama\Ollama getImage(): ?string
 * @method static \HalilCosdu\Ollama\Ollama embeddings(string $prompt)
 * @method static \HalilCosdu\Ollama\Ollama ask()
 * @method static \HalilCosdu\Ollama\Ollama chat(array $conversation)
 * @method static \HalilCosdu\Ollama\Ollama getKeepAlive(): string
 * @method static \HalilCosdu\Ollama\Ollama keepAlive(string $keepAlive): static
 *
 **/
class Ollama extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \HalilCosdu\Ollama\Ollama::class;
    }
}
