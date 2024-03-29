<?php
include("../connection.php");
require __DIR__ . '/../../vendor/autoload.php';


use Firebase\JWT\JWT;


$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username']) && isset($data['password'])) {
    $username = $data['username'];
    $password = $data['password'];

$query = $mysqli->prepare('select userid,role_name,username,password from users where username=?');
$query->bind_param('s', $username);
$query->execute();
$query->store_result();
$num_rows = $query->num_rows;
$query->bind_result($id, $name, $role_name, $hashed_password);
$query->fetch();


$response = [];
if ($num_rows == 0) {
    $response['status'] = 'user not found';
    echo json_encode($response);
} else {
    if (password_verify($password, $hashed_password)) {
        $key = "your_secret";
        $payload_array = [];
        $payload_array["user_id"] = $id;
        $payload_array["name"] = $name;
        $payload_array["rolename"] = $role_name;
        $payload_array["exp"] = time() + 3600;
        $payload = $payload_array;
        $response=[];
        $jwt = JWT::encode($payload, $key, 'HS256');
        $response['jwt'] = $jwt;
        $response2['id'] = $id;
        echo json_encode($response + $response2);
    
    } else {
        $response['status'] = 'wrong credentials';
        echo json_encode($response);
    }
}
};