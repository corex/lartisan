<?php

use CoRex\Laravel\Console\Application;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Application as SymfonyApplication;

class KernelTest extends TestCase
{
    /**
     * Test setName().
     */
    public function testSetName()
    {
        $check = $this->getRandomValue();
        $kernel = $this->app->make(\CoRex\Laravel\Console\Kernel::class);
        $kernel->setName($check);
        $this->assertEquals($check, $this->getProperty($kernel, 'name'));
    }

    /**
     * Test setVersion().
     */
    public function testSetVersion()
    {
        $check = $this->getRandomValue();
        $kernel = $this->app->make(\CoRex\Laravel\Console\Kernel::class);
        $kernel->setVersion($check);
        $this->assertEquals($check, $this->getProperty($kernel, 'version'));
    }

    /**
     * Test setCommands().
     */
    public function testSetCommands()
    {
        $check = $this->getRandomValue();
        $kernel = $this->app->make(\CoRex\Laravel\Console\Kernel::class);
        $kernel->setCommands([$check]);
        $this->assertEquals([$check], $this->getProperty($kernel, 'artisanCommands'));
    }

    /**
     * Test getArtisan().
     */
    public function testGetArtisan()
    {
        $check1 = $this->getRandomValue();
        $check2 = $this->getRandomValue();
        $kernel = $this->app->make(\CoRex\Laravel\Console\Kernel::class);
        $kernel->setName($check1);
        $kernel->setVersion($check2);
        $kernel->setCommands([
            \Something\TestCommand::class
        ]);
        $artisan = $this->callMethod('getArtisan', $kernel);
        $this->assertEquals(Application::class, get_class($artisan));
        $this->assertEquals($check1, $this->getProperty($artisan, 'name', null, SymfonyApplication::class));
        $this->assertEquals($check2, $this->getProperty($artisan, 'version', null, SymfonyApplication::class));
        $this->assertEquals(
            [\Something\TestCommand::class],
            $this->getProperty($artisan, 'commands', null, Application::class)
        );
    }

    /**
     * Get property.
     *
     * @param object $object
     * @param string $property
     * @param mixed $defaultValue Default null.
     * @param string $class Default null which means class from $object.
     * @return mixed
     */
    public function getProperty($object, $property, $defaultValue = null, $class = null)
    {
        $reflectionClass = $this->getReflectionClass($object, $class);
        try {
            $property = $reflectionClass->getProperty($property);
            if ($object === null && !$property->isStatic()) {
                return $defaultValue;
            }
            $property->setAccessible(true);
            return $property->getValue($object);
        } catch (Exception $e) {
            return $defaultValue;
        }
    }

    /**
     * Get reflection class.
     *
     * @param object $object
     * @param string $class Default null which means class from $object.
     * @return \ReflectionClass
     */
    private function getReflectionClass($object, $class = null)
    {
        if ($class === null) {
            $class = get_class($object);
        }
        return new ReflectionClass($class);
    }

    /**
     * Call method.
     *
     * @param string $name
     * @param object $object
     * @param array $arguments Default [].
     * @return mixed
     */
    private function callMethod($name, $object, array $arguments = [])
    {
        $method = new ReflectionMethod(get_class($object), $name);
        $method->setAccessible(true);
        if (count($arguments) > 0) {
            return $method->invokeArgs($object, $arguments);
        } else {
            return $method->invoke($object);
        }
    }

    /**
     * Get random value.
     *
     * @return string
     */
    private function getRandomValue()
    {
        return md5(mt_rand(1, 100000));
    }

    /**
     * Setup.
     */
    protected function setUp()
    {
        parent::setUp();
        require_once(__DIR__ . '/Helpers/TestCommand.php');
    }
}
