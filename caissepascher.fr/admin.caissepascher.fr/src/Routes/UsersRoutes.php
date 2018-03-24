<?php

$app->post('/login', function ($request, $response, $args) {
    
    $data = $request->getParsedBody();

    require_once __DIR__ . '/../Mapper/UsersMapper.php';
    $user = new UsersMapper();

    $result = $user->getAccessToken($data);
    
    if(!$result)
        return echoRespnse(403, array("message" => "Le client n'est pas defini"), $response);
    else
        return echoRespnse(200, array("access_token" => $result[0]["token"], "token_refresh" => strtotime($result[0]["token_refresh"]), "token_type" => "Bearer", "scope" => "dashboard"), $response);
    
})->add($user_login);




/*
$app->post('/register', function ($request, $response, $args) {

    $data = $request->getParsedBody();

    require_once __DIR__ . '/../Mapper/UsersMapper.php';
    $users = new UsersMapper();

    $result = $users->registerUsers($data);

    if(!$result)
        return echoRespnse(401, array("error" => false, "message" => "Le client n'ai pas definie"), $response);
    else
        return echoRespnse(201, array("error"=>false, "data"=>$result), $response);
    
})->add($user_register);


*/