# Laravel Ollama

[![Latest Version on Packagist](https://img.shields.io/packagist/v/halilcosdu/laravel-ollama.svg?style=flat-square)](https://packagist.org/packages/halilcosdu/laravel-ollama)
[![Total Downloads](https://img.shields.io/packagist/dt/halilcosdu/laravel-ollama.svg?style=flat-square)](https://packagist.org/packages/halilcosdu/laravel-ollama)

A fluent, Laravel-native client for running AI locally with [Ollama](https://ollama.com). Generate text, build conversations, stream tokens without buffering, enforce JSON schemas, call tools, create embeddings, use vision models, and manage the models on your Ollama server.

```php
use HalilCosdu\Ollama\Facades\Ollama;

$answer = Ollama::model('gemma3')
    ->agent('You are a concise Laravel expert.')
    ->prompt('Explain service containers in one sentence.')
    ->stream(false)
    ->ask();
```

## Why Laravel Ollama?

- Fluent facade that feels natural in Laravel applications.
- First-class, memory-efficient NDJSON streaming through PHP generators.
- Structured outputs with JSON Schema for reliable application data.
- Generate, chat, vision, tool calling, embeddings, and model management.
- Hermetic test suite: normal tests never require a running Ollama instance.
- Tested across Laravel 11, 12, and 13, including PHP 8.5.

## Requirements

| Package | Supported versions |
| --- | --- |
| PHP | 8.2–8.5 |
| Laravel | 11.x, 12.x, 13.x |
| Ollama | A reachable local or remote Ollama server |

Laravel 13 requires PHP 8.3 or newer.

## Installation

Install the package:

```bash
composer require halilcosdu/laravel-ollama
```

Laravel discovers the service provider and `Ollama` facade automatically. Optionally publish the configuration:

```bash
php artisan vendor:publish --tag=ollama-config
```

Configure your application in `.env`:

```dotenv
OLLAMA_URL=http://127.0.0.1:11434
OLLAMA_MODEL=gemma3
OLLAMA_DEFAULT_PROMPT="Hello, how can I assist you today?"
OLLAMA_CONNECTION_TIMEOUT=30
```

## Text generation

`ask()` returns the decoded Ollama response as an array when streaming is disabled:

```php
$response = Ollama::model('gemma3')
    ->agent('Answer as a senior PHP engineer.')
    ->prompt('When should I use a readonly class?')
    ->options(['temperature' => 0.2])
    ->keepAlive('10m')
    ->stream(false)
    ->ask();

$text = $response['response'];
```

Available generation controls include `agent()`, `prompt()`, `model()`, `format()`, `options()`, `raw()`, `stream()`, and `keepAlive()`.

## Token streaming

`streamAsk()` and `streamChat()` open an Ollama NDJSON stream and lazily yield each decoded chunk. The complete response is never buffered in memory, making this suitable for long answers, console commands, queues, and streamed HTTP responses.

```php
foreach (Ollama::model('gemma3')->prompt('Write a short story.')->streamAsk() as $chunk) {
    echo $chunk['response'] ?? '';
}
```

Stream chat content:

```php
$messages = [['role' => 'user', 'content' => 'Teach me about Laravel queues.']];

foreach (Ollama::model('gemma3')->streamChat($messages) as $chunk) {
    echo data_get($chunk, 'message.content', '');
}
```

The generator throws `HalilCosdu\Ollama\Exceptions\OllamaStreamException` for malformed chunks and for errors reported in the stream. Because generators are lazy, the HTTP request begins when iteration starts.

### Stream directly from a Laravel route

```php
use HalilCosdu\Ollama\Facades\Ollama;

Route::get('/explain', function () {
    return response()->stream(function () {
        foreach (Ollama::prompt('Explain dependency injection.')->streamAsk() as $chunk) {
            echo $chunk['response'] ?? '';
            ob_flush();
            flush();
        }
    }, headers: ['Content-Type' => 'text/plain; charset=UTF-8']);
});
```

## Chat

```php
$response = Ollama::model('gemma3')->stream(false)->chat([
    ['role' => 'system', 'content' => 'You are a helpful Laravel mentor.'],
    ['role' => 'user', 'content' => 'What is route model binding?'],
]);

$text = $response['message']['content'];
```

For vision chat, place base64-encoded image data in the relevant message's `images` array. The `image()` convenience method applies to text generation through `ask()`.

## Structured outputs

Pass `json` or an entire JSON Schema to `format()`. Use `stream(false)` for a single, easily validated JSON response and a low temperature for consistency.

```php
$schema = [
    'type' => 'object',
    'properties' => [
        'name' => ['type' => 'string'],
        'frameworks' => ['type' => 'array', 'items' => ['type' => 'string']],
    ],
    'required' => ['name', 'frameworks'],
];

$response = Ollama::model('gemma3')
    ->format($schema)
    ->options(['temperature' => 0])
    ->prompt('Describe the PHP ecosystem.')
    ->stream(false)
    ->ask();

$data = json_decode($response['response'], true, flags: JSON_THROW_ON_ERROR);
```

## Tool calling

```php
$tools = [[
    'type' => 'function',
    'function' => [
        'name' => 'get_weather',
        'description' => 'Get the current weather for a city',
        'parameters' => [
            'type' => 'object',
            'properties' => ['city' => ['type' => 'string']],
            'required' => ['city'],
        ],
    ],
]];

$response = Ollama::model('qwen3')
    ->tools($tools)
    ->stream(false)
    ->chat([['role' => 'user', 'content' => 'What is the weather in Istanbul?']]);

$calls = data_get($response, 'message.tool_calls', []);
```

Your application remains responsible for validating arguments, executing approved tools, and returning tool results to the conversation.

## Vision

```php
$response = Ollama::model('gemma3')
    ->prompt('Describe this image.')
    ->image(storage_path('app/photo.jpg'))
    ->stream(false)
    ->ask();
```

`image()` validates the path and sends the file as base64 data.

## Embeddings

```php
$one = Ollama::model('nomic-embed-text')->embed('Laravel is expressive.');
$batch = Ollama::model('nomic-embed-text')->embed(['first document', 'second document']);
```

`embeddings()` targets Ollama's deprecated `/api/embeddings` endpoint and remains available only for backward compatibility. Prefer `embed()`.

## Model management and server information

```php
$localModels = Ollama::models();
$runningModels = Ollama::ps();
$serverVersion = Ollama::version();
$details = Ollama::model('gemma3')->show();

Ollama::model('gemma3')->pull();
Ollama::model('gemma3')->copy('gemma3-backup');
Ollama::model('gemma3-backup')->delete();
Ollama::model('my-model')->create("FROM gemma3\nSYSTEM You are concise.");
Ollama::model('myorg/my-model')->push();
```

## API reference

| Method | Ollama endpoint | Result |
| --- | --- | --- |
| `ask()` | `POST /api/generate` | Array, or raw Guzzle response with `stream(true)` |
| `streamAsk()` | `POST /api/generate` | Generator yielding decoded chunks |
| `chat($messages)` | `POST /api/chat` | Array, or raw Guzzle response with `stream(true)` |
| `streamChat($messages)` | `POST /api/chat` | Generator yielding decoded chunks |
| `embed($input)` | `POST /api/embed` | Embedding response array |
| `models()` | `GET /api/tags` | Local models |
| `ps()` | `GET /api/ps` | Running models |
| `version()` | `GET /api/version` | Server version |
| `show()` | `POST /api/show` | Model details |
| `pull()` / `push()` | `POST /api/pull`, `/api/push` | Fluent instance |
| `create($modelfile)` | `POST /api/create` | Fluent instance |
| `copy($destination)` | `POST /api/copy` | Fluent instance |
| `delete()` | `DELETE /api/delete` | Fluent instance |

## Testing and quality

```bash
composer test
composer analyse
composer format
```

The default suite uses Laravel HTTP fakes and Guzzle mock streams, so it is deterministic and makes no network calls. To run the optional live smoke tests:

```bash
OLLAMA_INTEGRATION=1 OLLAMA_MODEL=gemma3 composer test -- --group=integration
```

## Changelog, contributing, and security

See [CHANGELOG.md](CHANGELOG.md) for release history. Contributions and focused bug reports are welcome through GitHub. Please report security vulnerabilities privately through the repository's [security advisory page](https://github.com/halilcosdu/laravel-ollama/security/advisories/new).

## Credits

- [Halil Cosdu](https://github.com/halilcosdu)
- Inspired by the original Ollama Laravel work from [Cloud Studio](https://github.com/cloudstudio/ollama-laravel)

## License

Laravel Ollama is open-source software licensed under the [MIT license](LICENSE.md).
