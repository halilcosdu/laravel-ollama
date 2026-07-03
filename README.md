## Laravel Ollama

[![Latest Version on Packagist](https://img.shields.io/packagist/v/halilcosdu/laravel-ollama.svg?style=flat-square)](https://packagist.org/packages/halilcosdu/laravel-ollama)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/halilcosdu/laravel-ollama/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/halilcosdu/laravel-ollama/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/halilcosdu/laravel-ollama/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/halilcosdu/laravel-ollama/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/halilcosdu/laravel-ollama.svg?style=flat-square)](https://packagist.org/packages/halilcosdu/laravel-ollama)

Laravel Ollama is a PHP package that provides a simple and intuitive interface for interacting with the Ollama API. It is designed to be used with Laravel, a popular PHP framework, but can also be used in any PHP application.  This package provides a set of methods for making requests to the Ollama API, including methods for setting the agent, prompt, model, format, options, and more. It also includes methods for handling responses from the API, such as retrieving the response in a specific format or streaming the response.  With Laravel Ollama, you can easily integrate the Ollama API into your Laravel application and start making requests in a matter of minutes.  
Features
- Easy configuration
- Fluent interface: chain methods together to build your requests.
- Flexible
- Covers the Ollama API endpoints: generate, chat, models, show, copy, delete, pull, push, create, embed, ps, version, embeddings (deprecated).

> **Notes**
> - `embeddings()` is deprecated upstream (POST /api/embeddings). Prefer `embed()` (POST /api/embed) for new code.
> - For chat with images, include base64-encoded images inside the relevant message's `images` array (per the Ollama chat schema); the fluent `image()` setter only applies to `ask()` (generate).

This package builds upon the foundational work provided by the Ollama Laravel package developed by [Cloud Studio](https://github.com/cloudstudio/ollama-laravel). Special thanks to them for their innovative approach and contributions to the Laravel community.

★ In the near future, I will add more features to this package.

## Installation

You can install the package via composer:

```bash
composer require halilcosdu/laravel-ollama
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="ollama-config"
```

This is the contents of the published config file:

```php
return [
    'model' => env('OLLAMA_MODEL', 'llama3'),
    'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
    'default_prompt' => env('OLLAMA_DEFAULT_PROMPT', 'Hello world!'),
    'connection' => [
        'timeout' => env('OLLAMA_CONNECTION_TIMEOUT', 30),
    ],
];
```

## Usage
```php
@method static \HalilCosdu\Ollama\Ollama agent(string $agent): static
@method static \HalilCosdu\Ollama\Ollama getAgent(): string
@method static \HalilCosdu\Ollama\Ollama prompt(string $prompt): static
@method static \HalilCosdu\Ollama\Ollama getPrompt(): string
@method static \HalilCosdu\Ollama\Ollama model(string $model): static
@method static \HalilCosdu\Ollama\Ollama getModel()
@method static \HalilCosdu\Ollama\Ollama format(string $format): static
@method static \HalilCosdu\Ollama\Ollama getFormat(): string
@method static \HalilCosdu\Ollama\Ollama options(array $options = []): static
@method static \HalilCosdu\Ollama\Ollama getOptions(): array
@method static \HalilCosdu\Ollama\Ollama stream(bool $stream = false): static
@method static \HalilCosdu\Ollama\Ollama getStream(): bool
@method static \HalilCosdu\Ollama\Ollama raw(bool $raw): static
@method static \HalilCosdu\Ollama\Ollama getRaw(): bool
@method static \HalilCosdu\Ollama\Ollama models(): array
@method static \HalilCosdu\Ollama\Ollama show()
@method static \HalilCosdu\Ollama\Ollama copy(string $destination): static
@method static \HalilCosdu\Ollama\Ollama delete(): static
@method static \HalilCosdu\Ollama\Ollama pull(): static
@method static \HalilCosdu\Ollama\Ollama image(string $imagePath): static
@method static \HalilCosdu\Ollama\Ollama getImage(): ?string
@method static \HalilCosdu\Ollama\Ollama tools(array $tools): static
@method static \HalilCosdu\Ollama\Ollama getTools(): array
@method static \HalilCosdu\Ollama\Ollama embeddings(string $prompt)
@method static \HalilCosdu\Ollama\Ollama embed(string|array $input)
@method static \HalilCosdu\Ollama\Ollama ps()
@method static \HalilCosdu\Ollama\Ollama version()
@method static \HalilCosdu\Ollama\Ollama push(): static
@method static \HalilCosdu\Ollama\Ollama create(string $modelfile): static
@method static \HalilCosdu\Ollama\Ollama getKeepAlive(): string
@method static \HalilCosdu\Ollama\Ollama keepAlive(string $keepAlive): static
@method static \HalilCosdu\Ollama\Ollama ask()
@method static \HalilCosdu\Ollama\Ollama chat(array $conversation)
@method static \HalilCosdu\Ollama\Ollama toArray(): array
@method static \HalilCosdu\Ollama\Ollama toJson($options = 0): false|string


```
## Usage

### Basic Usage

```php
use HalilCosdu\Ollama\Facades\Ollama;

$response = Ollama::agent('You are a weather expert...')
    ->prompt('Why is the sky blue?')
    ->model('llama3')
    ->options(['temperature' => 0.8])
    ->stream(false)
    ->ask();
```


### Vision Support

```php
$response = Ollama::model('llava:13b')
    ->prompt('What is in this picture?')
    ->image(public_path('images/example.jpg')) 
    ->ask();

// "The image features a close-up of a person's hand, wearing bright pink fingernail polish and blue nail polish. In addition to the colorful nails, the hand has two tattoos – one is a cross and the other is an eye."

```

### Chat Completion

```php
$messages = [
    ['role' => 'user', 'content' => 'My name is Halil Cosdu and I live in Turkey'],
    ['role' => 'assistant', 'content' => 'Nice to meet you , Halil Cosdu'],
    ['role' => 'user', 'content' => 'where I live ?'],
];

$response = Ollama::agent('You know me really well!')
    ->model('llama3')
    ->chat($messages);

// "You mentioned that you live in Turkey."

```

### Show Model Information

```php
$response = Ollama::model('llama3')->show();
```

### Copy a Model

```php
Ollama::model('llama3')->copy('NewModel');
```

### Delete a Model

```php
Ollama::model('llama3')->delete();
```

### Generate Embeddings

```php
// New: /api/embed (preferred) — accepts a string or an array of inputs.
$embeddings = Ollama::model('llama3')->embed('Your prompt here');
$embeddings = Ollama::model('llama3')->embed(['first', 'second']);

// Deprecated: /api/embeddings (kept for backwards compatibility).
$embeddings = Ollama::model('llama3')->embeddings('Your prompt here');
```

### Tool Calling (Function Calling)

```php
$tools = [
    [
        'type' => 'function',
        'function' => [
            'name' => 'get_weather',
            'description' => 'Get the weather for a city',
            'parameters' => [
                'type' => 'object',
                'properties' => ['city' => ['type' => 'string']],
                'required' => ['city'],
            ],
        ],
    ],
];

$response = Ollama::model('llama3')
    ->tools($tools)
    ->chat([['role' => 'user', 'content' => 'Weather in Istanbul?']]);
```

### Running Models & Server Version

```php
Ollama::ps();       // GET /api/ps — models currently loaded into memory.
Ollama::version();  // GET /api/version — running Ollama server version.
```

### Push / Create a Model

```php
Ollama::model('myorg/mymodel')->push();
Ollama::model('my-model')->create("FROM llama3\nSYSTEM You are helpful.");
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Halil Cosdu](https://github.com/halilcosdu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
