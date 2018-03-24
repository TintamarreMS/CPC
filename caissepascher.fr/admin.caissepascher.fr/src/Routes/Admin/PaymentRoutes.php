<?php

$app->get('/payment', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/PaymentMapper.php';
    $paymentDb = new PaymentMapper();
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "payments" => $paymentDb->searchPayment(array("Id_User"=>$this->userId)),
    );
    // var_dump($args);
    return $this->renderer->render($response, 'liste/payment.phtml', $args);

})->add($client_autorization);



$app->get('/payment/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $paymentId = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/PaymentMapper.php';
    $paymentDb = new PaymentMapper();
    
    if(!$paymentDb->isPaymentToUser($this->userId, $paymentId))
        return $response->withRedirect('/dashboard');

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "payment" => $paymentDb->searchPayment(array("Id_User" => $this->userId, "UserConfigs_has_PaymentMean_Id" => $paymentId))[0],
    );
    // var_dump($args);
    return $this->renderer->render($response, 'form/editPayment.phtml', $args);

})->add($client_autorization);




$app->post('/payment/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $paymentId = $route->getArgument('id');

    $data = $request->getParsedBody();

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/PaymentMapper.php';
    $paymentDb = new PaymentMapper();

    if(!$paymentDb->isPaymentToUser($this->userId, $paymentId))
        return $response->withRedirect('/dashboard');


    $thePayment = array();
    if(strlen($data['PaymentLabel']) >= 2 && strlen($data['PaymentLabel']) <= 44)
        $thePayment['PaymentLabel'] = trim(utf8_encode($data['PaymentLabel']));
    else{
        $error_fields .= 'Moyen de paiement, ';
        $error = true;
    }

    $paymentDb->updatePayment($thePayment, array("UserConfigs_has_PaymentMean_Id" => $paymentId));
    

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "payment" => $paymentDb->searchPayment(array("Id_User" => $this->userId, "UserConfigs_has_PaymentMean_Id" => $paymentId))[0],
    );
    // var_dump($data);
    return $this->renderer->render($response, 'form/editPayment.phtml', $args);

})->add($client_autorization);



$app->get('/payment-add', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/PaymentMapper.php';
    $paymentDb = new PaymentMapper();

    return $this->renderer->render($response, 'form/addPayment.phtml', $args);

})->add($client_autorization);




$app->post('/payment-add', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/PaymentMapper.php';
    $paymentDb = new PaymentMapper();

    $data = $request->getParsedBody();


    $thePayment = array();
    if(strlen($data['PaymentLabel']) >= 2 && strlen($data['PaymentLabel']) <= 44 && ($data['PaymentLabel'] !=""))
    {
        $thePayment['PaymentLabel'] = trim(utf8_encode($data['PaymentLabel']));
        
        $thePayment['Id_User'] = $this->userId;

        $paymentDb->createPayment($thePayment); 
        return $response->withRedirect('/payment'); 
    }
        
        
    else{
        $error_fields .= 'Moyen de paiement, ';
        $error = true;
        
        return $response->withRedirect('/payment'); 
    }
    
    

})->add($client_autorization);





$app->get('/payment-delete/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $paymentId = $route->getArgument('id');

    $data = $request->getParsedBody();

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/PaymentMapper.php';
    $paymentDb = new PaymentMapper();

    if(!$paymentDb->isPaymentToUser($this->userId, $paymentId))
        return $response->withRedirect('/dashboard');

     $paymentDb->dbDelete("UserConfigs_has_PaymentMean",array("UserConfigs_has_PaymentMean_Id" => $paymentId));
    
    return $response->withRedirect('/payment');  

})->add($client_autorization);




