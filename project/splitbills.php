<?php

if (php_sapi_name() !== 'cli') {
	exit;
}

use Splitbills\App;

require './vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('./config', '.env');
// All of the defined variables are now available in the $_ENV and $_SERVER super-globals.
$dotenv->load();


$app = new App();
$app->run($argv);
