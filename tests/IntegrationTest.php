<?php

use HalilCosdu\Ollama\Ollama;
use HalilCosdu\Ollama\Services\OllamaService;

/**
 * Live integration tests against a real Ollama server.
 *
 * Skipped by default. To run them locally:
 *   1. Start Ollama and pull a model: `ollama pull llama3`
 *   2. Export OLLAMA_INTEGRATION=1 (and optionally OLLAMA_URL / OLLAMA_MODEL)
 *   3. `vendor/bin/pest --group=integration`
 *
 * These are excluded from the default CI test workflow.
 */
beforeEach(function () {
    if (! getenv('OLLAMA_INTEGRATION')) {
        $this->markTestSkipped('Set OLLAMA_INTEGRATION=1 to run live Ollama integration tests.');
    }

    $this->ollama = new Ollama(new OllamaService);
});

it('asks the model via a real Ollama server', function () {
    $response = $this->ollama
        ->agent('You are a weather expert...')
        ->prompt('Why is the sky blue? answer only in 4 words')
        ->model(getenv('OLLAMA_MODEL') ?: 'llama3')
        ->options(['temperature' => 0.8])
        ->stream(false)
        ->ask();

    expect($response)->toBeArray();
})->group('integration');

it('lists available local models from a real Ollama server', function () {
    $models = $this->ollama->models();

    expect($models)->toBeArray();
})->group('integration');
