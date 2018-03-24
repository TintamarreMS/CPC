<?php


$app->get('/products', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/ProductMapper.php';
    $productDb = new ProductMapper();

    return echoRespnse(200, $productDb->searchProducts(array("product_or_service.cash_desk_users_idUsers" => $this->userId)), $response);
})->add($caisse_autorization);



$app->get('/product', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ProductMapper.php';
    $productDb = new ProductMapper();

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "products" => $productDb->searchProducts(array("product_or_service.cash_desk_users_idUsers" => $this->userId)),
    );
    // var_dump($args);
    return $this->renderer->render($response, 'liste/product.phtml', $args);

})->add($client_autorization);


$app->get('/product/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $productId = $route->getArgument('id');
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ProductMapper.php';
    $productDb = new ProductMapper();
    
    if(!$productDb->isProductToUser($this->userId, $productId))
        return $response->withRedirect('/product');

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "categories" => $productDb->searchCategorie($this->userId),
        "product" => $productDb->searchProducts(array("product_or_service.cash_desk_users_idUsers" => $this->userId, "idProduct_Services" => $productId))[0],
    );
    // var_dump($args);
    return $this->renderer->render($response, 'form/editProduct.phtml', $args);
})->add($client_autorization);


$app->post('/product/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $productId = $route->getArgument('id');

    $data = $request->getParsedBody();

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ProductMapper.php';
    $productDb = new ProductMapper();

    if(!$productDb->isProductToUser($this->userId, $productId))
        return $response->withRedirect('/product');


    $theProduct = array();
    $theProduct['label'] = trim($data['label']);
    $theProduct['preTaxPrice'] = floatval($data['Prix']);
    $theProduct['VAT'] = floatval($data['Tax']);
    $theProduct['isActive'] = (isset($data['isActive']))?$data['isActive']:0;
    $theProduct['categories_idcategories'] = (isset($data['idcategories']))?$data['idcategories']:0;

    $productDb->updateProduct($theProduct, array("idProduct_Services" => $productId));
    

    return $response->withRedirect('/product'); 

})->add($client_autorization);

$app->get('/product-add', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ProductMapper.php';
    $productDb = new ProductMapper();

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "categories" => $productDb->searchCategorie($this->userId)
    );

    return $this->renderer->render($response, 'form/addProduct.phtml', $args);

})->add($client_autorization);






$app->post('/product-add', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ProductMapper.php';
    $productDb = new ProductMapper();

    $data = $request->getParsedBody();


    $theProduct = array();
    $theProduct['label'] = trim($data['label']);
    $theProduct['preTaxPrice'] = trim($data['preTaxPrice']);
    $theProduct['VAT'] = trim($data['VAT']);
    $theProduct['label'] = trim($data['label']);
    $theProduct['isActive'] = $data['isActive'];
    $theProduct['categories_idcategories'] = $data['idcategories'];
    $theProduct['cash_desk_users_idUsers'] = $this->userId;

    $productDb->createProduct($theProduct); 
        return $response->withRedirect('/product'); 

})->add($client_autorization);



$app->get('/product-activate/{id}', function ($request, $response, $args) {

    $route = $request->getAttribute('route');
    $productId = $route->getArgument('id');
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ProductMapper.php';
    $productDb = new ProductMapper();
    
    if(!$productDb->isProductToUser($this->userId, $productId))
    return $response->withRedirect('/dashboard');

    $productDb->activateProduct($productId);

     $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "products" => $productDb->searchProducts(array("product_or_service.cash_desk_users_idUsers" => $this->userId)),
    );
    // var_dump($args);
    return $this->renderer->render($response, 'liste/product.phtml', $args);

})->add($client_autorization);





$app->get('/product-disactivate/{id}', function ($request, $response, $args) {

    $route = $request->getAttribute('route');
    $productId = $route->getArgument('id');
    
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ProductMapper.php';
    $productDb = new ProductMapper();
    
    if(!$productDb->isProductToUser($this->userId, $productId))
    return $response->withRedirect('/dashboard');

    $productDb->desactivateProduct($productId);

     $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "products" => $productDb->searchProducts(array("product_or_service.cash_desk_users_idUsers" => $this->userId)),
    );
    // var_dump($args);
    return $this->renderer->render($response, 'liste/product.phtml', $args);

})->add($client_autorization);





$app->get('/product-delete/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $productId = $route->getArgument('id');

    $data = $request->getParsedBody();

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/ProductMapper.php';
    $productDb = new ProductMapper();

    if(!$productDb->isProductToUser($this->userId, $productId))
        return $response->withRedirect('/dashboard');

    $productDb->dbDelete("product_or_service",array("idProduct_Services" => $productId));
    
    return $response->withRedirect('/product'); 

})->add($client_autorization);
