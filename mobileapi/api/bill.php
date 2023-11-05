<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/addbill', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);
    $hash = password_hash($jsonData['password'],PASSWORD_DEFAULT);

    $conn = $GLOBALS['connect'];
    $sql = 'insert into bill (id_user, total) values (?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $jsonData['id_user'],$jsonData['total']);
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
$app->get('/getbill/{id_user}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['connect'];

    $sql = 'select id_bill from bill where id_user = ? order by id_bill desc LIMIT 1';
    $stmt = $conn->prepare($sql);
    $id_user = $args['id_user'];
    $stmt->bind_param('i',$id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($row["id_bill"], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

$app->get('/bill/history/{id_user}', function (Request $request, Response $response,$args) {
    $conn = $GLOBALS['connect'];
    $id_user=$args['id_user'];
    $sql = 'SELECT ROW_NUMBER() OVER (ORDER BY id_bill desc) numberrow,id_bill,total
     from bill 
     where id_user=? order by id_bill desc ';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s',$id_user);
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