<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/basket/user', function (Request $request, Response $response ,$args ) {
    
    $conn = $GLOBALS['connect'];

    $sql = 'SELECT basket.id_product,product.image,type.name,basket.amount,basket.id_user,product.price,sum(amount*price)as total
    FROM basket
    INNER JOIN product ON basket.id_product = product.id_product
    INNER JOIN type ON product.type = type.id_type
    group by basket.id_product,product.image,type.name,basket.amount,basket.id_user,product.price
    ';

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

$app->post('/basket', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);

    $conn = $GLOBALS['connect'];
    $sql = 'insert into basket (id_product, amount, id_user ) values (?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $jsonData['id_product'], $jsonData['amount'], $jsonData['id_user']);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {

        $data = ["affected_rows" => $affected, "last_idx" => $conn->insert_id];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});
$app->put('/basket/up/{id_product}/{id_user}', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);
    $id_product = $args['id_product'];
    $id_user = $args['id_user'];
    $conn = $GLOBALS['connect'];
    $sql = 'update basket set amount=amount+1 where id_product = ? and id_user = ? ';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $id_product,$id_user);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {
        $data = ["affected_rows" => $affected];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});
$app->put('/basket/down/{id_product}/{id_user}', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);
    $id_product = $args['id_product'];
    $id_user = $args['id_user'];
    $conn = $GLOBALS['connect'];
    $sql = 'update basket set amount=amount-1 where id_product = ? and id_user = ? ';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $id_product,$id_user);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {
        $data = ["affected_rows" => $affected];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});
$app->delete('/basket/{id_product}/{id_user}', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);
    $id_product = $args['id_product'];
    $id_user = $args['id_user'];
    $conn = $GLOBALS['connect'];
    $sql = 'DELETE from basket where id_product = ? and id_user = ? ';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $id_product,$id_user);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {
        $data = ["affected_rows" => $affected];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});