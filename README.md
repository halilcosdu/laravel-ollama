# This is my package laravel-ollama

[![Latest Version on Packagist](https://img.shields.io/packagist/v/halilcosdu/laravel-ollama.svg?style=flat-square)](https://packagist.org/packages/halilcosdu/laravel-ollama)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/halilcosdu/laravel-ollama/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/halilcosdu/laravel-ollama/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/halilcosdu/laravel-ollama/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/halilcosdu/laravel-ollama/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/halilcosdu/laravel-ollama.svg?style=flat-square)](https://packagist.org/packages/halilcosdu/laravel-ollama)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-ollama.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-ollama)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

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
