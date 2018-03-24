<?php

$app->get('/user', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "parametre" => $userDb->searchProducts(array("idUsers" => $this->userId)),
    );
    // var_dump($args);
    return $this->renderer->render($response, 'form/user.phtml', $args);

})->add($client_autorization);




$app->post('/user', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $productId = $route->getArgument('id');

    $data = $request->getParsedBody();

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    

    return $this->renderer->render($response, 'form/user.phtml', $args);

})->add($client_autorization);