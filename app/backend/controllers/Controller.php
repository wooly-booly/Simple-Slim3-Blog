<?php namespace backend\controllers;

use Slim\Container;

class Controller
{
    public function __construct(Container $c)
    {
        $this->container = $c;
        
        $this->view->getLoader()->setPaths(
            $this->settings['view']['template_path_backend']
        );
        $this->view->getEnvironment()->addGlobal("lang", $this->lang);
        $this->view->getEnvironment()->addGlobal("main_title", $this->settings['main']['title']);

        if ($this->auth->check()) {
            $this->view->getEnvironment()->addGlobal("auth_username", $this->auth->user()->getFullNameOrUsername());
        }
    }

    public function __get($key)
    {
        if ($this->container->{$key}) {
            return $this->container->{$key};
        }
    }
}
