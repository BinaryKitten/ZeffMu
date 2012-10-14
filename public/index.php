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