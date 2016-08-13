<?php
use \src\App;

session_start();

// Config
$mode     = file_get_contents(APP . 'config/mode.php');
$settings = require APP . 'config/' . $mode . '.php';

// Instantiate the app
$app = new \Slim\App($settings);
App::setup($app);

// Set up dependencies (DIC $container)
require APP . '/dependencies.php';
// Register middleware
require APP . '/middleware.php';
// Register routes
require APP . '/routes.php';

// Run app
$app->run();
