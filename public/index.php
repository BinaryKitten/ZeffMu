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
