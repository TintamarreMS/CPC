<?php

$transaction_create = function ($request, $response, $next) {
    $verifParam = verifyRequiredParams(array('userTransactionNb'), $request->getParsedBody());
    if(is_array($verifParam))
        return echoRespnse($verifParam["status_code"], $verifParam, $response);
    $response = $next($request, $response);
    return $response;
};