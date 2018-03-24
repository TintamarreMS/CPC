<?php

$client_autorization = function ($request, $response, $next) {
    if(!isset($_COOKIE["token"]) || empty($_COOKIE["token"]))
        return $response->withRedirect('/');
        
    require_once __DIR__ . '/../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    $this->userId = $userDb->isValidApiKey(array("type" => "admin_credentials", "token" => $_COOKIE["token"] ));
    
    if($this->userId  == false || $this->userId  == null)
        return $response->withRedirect('/');
    
    $response = $next($request, $response);

    return $response;
};

$caisse_autorization = function ($request, $response, $next) {
    $token = $request->getHeader('Authorization');
    if(count($token) != 1 )
        return echoRespnse(401, "No Authorization", $response);
    $token = explode(" ", $token[0]);
    if(count($token) != 2 )
        return echoRespnse(401, "Error sytaxe Authorization", $response);

    require_once __DIR__ . '/../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    $this->userId = $userDb->isValidApiKey(array("type" => "client_credentials", "token" => $token[1] ));
    
    if($this->userId  == false || $this->userId  == null)
        return echoRespnse(401, "Error Token", $response);
    
    $response = $next($request, $response);

    return $response;
};