<?php

$app->get('/client', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ClientMapper.php';
    $clientDb = new ClientMapper();

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "clients" => $clientDb->searchClient(array("cash_desk_users_idUsers"=>$this->userId, "isDelete" => 0)),
    );
    // var_dump($args);
    return $this->renderer->render($response, 'liste/client.phtml', $args);

})->add($client_autorization);


$app->get('/client-add', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ClientMapper.php';
    $clientDb = new ClientMapper();

    return $this->renderer->render($response, 'form/addClient.phtml', $args);

})->add($client_autorization);


$app->post('/client-add', function ($request, $response, $args) {
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ClientMapper.php';
    $clientDb = new ClientMapper();

    $data = $request->getParsedBody();

    $theClient = array();
    if(strlen($data['firstname']) >= 2 && strlen($data['firstname']) <= 44)
        $theClient['firstname'] = trim($data['firstname']);
    else{
        $error_fields .= 'nom, ';
        $error = true;
    }if(strlen($data['lastname']) >= 2 && strlen($data['lastname']) <= 44)
        $theClient['lastname'] = trim($data['lastname']);
    else{
        $error_fields .= 'lastname, ';
        $error = true;
    }if(strlen($data['postcode']) == 5)
        $theClient['postcode'] = trim($data['postcode']);
    else{
        $error_fields .= 'postcode, ';
        $error = true;
    }if(validateEmail($data['email']) && strlen($data['email']) >= 10)
        $theClient['email'] = trim($data['email']);
    if(strlen($data['phonenumber']) >= 7 && strlen($data['phonenumber']) <= 10)
        $theClient['phonenumber'] = trim($data['phonenumber']);
    
    $theClient['cash_desk_users_idUsers'] = $this->userId;

    $clientDb->createClient($theClient); 
    if(isset($data['type'])){
        unset($data['type']);
        return echoRespnse(201, array($data), $response);
    }else
        return $response->withRedirect('/client'); 

})->add($client_autorization);


$app->get('/client/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $idClient = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ClientMapper.php';
    $clientDb = new ClientMapper();
    
    if(!$clientDb->isClientToUser($this->userId, $idClient))
        return $response->withRedirect('/client');

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "champs" => $clientDb->valeurChampsClient($idClient),
        "type" => array("email" => "email", "phonenumber" => "tel", "created" => "datetime"),
        "client" => $clientDb->searchClient(array("cash_desk_users_idUsers" => $this->userId, "idClients" => $idClient))[0],
    );
    // var_dump($args);
    return $this->renderer->render($response, 'form/editClient.phtml', $args);

})->add($client_autorization);


$app->post('/client/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $idClient = $route->getArgument('id');

    $data = $request->getParsedBody();

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ClientMapper.php';
    $clientDb = new ClientMapper();

    if(!$clientDb->isClientToUser($this->userId, $idClient))
        return $response->withRedirect('/client');


    $theClient = array();
    if(strlen($data['firstname']) >= 2 && strlen($data['firstname']) <= 44)
        $theClient['firstname'] = trim($data['firstname']);
    else{
        $error_fields .= 'nom, ';
        $error = true;
    }if(strlen($data['lastname']) >= 2 && strlen($data['lastname']) <= 44)
        $theClient['lastname'] = trim($data['lastname']);
    else{
        $error_fields .= 'lastname, ';
        $error = true;
    }if(strlen($data['postcode']) == 5)
        $theClient['postcode'] = trim($data['postcode']);
    else{
        $error_fields .= 'postcode, ';
        $error = true;
    }if(validateEmail($data['email']) && strlen($data['email']) >= 10)
        $theClient['email'] = trim($data['email']);
    if(strlen($data['phonenumber']) >= 7 && strlen($data['phonenumber']) <= 10)
        $theClient['phonenumber'] = trim($data['phonenumber']);
    
    $champs = $clientDb->valeurChampsClient($idClient);

    $clientLiaisson = array();

    foreach($champs as $champ)
        if(isset($data[$champ["libelle"]]))
            array_push($clientLiaisson, array($champ["idchamps"] => trim($data[$champ["libelle"]])));

    $clientDb->updateClient($theClient, array("idClients" => $idClient));
    if(count($clientLiaisson) >= 1)
        $clientDb->updateClientChampsPerso($clientLiaisson, $idClient);

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "champs" => $champs,
        "type" => array("email" => "email", "phonenumber" => "tel", "created" => "datetime"),
        "client" => $clientDb->searchClient(array("cash_desk_users_idUsers" => $this->userId, "idClients" => $idClient))[0],
    );
    // var_dump($champs);
    return $this->renderer->render($response, 'form/editClient.phtml', $args);

})->add($client_autorization);

$app->get('/client-delete/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $clientId = $route->getArgument('id');

    $data = $request->getParsedBody();
    
    require_once __DIR__ . '/../../Mapper/ClientMapper.php';
    $clientDb = new ClientMapper();

    if(!$clientDb->isClientToUser($this->userId, $clientId))
        return $response->withRedirect('/dashboard');

    $clientDb->updateClient(array("isDelete" => 1), array("idClients" => $clientId));
    
    return $response->withRedirect('/client'); 

})->add($client_autorization);