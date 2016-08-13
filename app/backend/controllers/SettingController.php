<?php namespace backend\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use \Slim\Container;
use \models\Setting;

class SettingController extends Controller
{
    public function __construct(Container $c)
    {
        parent::__construct($c);

        $this->setting = new Setting();
        $this->view->getEnvironment()->addGlobal('nav_title', $this->lang['settings']);
    }

    public function index(Request $request, Response $response)
    {
        $settings = $this->settings['main'];

        // Get Templates
        $templatesDir = $this->settings['view']['template_path'];
        $templates    = glob($templatesDir . '*', GLOB_ONLYDIR);

        array_walk($templates, function (&$val) {
            $paths = explode('/', $val);
            $val   = end($paths);
        });

        // Get Languages
        $languagesDir = $this->settings['language_dir'];
        $languages    = glob($languagesDir . '*', GLOB_ONLYDIR);

        array_walk($languages, function (&$val) {
            $paths = explode('/', $val);
            $val   = end($paths);
        });

        $this->view->render($response, 'settings/index.html', compact('settings', 'templates', 'languages'));
    }

    public function update(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if ($this->setting->isValid($data) === true) {
            $this->setting->first()->update($data);
            $this->flash->addMessage('success', $this->lang['success']);
        } else {
            $this->flash->addMessage('errors', $this->setting->errors());
        }

        return $response->withRedirect($this->router->pathFor('settings'));
    }
}
