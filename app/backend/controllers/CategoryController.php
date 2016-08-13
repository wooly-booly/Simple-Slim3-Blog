<?php namespace backend\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Container;
use \models\Category;

class CategoryController extends Controller
{
    public function __construct(Container $c)
    {
        parent::__construct($c);

        $this->category = new Category();
        $this->view->getEnvironment()->addGlobal('nav_title', $this->lang['categories']);
    }

    public function index(Request $request, Response $response)
    {
        $categories = $this->category->getCategories();

        return $this->view->render($response, 'categories/index.html', compact('categories'));
    }

    public function add(Request $request, Response $response)
    {
        $parents  = $this->category->getCategories();
        $category = ["position" => 0];

        if ($request->isPost()) {
            $data = $category = $request->getParsedBody();

            if ($this->category->isValid($data) === true) {
                $this->category->addCategory($data);
                $this->flash->addMessage('success', $this->lang['success']);
                
                return $response->withRedirect($this->router->pathFor('categories'));
            } else {
                $this->flash->addMessage('errors', $this->category->errors());
                return $response->withRedirect($this->router->pathFor('categories.add'));
            }
        }

        return $this->view->render($response, 'categories/add.html', compact('parents', 'category'));
    }

    public function delete(Request $request, Response $response, $args)
    {
        $this->category = Category::find($args['id']);

        if (is_null($this->category)) {
            return $response->withStatus(404);
        }

        $this->category->delete();
        $this->flash->addMessage('success', $this->lang['success']);

        return $response->withRedirect($this->router->pathFor('categories'));
    }

    public function edit(Request $request, Response $response, $args)
    {
        $parents        = $this->category->getCategories();
        $this->category = Category::find($args['id']);

        if (is_null($this->category)) {
            return $response->withStatus(404);
        }

        $category = $this->category->toArray();

        if ($request->isPost()) {
            $data = $category = $request->getParsedBody();

            if ($this->category->isValid($data) === true) {
                $this->category->edit($data);
                $this->flash->addMessage('success', $this->lang['success']);
                
                return $response->withRedirect($this->router->pathFor('categories'));
            } else {
                $this->flash->addMessage('errors', $this->category->errors());

                return $response->withRedirect($this->router->pathFor('categories.edit', $args));
            }
        }

        return $this->view->render($response, 'categories/edit.html', compact('category', 'parents'));
    }
}
