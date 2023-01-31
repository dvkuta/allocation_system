<?php

require __DIR__ . '/../vendor/autoload.php';   # načte Composer autoloader

Tester\Environment::setup();

$configurator = new Nette\Configurator;

$configurator->setDebugMode(false);
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
    ->addDirectory(__DIR__ . '/../app')
    ->register();

$configurator->setTimeZone('Europe/Prague');


$configurator->addConfig(__DIR__ . '/../config/configTest.neon');

return $configurator->createContainer();