<?php


$app->get('/register', function ($request, $response, $args) {
    
        require_once __DIR__ . '/../../Mapper/UsersMapper.php';
        $userDb = new UsersMapper();

    
        $args = array("user" => $userDb->selectUsers($this->userId)[0]);
        // var_dump($args);
        return $this->renderer->render($response, 'register.phtml', $args);
    
    })->add($client_autorization);







$app->get('/register-list', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                  "clients" => $userDb->searchMyUsers($this->userId));

  
    // var_dump($args);
    return $this->renderer->render($response, 'liste/register-list.phtml', $args);

})->add($client_autorization);





$app->get('/register-activate/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $idClient = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    
    $userDb->ActivateUser($idClient);
    
    
    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                  "clients" => $userDb->searchMyUsers($this->userId));

    return $this->renderer->render($response, 'liste/register-list.phtml', $args);

})->add($client_autorization);



$app->get('/register-disactivate/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $idClient = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    
    $userDb->DisactivateUser($idClient);
    
    
    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                  "clients" => $userDb->searchMyUsers($this->userId));

    return $this->renderer->render($response, 'liste/register-list.phtml', $args);

})->add($client_autorization);



$app->get('/register-delete/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $idClient = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    
    $userDb->DeleteUser($idClient);
    
    
    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                  "clients" => $userDb->searchMyUsers($this->userId));

    return $this->renderer->render($response, 'liste/register-list.phtml', $args);

})->add($client_autorization);





$app->post('/register', function ($request, $response, $args) {
    
    $data = $request->getParsedBody();
    
    $id_user = $this->userId[0];
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    require_once __DIR__ . '/../../mail/class.phpmailer.php';
    
    $userDb = new UsersMapper();
    $mail   = new PHPMailer(true);

    
    $result = $userDb->registerUserAdmin($data,$id_user,$mail);
    
    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                   "clients" => $userDb->searchMyUsers($this->userId));
    
    return $this->renderer->render($response, 'liste/register-list.phtml', $args);

    
})->add($client_autorization);




$app->get('/profil', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    $args = array("user" => $userDb->selectUsers($this->userId)[0]);

    return $this->renderer->render($response, 'liste/register-profil.phtml', $args);

})->add($client_autorization);




$app->post('/profil', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    
    $data = $request->getParsedBody();
    
    $theUser = array();
    
    $theUser['username'] = trim($data['login']);
    $theUser['pass']     = trim($data['pass']);
    
    
    $id_user = $this->userId[0];

    
    $result = $userDb->UpdateUser($theUser,$id_user);
    
  

    $args = array("user" => $userDb->selectUsers($this->userId)[0],
                   "clients" => $userDb->searchMyUsers($this->userId));
    
    return $this->renderer->render($response, 'liste/register-list.phtml', $args);
 
  

})->add($client_autorization);



