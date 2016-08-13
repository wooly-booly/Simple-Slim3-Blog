<?php

use \src\Middleware\AuthMiddleware;

// ADMIN SECTION
$app->get('/admin', 'backend\controllers\AuthController:getLogin')->setName('login');
$app->post('/admin', 'backend\controllers\AuthController:postLogin');

// Forgot Password TODO :)
$app->get('/recover-password', 'backend\controllers\AuthController:getRecoverPassword')->setName('password.recover');
$app->post('/recover-password', 'backend\controllers\AuthController:postRecoverPassword')->setName('password.recover.post');
$app->get('/reset-password', 'backend\controllers\AuthController:getResetPassword')->setName('password.reset');
$app->post('/reset-password', 'backend\controllers\AuthController:postResetPassword')->setName('password.reset.post');

$app->group('/admin', function () {

    // Logout
    $this->get('/logout', 'backend\controllers\AuthController:logout')->setName('logout');

    // Users
    $this->get('/users', 'backend\controllers\UserController:index')->setName('users');
    $this->any('/users/add', 'backend\controllers\UserController:add')->setName('users.add');
    $this->any('/users/edit/{id: \d+}', 'backend\controllers\UserController:edit')->setName('users.edit');
    $this->any('/users/delete', 'backend\controllers\UserController:delete')->setName('users.delete');

    // Settings
    $this->get('/settings', 'backend\controllers\SettingController:index')->setName('settings');
    $this->post('/settings', 'backend\controllers\SettingController:update')->setName('settings.update');

    // Categories
    $this->get('/categories', 'backend\controllers\CategoryController:index')->setName('categories');
    $this->any('/categories/add', 'backend\controllers\CategoryController:add')->setName('categories.add');
    $this->get('/categories/delete/{id: \d+}', 'backend\controllers\CategoryController:delete')->setName('categories.delete');
    $this->any('/categories/edit/{id: \d+}', 'backend\controllers\CategoryController:edit')->setName('categories.edit');

    // Posts
    $this->get('/posts', 'backend\controllers\PostController:index')->setName('posts');
    $this->any('/posts/add', 'backend\controllers\PostController:add')->setName('posts.add');
    $this->get('/posts/delete/{id: \d+}', 'backend\controllers\PostController:delete')->setName('posts.delete');
    $this->any('/posts/edit/{id: \d+}', 'backend\controllers\PostController:edit')->setName('posts.edit');
    $this->get('/posts/state/{id: \d+}', 'backend\controllers\PostController:state')->setName('posts.state');

})->add(new AuthMiddleware($container));

$app->get('/', function ($request, $response, $args) {
    return 'Frontend under constraction';
})->setName('home');
