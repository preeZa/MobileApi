<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/product/type/{id_type}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['connect'];

    $sql = 'select id_product,price,type.name,image from product inner join type on  product.type = type.id_type where id_type = ?';
    $stmt = $conn->prepare($sql);
    $id_type = $args['id_type'];
    $stmt->bind_param('i', $id_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});
$app->get('/product', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['connect'];

    $sql = 'select id_product,price,type.name,image from product inner join type on  product.type = type.id_type';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});