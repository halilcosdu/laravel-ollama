<?php

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use HalilCosdu\Ollama\Exceptions\OllamaStreamException;
use HalilCosdu\Ollama\Ollama;
use HalilCosdu\Ollama\Services\OllamaService;
use Illuminate\Support\Facades\Http;

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

describe('generate (ask)', function () {
    it('POSTs to /api/generate with the fluent state', function () {
        Http::fake();

        $this->ollama->prompt('hi')->model('llama3')->stream(false)->ask();

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request->url() === 'http://127.0.0.1:11434/api/generate'
                && $request['model'] === 'llama3'
                && $request['prompt'] === 'hi'
                && $request['stream'] === false;
        });
    });

    it('forwards keep_alive to generate', function () {
        Http::fake();

        $this->ollama->prompt('hi')->model('llama3')->stream(false)->keepAlive('10m')->ask();

        Http::assertSent(fn ($request) => $request['keep_alive'] === '10m');
    });
});

describe('chat', function () {
    it('POSTs the conversation to /api/chat', function () {
        Http::fake();

        $this->ollama->model('llama3')->chat([
            ['role' => 'user', 'content' => 'hi'],
        ]);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request->url() === 'http://127.0.0.1:11434/api/chat'
                && $request['model'] === 'llama3'
                && $request['messages'][0]['content'] === 'hi';
        });
    });
});

describe('models', function () {
    it('GETs /api/tags', function () {
        Http::fake([
            '127.0.0.1:11434/api/tags' => Http::response(['models' => []]),
        ]);

        $this->ollama->models();

        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && $request->url() === 'http://127.0.0.1:11434/api/tags');
    });
});

describe('show', function () {
    it('POSTs the model name to /api/show', function () {
        Http::fake();

        $this->ollama->model('llama3')->show();

        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'http://127.0.0.1:11434/api/show'
            && $request['name'] === 'llama3');
    });
});

describe('copy', function () {
    it('POSTs source and destination to /api/copy', function () {
        Http::fake();

        $this->ollama->model('llama3')->copy('NewModel');

        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'http://127.0.0.1:11434/api/copy'
            && $request['source'] === 'llama3'
            && $request['destination'] === 'NewModel');
    });
});

describe('delete', function () {
    it('DELETEs the model at /api/delete', function () {
        Http::fake();

        $this->ollama->model('llama3')->delete();

        Http::assertSent(fn ($request) => $request->method() === 'DELETE'
            && $request->url() === 'http://127.0.0.1:11434/api/delete'
            && $request['name'] === 'llama3');
    });
});

describe('pull', function () {
    it('POSTs the model name to /api/pull', function () {
        Http::fake();

        $this->ollama->model('llama3')->pull();

        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'http://127.0.0.1:11434/api/pull'
            && $request['name'] === 'llama3');
    });
});

describe('embeddings', function () {
    it('POSTs to the deprecated /api/embeddings endpoint', function () {
        Http::fake();

        $this->ollama->model('llama3')->embeddings('hello');

        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'http://127.0.0.1:11434/api/embeddings'
            && $request['model'] === 'llama3'
            && $request['prompt'] === 'hello');
    });
});

describe('streaming', function () {
    it('uses the bound Guzzle client when streaming a generate request', function () {
        $container = [];
        $mock = new MockHandler([
            new Response(200, [], Utils::streamFor('{"response":"hi","done":true}'."\n")),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push(Middleware::history($container));

        $this->app->bind(ClientInterface::class, fn () => new Client(['handler' => $handler]));

        $this->ollama->prompt('hi')->model('llama3')->stream(true)->ask();

        expect($container)->toHaveCount(1);
        expect($container[0]['request']->getMethod())->toBe('POST');
        expect((string) $container[0]['request']->getUri())->toBe('http://127.0.0.1:11434/api/generate');
        expect($container[0]['request']->hasHeader('stream'))->toBeFalse();
    });
});

describe('decoded NDJSON streaming', function () {
    it('lazily yields decoded generate chunks', function () {
        $history = [];
        $mock = new MockHandler([
            new Response(200, [], Utils::streamFor("{\"response\":\"Hel\",\"done\":false}\n{\"response\":\"lo\",\"done\":true}\n")),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push(Middleware::history($history));
        $this->app->bind(ClientInterface::class, fn () => new Client(['handler' => $handler]));

        $chunks = iterator_to_array($this->ollama->model('llama3')->prompt('Hi')->streamAsk());

        expect($chunks)->toHaveCount(2)
            ->and($chunks[0]['response'])->toBe('Hel')
            ->and($chunks[1]['done'])->toBeTrue()
            ->and(json_decode((string) $history[0]['request']->getBody(), true)['stream'])->toBeTrue();
    });

    it('streams chat messages and preserves tools', function () {
        $history = [];
        $mock = new MockHandler([
            new Response(200, [], Utils::streamFor('{"message":{"content":"Hi"},"done":true}')),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push(Middleware::history($history));
        $this->app->bind(ClientInterface::class, fn () => new Client(['handler' => $handler]));

        $tools = [['type' => 'function', 'function' => ['name' => 'clock']]];
        $chunks = iterator_to_array($this->ollama->tools($tools)->streamChat([
            ['role' => 'user', 'content' => 'Hi'],
        ]));
        $payload = json_decode((string) $history[0]['request']->getBody(), true);

        expect($chunks[0]['message']['content'])->toBe('Hi')
            ->and($payload['tools'])->toBe($tools)
            ->and($payload['stream'])->toBeTrue();
    });

    it('raises a meaningful exception for stream errors', function () {
        $mock = new MockHandler([
            new Response(200, [], Utils::streamFor("{\"error\":\"model not found\"}\n")),
        ]);
        $this->app->bind(ClientInterface::class, fn () => new Client([
            'handler' => HandlerStack::create($mock),
        ]));

        iterator_to_array($this->ollama->streamAsk());
    })->throws(OllamaStreamException::class, 'model not found');

    it('rejects malformed stream chunks', function () {
        $mock = new MockHandler([new Response(200, [], Utils::streamFor("not-json\n"))]);
        $this->app->bind(ClientInterface::class, fn () => new Client([
            'handler' => HandlerStack::create($mock),
        ]));

        iterator_to_array($this->ollama->streamAsk());
    })->throws(OllamaStreamException::class, 'invalid NDJSON');
});

it('accepts a JSON schema as structured output format', function () {
    Http::fake();
    $schema = ['type' => 'object', 'properties' => ['name' => ['type' => 'string']]];

    $this->ollama->format($schema)->stream(false)->prompt('Name a city')->ask();

    Http::assertSent(fn ($request) => $request['format'] === $schema);
});
