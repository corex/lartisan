<?php

namespace CoRex\Laravel\Console\Commands;

use Illuminate\Console\Command;

class MakeArtisanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:artisan
        {name : Name of artisan file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make artisan';

    /**
     * Handle.
     *
     * @return boolean
     */
    public function handle()
    {
        $this->info('CoRex Laravel Console - ' . $this->description);
        $name = $this->argument('name');
        if (strpos($name, '.') !== false) {
            $this->error('It is not allowed to specify extension.');
            return false;
        }

        $filename = base_path($name);
        if (file_exists($filename)) {
            $this->error('Artisan ' . $name . ' already exists.');
            return false;
        }

        try {
            copy(dirname(dirname(__DIR__)) . '/artisan', $filename);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }

        // Show explanation.
        $this->line('');
        $this->info('Artisan ' . $name . ' (' . $filename . ') created.');
        $this->line('');
        $this->line('Modify ' . $filename . ' to suit your needs.');

        return true;
    }
}