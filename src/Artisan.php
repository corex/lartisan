<?php

namespace CoRex\Laravel\Console;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Artisan
{
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;

    private $path;
    private $name;
    private $version;
    private $commands;

    /**
     * Artisan constructor.
     *
     * @param string $path Must be root of Laravel installation.
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->commands = [];
    }

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
     * Add command.
     *
     * @param string $command
     * @throws \Exception
     */
    public function addCommand($command)
    {
        if (!is_string($command)) {
            throw new \Exception('You must specify class.');
        }
        if (!in_array($command, $this->commands)) {
            $this->commands[] = $command;
        }
    }

    /**
     * Add commands on path.
     *
     * @param string $path
     * @param boolean $recursive Default true.
     * @param string $commandSuffix Default 'Command'.
     */
    public function addCommandsOnPath($path, $recursive = true, $commandSuffix = 'Command')
    {
        if (substr($commandSuffix, -4) != '.php') {
            $commandSuffix .= '.php';
        }
        $path = str_replace('\\', '/', $path);
        if (strlen($path) > 0 && substr($path, -1) == '/') {
            $path = rtrim($path, '//');
        }
        if (!is_dir($path)) {
            return;
        }
        $files = scandir($path);
        if (count($files) == 0) {
            return;
        }
        foreach ($files as $file) {
            if (substr($file, 0, 1) == '.') {
                continue;
            }
            if (substr($file, -strlen($commandSuffix)) == $commandSuffix) {
                $class = $this->extractFullClass($path . '/' . $file);
                if ($class != '') {
                    $this->addCommand($class);
                }
            }
            if (is_dir($path . '/' . $file) && $recursive) {
                $this->addCommandsOnPath($path . '/' . $file, $recursive, $commandSuffix);
            }
        }
    }

    /**
     * Execute console application.
     */
    public function execute()
    {
        // Validate Laravel Installation.
        if (!file_exists($this->path . '/bootstrap/app.php')) {
            print("Laravel Installation not found in " . $this->path . "\n");
            exit;
        }

        $this->app = require_once($this->path . '/bootstrap/app.php');
        $kernel = $this->app->make(Kernel::class);
        $kernel->setName($this->name);
        $kernel->setVersion($this->version);
        $kernel->setCommands($this->commands);
        $status = $kernel->handle($input = new ArgvInput(), new ConsoleOutput());
        $kernel->terminate($input, $status);
        exit($status);
    }

    /**
     * Extract full class.
     *
     * @param string $filename
     * @return string
     */
    private function extractFullClass($filename)
    {
        $result = '';
        if (file_exists($filename)) {
            $data = $this->getFileContent($filename);
            $data = explode("\n", $data);
            if (count($data) > 0) {
                $namespace = '';
                $class = '';
                foreach ($data as $line) {
                    $line = str_replace('  ', ' ', $line);
                    if (substr($line, 0, 9) == 'namespace') {
                        $namespace = $this->getPart($line, 2, ' ');
                        $namespace = rtrim($namespace, ';');
                    }
                    if (substr($line, 0, 5) == 'class') {
                        $class = $this->getPart($line, 2, ' ');
                    }
                }
                if ($namespace != '' && $class != '') {
                    $result = $namespace . '\\' . $class;
                }
            }
        }
        return $result;
    }

    /**
     * Get part.
     *
     * @param string $data
     * @param integer $index
     * @param string $separator Trims data on $separator..
     * @return string
     */
    private function getPart($data, $index, $separator)
    {
        $data = trim($data, $separator) . $separator;
        if ($data != '') {
            $data = explode($separator, $data);
            if (isset($data[$index - 1])) {
                return $data[$index - 1];
            }
        }
        return '';
    }

    /**
     * Get file content.
     *
     * @param string $filename
     * @return string
     */
    private function getFileContent($filename)
    {
        $content = '';
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            $content = str_replace("\r", '', $content);
        }
        return $content;
    }
}