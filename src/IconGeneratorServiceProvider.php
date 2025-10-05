<?php

namespace MickJ\IconGenerator;

use Illuminate\Support\ServiceProvider;
use MickJ\IconGenerator\Commands\GenerateIcons;

class IconGeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateIcons::class,
            ]);
        }
    }

    public function boot(): void
    {
        $this->loadCommands();
    }

    protected function loadCommands(): void
    {
        // eventualmente in futuro altri comandi
    }
}
