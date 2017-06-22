<?php

namespace CoRex\Laravel\Console;

use CoRex\Laravel\Console\Commands\MakeArtisanCommand;
use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->commands(MakeArtisanCommand::class);
    }
}
