<?php

if (is_readable(__DIR__ . '/../vendor/autoload.php')) {
    $loader = require __DIR__ . '/../vendor/autoload.php';
} else {
    if (!is_readable(__DIR__ . '/../../../autoload.php')) {
        throw new RuntimeException('Please run composer installation!');
    }

    $loader = require __DIR__ . '/../../../autoload.php';
}

$loader->add('ZeffMuTest\\', __DIR__);
$loader->add('ZeffMuTestAsset\\', __DIR__);

if (is_readable(__DIR__ . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    $config =  __DIR__ . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
} else {
    $config = __DIR__ . DIRECTORY_SEPARATOR . 'TestConfiguration.php.dist';
}
