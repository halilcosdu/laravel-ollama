<?php

namespace HalilCosdu\Ollama\Commands;

use Illuminate\Console\Command;

class OllamaCommand extends Command
{
    public $signature = 'laravel-ollama';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
