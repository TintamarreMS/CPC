<?php

$client_update = function ($request, $response, $next) {
    $verifParam = verifyRequiredParams(array('idClient'), $request->getParsedBody());
    if(is_array($verifParam))
        return echoRespnse($verifParam["status_code"], $verifParam, $response);
    $response = $next($request, $response);
    return $response;
};

$client_create = function ($request, $response, $next) {
    $verifParam = verifyRequiredParams(array('nom', 'prenom', 'postal'), $request->getParsedBody());
    if(is_array($verifParam))
        return echoRespnse($verifParam["status_code"], $verifParam, $response);
    $response = $next($request, $response);
    return $response;
};

$client_update_form = function ($request, $response, $next) {
    $verifParam = verifyRequiredParams(array('firstname', 'lastname', 'postcode'), $request->getParsedBody());
    if(is_array($verifParam))
        return $response->withRedirect('/client');
    $response = $next($request, $response);
    return $response;
};
