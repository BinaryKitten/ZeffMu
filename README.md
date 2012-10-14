# ZfMicroFramework - a micro framework built on ZF2

This project is a simple example of how ZF2 could be used to build a 
really simple micro-framework. It looks exactly like 
[silex](http://silex.sensiolabs.org/), but its core is basically a 
[`ServiceManager`](http://framework.zend.com/manual/2.0/en/modules/zend.service-manager.intro.html).

This allows you to have simple closures returning output as complex
architectures involving services and more advanced components, such as 
ZF2's [ModuleManager](http://framework.zend.com/manual/2.0/en/modules/zend.module-manager.intro.html) 
or [Doctrine2 ORM](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html).

Please note that this is a project just developed for fun and to see
how similar/different the architectures of Silex and ZF2 are.

## Installation:

```sh
$ composer require ocramius/zf-micro-framework
```

## Usage:

In your `public/index.php` file (assuming `public` is your webroot), define 
following:

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = \ZfMicroFramework\Application::init();

$app->route('/hello', function() {
    return 'Hi!';
});

$app->route('/hello/:name', function($params) use ($app) {
    return 'Hello ' . $params['name'];
});

$app->route('/hello/:name/:surname', function($params) use ($app) {
    return 'Hello, Mr. ' . $params['surname'] . ', or shall I call you ' . $params['name'] . '?';
});

$app->run();
```

## Advantages
 * Since the application is a fully functional ZF application, you could also return view
   models in your closures, thus having templates rendered.
 * You can attach listeners to events like in a standard ZF2 application
 * You can fetch services like in a standard MVC application (i.e. `$app->getServiceLocator()->get('db')`)
 * You can load modules and have any module functionality as in typical ZF2 applications

## Limitations (for now)

 * ZfMicroFramework will currently route all HTTP requests, regardless of the HTTP method.
 * It does not support things such as filtering output strings natively
 * It does not support setting controllers or retrieving them from the internal service
   locator (since that would require naming the controllers and basically ending up with
   ZF2's MVC). Routing and dispatching is also quite different from ZF2. A fallback may be
   interesting.
 * It does not support registering routes other than `Zend\Mvc\Router\Http\Part`
 * As a default, it has all the service provided in a default ZF2 application
 * Cannot define a parameter named "controller" in route matches, since it is reserved
 * Assembling routes does not yet work
 * Helper methods (view helpers/controller plugins) utilities are not yet accessible in a
   simple way from the application object. Some simple shortcuts may help.

## Advanced usage

 * TBD: services
 * TBD: events
