# ZeffMu - a micro framework built on ZF2

Zeff - The effective nuclear charge (often symbolize as Zeff or Z*) is the net positive charge experienced by an electron in a multi-electron atom.

Mu - Micron or small

Zeff is also a contraction of the British English pronounced Zed Eff (ZF)

--------------------------------------------------------------------------------------------------

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

In a project with a `composer.json` file, type following in your console.

```sh
$ composer require binarykitten/zeffmu
```

You can type `*` as a required version.

## Usage:

In your `public/index.php` file (assuming `public` is your webroot), define
following:

```php
<?php
chdir(dirname(__DIR__));
require 'vendor/autoload.php';

use ZeffMu\App;

$app = App::init();
$app
    ->route('/', function() use ($app) {
        $helper = $app->getViewHelper();

        return '<a href="' . $helper->basepath('hello') . '">Hello</a> World!';
    })
    ->route('/hello', function() {
        return 'Hi!';
    })
    ->route('/hello/:name', function($params) {
        return 'Hello ' . $params['name'];
    })
    ->route('/hello/:name/:surname', function($params) {
        return 'Hello, Mr. ' . $params['surname'] . ', or shall I call you ' . $params['name'] . '?';
    })
    ->error(function($event) {
        $response = $event->getResponse();

        switch ($event->getError()) {
            case App::ERROR_ROUTER_NO_MATCH:
            case App::ERROR_CONTROLLER_INVALID:
            case App::ERROR_CONTROLLER_NOT_FOUND:
                $response->setStatusCode(404);
                $message = '<h1>Mystery of the 404 page</h1> Uh this is too big a mystery for me. '
                         . 'I think we\'d better call in the Hardly Boys.';
                return $message;

            default:
                $response->setStatusCode(500);
                return '<h1>It\'s over... 500</h1> That was not supposed to happen.';
        }
    })
->run();
```

## Advantages
 * Since the application is a fully functional ZF application, you could also return view
   models in your closures, thus having templates rendered.
 * You can attach listeners to events like in a standard ZF2 application
 * You can fetch services like in a standard MVC application (i.e. `$app->getService('db')` or `$app->getServiceLocator()->get('db')`)
 * You can load modules and have any module functionality as in typical ZF2 applications

## Limitations (for now)

 * ZeffMu will currently route all HTTP requests, regardless of the HTTP method.
 * It does not support things such as filtering output strings natively
 * It does not support setting controllers or retrieving them from the internal service
   locator (since that would require naming the controllers and basically ending up with
   ZF2's MVC). Routing and dispatching is also quite different from ZF2. A fallback may be
   interesting.
 * It does not support registering routes other than `Zend\Mvc\Router\Http\Part`
 * As a default, it has all the service provided in a default ZF2 application
 * Cannot define a parameter named "controller" in route matches, since it is reserved
 * Assembling routes does not yet work

## Advanced usage

 * TBD: services
 * TBD: events