<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/customer', function (Request $request, Response $response ,$args ) {
    $conn = $GLOBALS['connect'];
    $sql = 'select * from customer ';
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
$app->get('/customer/{id}', function (Request $request, Response $response ,$args ) {
    $conn = $GLOBALS['connect'];
    $id = $args['id'];
    $sql = 'select * from customer where id_user = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($row, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});
$app->delete('/customer/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $conn = $GLOBALS['connect'];
    $sql = 'delete from customer where id_user = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
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
$app->post('/customer', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);
    $hash = password_hash($jsonData['password'],PASSWORD_DEFAULT);

    $conn = $GLOBALS['connect'];
    $sql = 'insert into customer (username, name, phone, addres, image , password) values (?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssss', $jsonData['username'],$jsonData['name'],$jsonData['phone'], $jsonData['addres'],$jsonData['image'], $hash);
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
$app->post('/customer/login', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);

    $conn = $GLOBALS['connect'];
    $sql = 'select * from customer where username=? ';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s',$jsonData['username']);
    $stmt->execute();
    $result =$stmt->get_result();
    if($result->num_rows==1){
        //กระบวนการแปลง รหัสปกติ ให้เป็นการเข้ารหัส
        $row=$result->fetch_assoc();
        $pwdInDb = $row["password"];
       if(password_verify($jsonData['password'],$pwdInDb)){
        // echo "Login Success";
            $response->getBody()->write(json_encode($row, JSON_UNESCAPED_UNICODE));
            return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(201);
        }
       else{
        $response->getBody()->write(json_encode("รหัสผ่านผิด", JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
            return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
       } 
    }
    else{
        $response->getBody()->write(json_encode("ชื่อผู้ใช้ผิด", JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
            return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }
});
?>