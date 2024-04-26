<?php

use HalilCosdu\Ollama\Ollama;
use HalilCosdu\Ollama\Services\OllamaService;

beforeEach(function () {
    $this->ollama = new Ollama(new OllamaService);
});

it('sets properties correctly and returns instance', function ($method, $value) {
    expect($this->ollama->$method($value))->toBeInstanceOf(Ollama::class);
})->with([
    'agent' => ['agent', 'Act as Bill Gates'],
    'prompt' => ['prompt', 'Who are you?'],
    'model' => ['model', 'llama3'],
    'format' => ['format', 'json'],
    'options' => ['options', ['temperature' => 0.7]],
    'stream' => ['stream', true],
    'raw' => ['raw', true],
]);

it('correctly processes ask method with real API call', function () {
    $response = $this->ollama->agent('You are a weather expert...')
        ->prompt('Why is the sky blue? answer only in 4 words')
        ->model('llama3')
        ->options(['temperature' => 0.8])
        ->stream(false)
        ->ask();

    expect($response)->toBeArray();
});

it('lists available local models', function () {
    $models = $this->ollama->models();
    expect($models)->toBeArray();
});

it('shows information about the selected model', function () {
    $models = $this->ollama->models();
    $model = $models['models'][0];
    $this->ollama->model($model['name']);
    $info = $this->ollama->show();
    expect($info)->toBeArray();
});
