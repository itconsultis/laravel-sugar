<?php

require __DIR__.'/../vendor/autoload.php';

$dotfile = realpath(__DIR__.'/../.env');
$dotenv = new josegonzalez\Dotenv\Loader($dotfile);
$dotenv->parse()->toEnv();

Mockery::globalHelpers();


