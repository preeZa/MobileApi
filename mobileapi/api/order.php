<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/addorder', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);
   
    $conn = $GLOBALS['connect'];
    $sql = 'insert into iorder (id_bill, id_product,amount,sum) values (?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $jsonData['id_bill'],$jsonData['id_product'],$jsonData['amount'],$jsonData['sum']);
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

$app->get('/getorder', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['connect'];

    $sql = 'SELECT type.name,product.image,iorder.amount,iorder.sum,iorder.id_bill from iorder
    INNER JOIN product ON iorder.id_product = product.id_product INNER JOIN type ON product.type = type.id_type ';
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