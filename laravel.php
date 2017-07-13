#! /usr/bin/env php

<?php 

use App\NewCommand;
use Symfony\Component\Console\Application;

require 'vendor/autoload.php';

$app = new Application('Homemade Laravel Install', '1.0');

$app->add( new NewCommand(new GuzzleHttp\Client));

$app->run();