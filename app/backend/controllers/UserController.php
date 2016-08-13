<?php namespace backend\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Container;
use \models\User;

class UserController extends Controller
{
    protected $user = null;

    public function __construct(Container $c)
    {
        parent::__construct($c);

        $this->user = new User();
        $this->view->getEnvironment()->addGlobal('nav_title', $this->lang['user']);
    }

    public function index(Request $request, Response $response)
    {
        $users = User::orderBy('created_at', 'asc')->get();

        return $this->view->render($response, 'users/index.html', compact('users'));
    }

    public function add(Request $request, Response $response)
    {
        $userInfo = [];

        if ($request->isPost()) {
            $userInfo = $request->getParsedBody();

            if ($this->user->isValid($userInfo, 'add') === true) {
                $this->user->create([
                    'username'   => $userInfo['username'],
                    'email'      => $userInfo['email'],
                    'first_name' => $userInfo['first_name'],
                    'last_name'  => $userInfo['last_name'],
                    'password'   => $this->hash->password($userInfo['password']),
                ]);
                $this->flash->addMessage('success', $this->lang['user_registered']);

                return $response->withRedirect($this->router->pathFor('users'));
            }
        }

        return $this->view->render($response, 'users/add.html', [
            'errors' => $this->user->errors(),
            'user'   => $userInfo,
        ]);
    }

    public function edit(Request $request, Response $response, $args)
    {
        $userInfo = $this->user = $this->user->find($args['id']);

        if (!$this->user) {
            return $response->withRedirect($this->router->pathFor('users'));
        }

        if ($request->isPost()) {
            $userInfo = array_merge($this->user->toArray(), $request->getParsedBody());

            if (isset($userInfo['submit_edit']) && ($this->user->isValid($userInfo, 'edit') === true)) {
                $this->user->update([
                    'email'      => $userInfo['email'],
                    'first_name' => $userInfo['first_name'],
                    'last_name'  => $userInfo['last_name'],
                ]);

                $this->flash->addMessage('success', $this->lang['user_updated']);

                return $response->withRedirect($this->router->pathFor('users'));
            }

            if (isset($userInfo['submit_change_password']) && ($this->user->isValid($userInfo, 'reset-password') === true)) {
                $this->user->update([
                    'password' => $this->hash->password($userInfo['password']),
                ]);
                $this->flash->addMessage('success', $this->lang['user_updated']);

                return $response->withRedirect($this->router->pathFor('users'));
            }
        }

        return $this->view->render($response, 'users/edit.html', [
            'errors' => $this->user->errors(),
            'user'   => $userInfo
        ]);
    }

    public function delete(Request $request, Response $response)
    {
        $ids = $request->getParsedBody()['users'];

        if (!empty($ids)) {
            User::destroy($ids);
        }

        $this->flash->addMessage('success', $this->lang['user_delete']);

        return $response->withRedirect($this->router->pathFor('users'));
    }
}
