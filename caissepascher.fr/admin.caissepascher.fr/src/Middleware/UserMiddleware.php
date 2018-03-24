<?php

$user_login = function ($request, $response, $next) {
    $verifParam = verifyRequiredParams(array('client_id', 'client_secret', 'grant_type'), $request->getParsedBody());
    if(is_array($verifParam))
        return echoRespnse($verifParam["status_code"], $verifParam, $response);
    $response = $next($request, $response);
    return $response;
};

$user_register = function ($request, $response, $next) {
    $verifParam = verifyRequiredParams(array('username', 'lastname', 'firstname', 'compagny_name', 'email', 'forfait', 'passwordDesk', 'passwordAdmin'), $request->getParsedBody());
    if(is_array($verifParam))
        return echoRespnse($verifParam["status_code"], $verifParam, $response);
    $response = $next($request, $response);
    return $response;
};


