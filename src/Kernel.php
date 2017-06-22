<?php

namespace CoRex\Laravel\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    private $name;
    private $version;
    private $artisanCommands;

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set version.
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Set commands.
     *
     * @param array $commands
     */
    public function setCommands(array $commands)
    {
        $this->artisanCommands = $commands;
    }

    /**
     * Get artisan.
     *
     * @return Application|\Illuminate\Console\Application
     */
    protected function getArtisan()
    {
        if (is_null($this->artisan)) {
            $this->artisan = new Application($this->app, $this->events, $this->app->version(), $this->artisanCommands);
            $this->artisan->setName($this->name);
            $this->artisan->setVersion($this->version);
            if (count($this->artisanCommands) > 0) {
                $this->artisan->resolveCommands($this->artisanCommands);
            }
        }
        return $this->artisan;
    }
}
