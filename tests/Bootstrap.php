<?php

if (is_readable(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    if (!is_readable(__DIR__ . '/../../../autoload.php')) {
        throw new RuntimeException('Please run composer installation!');
    }

    require_once __DIR__ . '/../../../autoload.php';
}

if (is_readable(__DIR__ . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    $config =  __DIR__ . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
} else {
    $config = __DIR__ . DIRECTORY_SEPARATOR . 'TestConfiguration.php.dist';
}
