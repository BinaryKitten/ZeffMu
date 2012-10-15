<?php
chdir(dirname(__DIR__));
include 'init_autoloader.php';

$app = \ZeffMu\App::init();
$b = $app->bob;
$b();
$app
    ->route('/', function() {
        return '<a href="/hello">HEllo!</a>';
    })
    ->route('/hello', function() {
        return 'Hi!';
    })
    ->route('/hello/:name', function($params) use ($app) {
        return 'Hello ' . $params['name'];
    })
    ->route('/hello/:name/:surname', function($params) use ($app) {
        return 'Hello, Mr. ' . $params['surname'] . ', or shall I call you ' . $params['name'] . '?';
    })
->run();