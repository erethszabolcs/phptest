<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir
    ]
)->register();

/**
 * Registering namespaces
 */
$loader->registerNamespaces(
    [
       'Models'      => '../app/models/',
       'Models\Resultsets'      => '../app/models/resultsets',
    ]
)->register();