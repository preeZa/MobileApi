<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/mobileapi');
$app->addErrorMiddleware(true, true, true);

require __DIR__ . '/dbconnect.php';
require __DIR__ . '/api/basket.php';
require __DIR__ . '/api/bill.php';
require __DIR__ . '/api/customer.php';
require __DIR__ . '/api/order.php';
require __DIR__ . '/api/product.php';
require __DIR__ . '/api/type.php';

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->get('/ping', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Pong!!!");
    return $response;
});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    throw new HttpNotFoundException($request);
});

$app->run();