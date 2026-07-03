<?php

namespace HalilCosdu\Ollama\Tests;

use HalilCosdu\Ollama\OllamaServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'HalilCosdu\\Ollama\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        // Keep the test suite hermetic — any un-faked HTTP request fails loudly.
        Http::preventStrayRequests();
    }

    protected function getPackageProviders($app)
    {
        return [
            OllamaServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('ollama.url', 'http://127.0.0.1:11434');
        config()->set('ollama.model', 'llama3');
    }
}
