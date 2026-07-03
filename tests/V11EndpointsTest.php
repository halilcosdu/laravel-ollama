<?php

use HalilCosdu\Ollama\Ollama;
use HalilCosdu\Ollama\Services\OllamaService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::fake();
    $this->ollama = new Ollama(new OllamaService);
});

describe('embed', function () {
    it('POSTs a string input to /api/embed', function () {
        $this->ollama->model('llama3')->embed('hello');

        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'http://127.0.0.1:11434/api/embed'
            && $request['model'] === 'llama3'
            && $request['input'] === 'hello');
    });

    it('accepts an array of inputs', function () {
        $this->ollama->model('llama3')->embed(['hello', 'world']);

        Http::assertSent(fn ($request) => $request['input'] === ['hello', 'world']);
    });
});

describe('ps', function () {
    it('GETs /api/ps for running models', function () {
        Http::fake([
            '127.0.0.1:11434/api/ps' => Http::response(['models' => []]),
        ]);

        $this->ollama->ps();

        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && $request->url() === 'http://127.0.0.1:11434/api/ps');
    });
});

describe('version', function () {
    it('GETs /api/version', function () {
        Http::fake([
            '127.0.0.1:11434/api/version' => Http::response(['version' => '0.1.48']),
        ]);

        $this->ollama->version();

        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && $request->url() === 'http://127.0.0.1:11434/api/version');
    });
});

describe('push', function () {
    it('POSTs the model name to /api/push', function () {
        $this->ollama->model('llama3')->push();

        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'http://127.0.0.1:11434/api/push'
            && $request['name'] === 'llama3');
    });
});

describe('create', function () {
    it('POSTs the modelfile to /api/create', function () {
        $this->ollama->model('my-model')->create("FROM llama3\nSYSTEM You are helpful.");

        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'http://127.0.0.1:11434/api/create'
            && $request['name'] === 'my-model'
            && $request['modelfile'] === "FROM llama3\nSYSTEM You are helpful.");
    });
});

describe('tools (function calling)', function () {
    it('forwards tools to /api/chat when set', function () {
        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_weather',
                    'description' => 'Get the weather',
                    'parameters' => ['type' => 'object', 'properties' => []],
                ],
            ],
        ];

        $this->ollama->model('llama3')->tools($tools)->chat([
            ['role' => 'user', 'content' => 'weather?'],
        ]);

        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'http://127.0.0.1:11434/api/chat'
            && $request['tools'] === $tools);
    });

    it('omits the tools field when none are set', function () {
        $this->ollama->model('llama3')->chat([
            ['role' => 'user', 'content' => 'hi'],
        ]);

        Http::assertSent(fn ($request) => ! array_key_exists('tools', $request->body() ? json_decode($request->body(), true) : []));
    });
});

describe('chat keep_alive forwarding', function () {
    it('forwards keep_alive to /api/chat', function () {
        $this->ollama->model('llama3')->keepAlive('30m')->chat([
            ['role' => 'user', 'content' => 'hi'],
        ]);

        Http::assertSent(fn ($request) => $request['keep_alive'] === '30m');
    });
});
