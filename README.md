# ZfMicroFramework - a micro framework built on ZF2

This project is a simple example of how ZF2 could be used to build a 
really simple micro-framework. It looks exactly like 
[silex](http://silex.sensiolabs.org/), but its core is basically a 
[`ServiceManager`](http://framework.zend.com/manual/2.0/en/modules/zend.service-manager.intro.html).

This allows you to have simple closures returning output as complex
architectures involving services and more advanced components, such as 
ZF2's [ModuleManager](http://framework.zend.com/manual/2.0/en/modules/zend.module-manager.intro.html) 
or [Doctrine2 ORM](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html).

## Installation:

```sh
$ composer require ocramius/zf-micro-framework
```

## Usage:

In your `public/index.php` file (assuming `public` is your webroot), define 
following:

```php
require_once __DIR__ . '/vendor/autoload.php';
$app = new \ZfMicroFramework\Application();

$app->route('/hello/:name', function($params) use ($app) {
    echo 'Hello' . $name;
});

$app->run();
```

## Limitations

Since I really didn't mean to build something complex, ZfMicroFramework 
will currently route all HTTP requests, regardless of the HTTP method.

## Advanced usage

 * TBD: services
 * TBD: events
