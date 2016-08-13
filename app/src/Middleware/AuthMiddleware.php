<?php namespace src\Middleware;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if (!$this->container->auth->check()) {
            // $this->container->flash->addMessage('error', $this->container->lang['forbidden']);

            return $response->withRedirect($this->container->router->pathFor('login'));
        }

        $response = $next($request, $response);

        return $response;
    }
}
