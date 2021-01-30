#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Splitbills\Command\ExportTransactionsCommand;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/config', '.env');
// All of the defined variables are now available in the $_ENV and $_SERVER super-globals.
$dotenv->load();

$application = new Application();

// ... register commands
$application->add(new ExportTransactionsCommand());


$application->run();
