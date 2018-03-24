<?php

$app->get('/account-list', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                  "accounts" => $userDb->searchMyUsers($this->userId));

  
    // var_dump($args);
    return $this->renderer->render($response, 'liste/account-list.phtml', $args);

})->add($client_autorization);



$app->get('/account-add', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    return $this->renderer->render($response, 'form/addAccount.phtml', $args);

})->add($client_autorization);





$app->post('/account-add', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    require_once __DIR__ . '/../../mail/class.phpmailer.php';
    
    $userDb = new UsersMapper();
    $mail   = new PHPMailer(true);
    

    $data = $request->getParsedBody();
    
   
    $theAdmin = array();
    $theUser  = array();
    
    $theUser['firstname'] = trim($data['firstname']);
    $theUser['lastname']  = trim($data['lastname']);
    $theUser['email']     = trim($data['email']);
    $theUser['username']  = trim($data['login']);
    $theUser['pass']      = trim($data['pass']);

    $theAdmin =  $userDb->selectUsers($this->userId)[0]; 
    
    $result = $userDb->CreateUserAdmin($theUser,$this->userId,$theAdmin,$mail);
    
    
    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                  "accounts" => $userDb->searchMyUsers($this->userId));
    
    return $this->renderer->render($response, 'liste/account-list.phtml', $args);
    

})->add($client_autorization);




$app->get('/account-activate/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $IdUser = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    
    $userDb->ActivateUser($IdUser);
    
    
    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                  "accounts" => $userDb->searchMyUsers($this->userId));

    return $this->renderer->render($response, 'liste/account-list.phtml', $args);

})->add($client_autorization);



$app->get('/account-disactivate/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $IdUser = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    
    $userDb->DisactivateUser($IdUser);
    
    
    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                  "accounts" => $userDb->searchMyUsers($this->userId));

    return $this->renderer->render($response, 'liste/account-list.phtml', $args);

})->add($client_autorization);





$app->get('/account-delete/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $IdUser = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    
    $userDb->DeleteUser($IdUser);
    
    
    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                  "accounts" => $userDb->searchMyUsers($this->userId));

   return $this->renderer->render($response, 'liste/account-list.phtml', $args);


})->add($client_autorization);



$app->get('/account/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $userIdent = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    

  
    if(!$userDb->isPropertyToUser($userIdent,$this->userId))
        return $response->withRedirect('/dashboard');
  
    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                  "accounts" => $userDb->ReturnUser($userIdent)[0]);
    

    return $this->renderer->render($response, 'form/editAccount.phtml', $args);


})->add($client_autorization);



$app->post('/account/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $userIdent = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    
     $data = $request->getParsedBody();
  
    if(!$userDb->isPropertyToUser($userIdent,$this->userId))
        return $response->withRedirect('/dashboard');

    $theUser  = array();
    
    $theUser['iduser']   = $userIdent;
    $theUser['firstname'] = trim($data['firstname']);
    $theUser['lastname']  = trim($data['lastname']);
    $theUser['email']     = trim($data['email']);
    $theUser['username']  = trim($data['login']);
    $theUser['pass']      = trim($data['pass']);     
        
    $res = $userDb->UpdateUserAdmin($theUser);
    
    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                  "accounts" => $userDb->searchMyUsers($this->userId));
    
    return $this->renderer->render($response, '/liste/account-list.phtml', $args);


})->add($client_autorization);



