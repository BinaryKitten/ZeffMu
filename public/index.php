<?php
chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$app = \ZeffMu\App::init();
$app
    ->route('/', function() {
        return '<a href="/hello">HEllo!</a>';
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
->run();
