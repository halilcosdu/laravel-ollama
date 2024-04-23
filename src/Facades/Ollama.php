<?php

namespace HalilCosdu\Ollama\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HalilCosdu\Ollama\Ollama
 */
class Ollama extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \HalilCosdu\Ollama\Ollama::class;
    }
}
