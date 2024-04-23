# This is my package laravel-ollama

[![Latest Version on Packagist](https://img.shields.io/packagist/v/halilcosdu/laravel-ollama.svg?style=flat-square)](https://packagist.org/packages/halilcosdu/laravel-ollama)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/halilcosdu/laravel-ollama/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/halilcosdu/laravel-ollama/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/halilcosdu/laravel-ollama/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/halilcosdu/laravel-ollama/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/halilcosdu/laravel-ollama.svg?style=flat-square)](https://packagist.org/packages/halilcosdu/laravel-ollama)

## Laravel Ollama

Laravel Ollama is a PHP package that provides a simple and intuitive interface for interacting with the Ollama API. It is designed to be used with Laravel, a popular PHP framework, but can also be used in any PHP application.  This package provides a set of methods for making requests to the Ollama API, including methods for setting the agent, prompt, model, format, options, and more. It also includes methods for handling responses from the API, such as retrieving the response in a specific format or streaming the response.  With Laravel Ollama, you can easily integrate the Ollama API into your Laravel application and start making requests in a matter of minutes.  
Features
Easy configuration: Set up the package with your Ollama API credentials and start making requests.
Fluent interface: Chain methods together to build your requests.
Flexible: Use the package in any PHP application, not just Laravel.
Comprehensive: Covers all the endpoints of the Ollama API.

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
@method static \HalilCosdu\Ollama\Ollama ask()
@method static \HalilCosdu\Ollama\Ollama chat(array $conversation)

```
```php
$ollama = new HalilCosdu\Ollama();
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
