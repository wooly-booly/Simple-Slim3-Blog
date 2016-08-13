<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

define("ROOT", realpath(dirname(__DIR__)) . '/');
define("APP", ROOT . 'app/');
define("VENDOR_DIR", ROOT . "vendor/");

if (file_exists(VENDOR_DIR . "autoload.php")) {
    require VENDOR_DIR . "autoload.php";
} else {
    die("Please use 'composer install' to setup application");
}

require_once APP . 'bootstrap.php';
