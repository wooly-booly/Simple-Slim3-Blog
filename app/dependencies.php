<?php

use \Illuminate\Database\Capsule\Manager as Capsule;
use \Slim\Flash\Messages;
use \src\Hash;
use \src\Auth;
use \models\User;
use \Slim\Csrf\Guard;

// DIC configuration
$container = $app->getContainer();

// Eloquent ORM
$capsule = new Capsule;
$capsule->addConnection($container->get('settings')['db']);
$capsule->bootEloquent();
$capsule->setAsGlobal();
$container['db'] = function ($c) use ($capsule) {
    return $capsule;
};

// view renderer (Twig)
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new \Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);
    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_StringLoader()); // multiple languages
    $view->addExtension(new Twig_Extension_Debug());

    $view->getEnvironment()->addGlobal('flash', $c['flash']);

    return $view;
};

// Monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// Slim Flash
$container['flash'] = function () {
    return new Messages();
};

// Hash
$container['hash'] = function ($c) {
    $hashAlgo = $c->get('settings')['hash']['algo'];
    $hashCost = $c->get('settings')['hash']['cost'];

    return new Hash($hashAlgo, $hashCost);
};

// CSRF
$container['csrf'] = function ($c) {
    return new Guard;
};

// Auth
$container['auth'] = function ($c) {
    return new Auth($c->get('hash'), $c->get('settings')['auth']['session']);
};

//////////////////////////////////////////////////////////////////////
$container['user'] = function ($c) {
    return new User;
};
//////////////////////////////////////////////////////////////////////

// Site Settings from DB (user set this settings in admin panel)
$container['settings']['main'] = \models\Setting::first()->toArray();

// Langs for Admin Panel
$lang              = $container['settings']['main']['language'];
$container['lang'] = require_once $container['settings']['language_dir'] . $lang . '/' . $lang . ".php";
