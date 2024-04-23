<?php

namespace HalilCosdu\Ollama;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use HalilCosdu\Ollama\Commands\OllamaCommand;

class OllamaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-ollama')
            ->hasConfigFile();
    }
}
