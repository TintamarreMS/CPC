<?php

$app->get('/categories', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/CategorieMapper.php';
    $categoriesDb = new CategorieMapper();

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "categories" => $categoriesDb->searchCategorie(array("cash_desk_users_idUsers"=>$this->userId)),
    );
    // var_dump($args);
    return $this->renderer->render($response, 'liste/categories.phtml', $args);

})->add($client_autorization);





$app->get('/categories/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $categoriesId = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/CategorieMapper.php';
    $categoriesDb = new CategorieMapper();
    
    if(!$categoriesDb->isCategorieToUser($this->userId, $categoriesId))
        return $response->withRedirect('/categories');

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "categories" => $categoriesDb->searchCategorie(array("cash_desk_users_idUsers" => $this->userId, "idcategories" => $categoriesId))[0],
    );
    // var_dump($args);
    return $this->renderer->render($response, 'form/editCategories.phtml', $args);

})->add($client_autorization);



$app->get('/categories-delete/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $categoriesId = $route->getArgument('id');

    $data = $request->getParsedBody();

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/CategorieMapper.php';
    $categoriesDb = new CategorieMapper();

    if(!$categoriesDb->showsCategorieByUser($this->userId, $categoriesId))
        return $response->withRedirect('/categories');

    $categoriesDb->updateCategorie(array("isdelete" => 1), array("idcategories" => $categoriesId));
    
    return $response->withRedirect('/categories');

})->add($client_autorization);




$app->post('/categories/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $categoriesId = $route->getArgument('id');

    $data = $request->getParsedBody();

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/CategorieMapper.php';
    $categoriesDb = new CategorieMapper();

    if(!$categoriesDb->showsCategorieByUser($this->userId, $categoriesId))
        return $response->withRedirect('/categories');
    
    $thecat = array();
    $thecat['label'] = trim($data['label']);
    
        
        
    $categoriesDb->updateCategorie(array("label" => $thecat['label']), array("idcategories" => $categoriesId));


   /* 
    
    $thecategories = array();
    if(strlen($data['firstname']) >= 3 && strlen($data['firstname']) <= 44)
        $thecategories['firstname'] = trim($data['firstname']);
    else{
        $error_fields .= 'nom, ';
        $error = true;
    }if(strlen($data['lastname']) >= 3 && strlen($data['lastname']) <= 44)
        $thecategories['lastname'] = trim($data['lastname']);
    else{
        $error_fields .= 'lastname, ';
        $error = true;
    }
    $thecategories['isActive'] = $data['isActive'];

    $categoriesDb->updateCategorie($thecategories, array("idcategories" => $categoriesId));
    

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "categories" => $categoriesDb->searchCategorie(array("cash_desk_users_idUsers" => $this->userId, "idcategories" => $categoriesId))[0],
    );
    // var_dump($data);
    return $this->renderer->render($response, 'form/editCategories.phtml', $args);
    */
    
    return $response->withRedirect('/categories');
    

})->add($client_autorization);


$app->get('/categories-add', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/CategorieMapper.php';
    $categoriesDb = new CategorieMapper();

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "categories" => $categoriesDb->searchCategorie(array("cash_desk_users_idUsers"=>$this->userId, "children" => 0)),
    );
    return $this->renderer->render($response, 'form/addCategories.phtml', $args);

})->add($client_autorization);


$app->post('/categories-add', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/CategorieMapper.php';
    $categoriesDb = new CategorieMapper();

    $data = $request->getParsedBody();


    $thecategories = array();
    $thecategories['label'] = $data['label'];
    
    $thecategories['cash_desk_users_idUsers'] = $this->userId;


    $categoriesDb->createCategorie(array('label'=>$data['label'],'cash_desk_users_idUsers'=>$this->userId)); 

    return $response->withRedirect('/categories'); 

})->add($client_autorization);


