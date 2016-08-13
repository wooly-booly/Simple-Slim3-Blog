<?php namespace backend\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \models\Post;
use \models\Category;
use \Slim\Container;

class PostController extends Controller
{
    public function __construct(Container $c)
    {
        parent::__construct($c);

        $this->post     = new Post();
        $this->category = new Category();

        $this->view->getEnvironment()->addGlobal('nav_title', $this->lang['posts']);
    }

    public function index(Request $request, Response $response)
    {
        $posts = Post::orderBy('date', 'desc')->get();

        return $this->view->render($response, 'posts/index.html', compact('posts'));
    }

    public function add(Request $request, Response $response)
    {
        $parents = $this->category->getCategories();
        $imgDir  = $this->settings['images_dir'];

        if ($request->isPost()) {
            $data = $request->getParsedBody();

            if ($this->post->isValid($data) === true) {
                if (!empty($_FILES) && $_FILES['img']['size'] > 0) {
                    $data['img'] = $imgDir . date('Y-m-d_h-i-s_') . $_FILES['img']['name'];
                    move_uploaded_file($_FILES['img']['tmp_name'], $data['img']);
                }

                $data['category_id'] = ($data['category_id'] == 0) ? null : $data['category_id'];
                $this->post->fill($data)->save();
                $this->flash->addMessage('success', $this->lang['success']);

                return $response->withRedirect($this->router->pathFor('posts'));
            } else {
                $this->flash->addMessage('errors', $this->post->errors());

                return $response->withRedirect($this->router->pathFor('posts.add'));
            }
        }

        return $this->view->render($response, 'posts/add.html', compact('parents'));
    }

    public function state(Request $request, Response $response, $args)
    {
        $this->post = Post::find($args['id']);
        
        if (is_null($this->post)) {
            return $response->withStatus(404);
        }

        $this->post->state = ($this->post->state == 1) ? 0 : 1;

        $this->post->save();

        return $response->withRedirect($this->router->pathFor('posts'));
    }

    public function edit(Request $request, Response $response, $args)
    {
        $this->post = $post = Post::find($args['id']);
        $parents    = $this->category->getCategories();

        if (is_null($this->post)) {
            return $response->withStatus(404);
        }

        if ($request->isPost()) {
            $data   = $request->getParsedBody();
            $imgDir = $this->settings['images_dir'];

            if ($this->post->isValid($data) === true) {
                if (!empty($_FILES) && $_FILES['img']['size'] > 0) {
                    $data['img'] = $imgDir. date('Y-m-d_h-i-s_') . $_FILES['img']['name'];
                    move_uploaded_file($_FILES['img']['tmp_name'], $data['img']);

                    if (is_readable($this->post->img)) {
                        unlink($this->post->img);
                    }
                }

                $data['category_id'] = ($data['category_id'] == 0) ? null : $data['category_id'];

                $this->post->fill($data)->save();
                $this->flash->addMessage('success', $this->lang['success']);

                return $response->withRedirect($this->router->pathFor('posts'));
            } else {
                $this->flash->addMessage('errors', $this->category->errors());

                return $response->withRedirect($this->router->pathFor('posts.edit'));
            }
        }

        return $this->view->render($response, 'posts/edit.html', compact('parents', 'post'));
    }

    public function delete(Request $request, Response $response, $args)
    {
        $this->post = Post::find($args['id']);

        if (is_null($this->post)) {
            return $response->withStatus(404);
        }

        // Delete post image
        if (is_readable($this->post->img)) {
            unlink($this->post->img);
        }

        $this->post->delete();
        $this->flash->addMessage('success', $this->lang['success']);

        return $response->withRedirect($this->router->pathFor('posts'));
    }
}
