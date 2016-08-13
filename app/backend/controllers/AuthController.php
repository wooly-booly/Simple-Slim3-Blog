<?php namespace backend\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Container;
use \models\User;
use \Carbon\Carbon;

class AuthController extends Controller
{
    protected $user = null;

    public function getLogin(Request $request, Response $response)
    {
        if ($this->auth->check()) {
            return $response->withRedirect($this->router->pathFor('settings'));
        }

        return $this->view->render($response, 'auth/login.html');
    }

    public function postLogin(Request $request, Response $response)
    {
        $auth = $this->auth->attempt(
            $request->getParam('identifier'),
            $request->getParam('password')
        );

        if ($auth) {
            $this->flash->addMessage('success', $this->lang['login_success']);
            return $response->withRedirect($this->router->pathFor('settings'));
        }

        $this->flash->addMessage('errors', [$this->lang['login_error']]);
        return $response->withRedirect($this->router->pathFor('login'));
    }

    public function logout(Request $request, Response $response)
    {
        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('login'));
    }
}
