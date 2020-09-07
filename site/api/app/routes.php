<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/api', function (Group $group) {
        $group->get('/', function (Request $request, Response $response) {
            $response->getBody()->write('Blank Page');
            return $response;
        });

        $group->get('/Categories', 'ProductController')->setName('Categories');

        $group->get('/Collections', 'ProductController')->setName('Collections');

        $group->get('/SubCollections', 'ProductController')->setName('SubCollections');
    });

    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    // Default headers
    $app->add(function ($request, $handler) {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    });
};
