<?php

/**
 * @file
 * Boostrap for PHPUnit.
 */
$autoloader = __DIR__ . '/../vendor/autoload.php';
$loader = require $autoloader;

if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
  define('PHPUNIT_COMPOSER_INSTALL', $autoloader);
}

date_default_timezone_set('Australia/Sydney');
