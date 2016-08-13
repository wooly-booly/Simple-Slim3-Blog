<?php namespace src\Middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class CsrfViewMiddleware extends Middleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $nameKey  = $this->container->csrf->getTokenNameKey();
        $valueKey = $this->container->csrf->getTokenValueKey();
        $name     = $request->getAttribute($nameKey);
        $value    = $request->getAttribute($valueKey);

        $this->container->view->getEnvironment()->addGlobal('csrf', [
            'field' => '
                <input type="hidden" name="' . $nameKey . '" value="' . $name . '">
                <input type="hidden" name="' . $valueKey . '" value="' . $value . '">
            '
        ]);

        $response = $next($request, $response);

        return $response;
    }
}
