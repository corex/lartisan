# CoRex Laravel Console
Laravel Console (artisan, commands, visibility).

**_Versioning for this package follows http://semver.org/. Backwards compatibility might break on upgrade to major versions._**

For bigger Laravel installations, the list of artisan commands can be quite long. This package enables you to have your own artisan with commands of your choice.

**Warning: existing laravel commands such as route:list is only hidden. If you add a command with same signature as an existing command, it will override existing command. If this happens, you cannot use i.e. $this->call() to call the overridden Laravel Command.**

## Installation
Run ```"composer require corex/lconsole"```.

Now you have 2 options.

#### Option 1
Register the provider and make commands available in Laravel artisan.

Add following code to AppServiceProviders@register method.
```php
if ($this->app->environment() == 'local') {
    $this->app->register(\CoRex\Laravel\Console\ConsoleServiceProvider::class);
}
```

#### Option 2
Copy the file "artisan" in package to root of Laravel installation and rename it.


#### Setup artisan
Finally modify the created artisan file to suit your needs.


Example of a new artisan.
```php
require_once(__DIR__ . '/vendor/autoload.php');

$artisan = new \CoRex\Laravel\Console\Artisan(__DIR__);

// Set name on artisan.
//$artisan->setName('name');

// Set version on artisan.
//$artisan->setVersion('x.y.z');

// Add single command.
//$artisan->addCommand(MyCommand::class);

// Add multiple commands on specified path.
//$artisan->addCommandsOnPath(path-to-commands, true, '');

$artisan->execute();
```

It is good practice to end all of your commands with the word "Command" i.e. "MyCommand". This way you can have only your commands added on "$artisan->addCommandsOnPath()" and not all other classes i.e. helpers.

Example of a modified artisan.
```php
require_once(__DIR__ . '/vendor/autoload.php');

$artisan = new \CoRex\Laravel\Console\Artisan(__DIR__);

// Set name on artisan.
$artisan->setName('Test');

// Set version on artisan.
$artisan->setVersion('1.0.0');

// Add single command.
//$artisan->addCommand(MyCommand::class);

// Add multiple commands on specified path.
$artisan->addCommandsOnPath(__DIR__ . '/app/Console/Commands, true, '');

$artisan->execute();
```
