<?php
// Application middleware

use \src\Middleware\Auth;
use \src\Middleware\CsrfViewMiddleware;

$app->add(new CsrfViewMiddleware($container));
$app->add($container->get('csrf'));


// For generating default slim NotFound page
$app->add(function ($request, $response, $next) use ($container) {
    // First execute anything else
    $response = $next($request, $response);

    // Check if the response should render a 404
    if (404 === $response->getStatusCode() &&
        0   === $response->getBody()->getSize()
    ) {
        // A 404 should be invoked
        $handler = $container['notFoundHandler'];
        return $handler($request, $response);
    }

    // Any other request, pass on current response
    return $response;
});
