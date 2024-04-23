## Laravel Ollama

[![Latest Version on Packagist](https://img.shields.io/packagist/v/halilcosdu/laravel-ollama.svg?style=flat-square)](https://packagist.org/packages/halilcosdu/laravel-ollama)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/halilcosdu/laravel-ollama/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/halilcosdu/laravel-ollama/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/halilcosdu/laravel-ollama/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/halilcosdu/laravel-ollama/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/halilcosdu/laravel-ollama.svg?style=flat-square)](https://packagist.org/packages/halilcosdu/laravel-ollama)

Laravel Ollama is a PHP package that provides a simple and intuitive interface for interacting with the Ollama API. It is designed to be used with Laravel, a popular PHP framework, but can also be used in any PHP application.  This package provides a set of methods for making requests to the Ollama API, including methods for setting the agent, prompt, model, format, options, and more. It also includes methods for handling responses from the API, such as retrieving the response in a specific format or streaming the response.  With Laravel Ollama, you can easily integrate the Ollama API into your Laravel application and start making requests in a matter of minutes.  
Features
Easy configuration
Fluent interface: Chain methods together to build your requests.
Flexible
Comprehensive: Covers all the endpoints of the Ollama API.

This package builds upon the foundational work provided by the Ollama Laravel package developed by Cloud Studio. Special thanks to them for their innovative approach and contributions to the Laravel community.

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
    'model' => env('OLLAMA_MODEL', 'Llama3'),
    'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
    'default_prompt' => env('OLLAMA_DEFAULT_PROMPT', 'Hello, how can I assist you today?'),
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
@method static \HalilCosdu\Ollama\Ollama embeddings(string $prompt)
@method static \HalilCosdu\Ollama\Ollama getKeepAlive(): string
@method static \HalilCosdu\Ollama\Ollama keepAlive(string $keepAlive): static
@method static \HalilCosdu\Ollama\Ollama ask()
@method static \HalilCosdu\Ollama\Ollama chat(array $conversation)

```
## Usage

### Basic Usage

```php
use HalilCosdu\Ollama\Facades\Ollama;

$response = Ollama::agent('You are a weather expert...')
    ->prompt('Why is the sky blue?')
    ->model('llama2')
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
    ->model('llama2')
    ->chat($messages);

// "You mentioned that you live in Turkey."

```

### Show Model Information

```php
$response = Ollama::model('Llama3')->show();
```

### Copy a Model

```php
Ollama::model('Llama3')->copy('NewModel');
```

### Delete a Model

```php
Ollama::model('Llama3')->delete();
```

### Generate Embeddings

```php
$embeddings = Ollama::model('Llama3')->embeddings('Your prompt here');
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
