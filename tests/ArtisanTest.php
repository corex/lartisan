<?php

use CoRex\Laravel\Console\Artisan;
use Orchestra\Testbench\TestCase;

class ArtisanTest extends TestCase
{
    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $check = $this->getRandomValue();
        $artisan = new Artisan($check);
        $this->assertEquals($check, $this->getProperty($artisan, 'path'));
        $this->assertEquals([], $this->getProperty($artisan, 'commands'));
        $this->assertNull($this->getProperty($artisan, 'name'));
        $this->assertNull($this->getProperty($artisan, 'version'));
    }

    /**
     * Test setName().
     */
    public function testSetName()
    {
        $check = $this->getRandomValue();
        $artisan = new Artisan(null);
        $artisan->setName($check);
        $this->assertEquals($check, $this->getProperty($artisan, 'name'));
    }

    /**
     * Test setVersion().
     */
    public function testSetVersion()
    {
        $check = $this->getRandomValue();
        $artisan = new Artisan(null);
        $artisan->setVersion($check);
        $this->assertEquals($check, $this->getProperty($artisan, 'version'));
    }

    /**
     * Test addCommand().
     */
    public function testAddCommand()
    {
        $artisan = new Artisan(null);
        $artisan->addCommand(TestCommand::class);
        $this->assertEquals([TestCommand::class], $this->getProperty($artisan, 'commands'));
    }

    /**
     * Test addCommandsOnPath().
     */
    public function testAddCommandsOnPath()
    {
        $artisan = new Artisan(null);
        $artisan->addCommandsOnPath(__DIR__ . '/Helpers');
        $this->assertEquals([Something\TestCommand::class], $this->getProperty($artisan, 'commands'));
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
