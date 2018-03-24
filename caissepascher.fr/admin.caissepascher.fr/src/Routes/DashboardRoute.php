<?php

$app->get('/test', function ($request, $response, $args) {
    // isClientToUser
    $this->userId = 1;

    require_once __DIR__ . '/../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    require_once __DIR__ . '/../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();
    require_once __DIR__ . '/../Mapper/CategorieMapper.php';
    $categoriesDb = new CategorieMapper();

    // $data = $transactionDb->searchTransactionDate($this->userId, true, array(date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:59:59"));
    // $data = $transactionDb->searchTransactionDate(array($this->userId, "%".$transactionDb->DateTime(false)."%"), true)[0]["total"];
    // $data = $transactionDb->searchTransaction(array("transaction.cash_desk_users_idUsers" => 1, "transaction.created" => "'%".$transactionDb->DateTime(false)."%'"));
    $data = $userDb->password("stephanemonatadmin");
    // $data = $userDb->checkPassword(1, "Mike22", "administrator");
    // $data = $userDb->selectUsersToken(1, "administrator");
    // $data = $userDb->DateTime(false);
    // $data = date("Y-m-d")." 00:00:00";
    // $data = $transactionDb->transacticonHasPayments($this->userId,  array(date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:59:59"), 2);
    // $data = $categoriesDb->searchCategorie(array("cash_desk_users_idUsers"=>$this->userId, "children" => 0));
    
    return echoRespnse(200, array($data), $response);
});

$app->get('/test-connect', function ($request, $response, $args) {
    // isClientToUser

    require_once __DIR__ . '/../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    $data = $userDb->password("mike22");

    return echoRespnse(200, array("error"=>false, "data"=>$data), $response);
})->add($client_autorization)->add($client_autorization);

$app->get('/logout', function ($request, $response, $args) {
    setcookie('token', NULL, -1);
    return $response->withRedirect('/');
})->add($client_autorization);

$app->get('/config', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "paymentMeans" => $userDb->MymoyenPayment($this->userId),
        "theUser" => $userDb->selectUsersAll($this->userId)[0],
    );
    // var_dump($args);
    return $this->renderer->render($response, 'config.phtml', $args);
})->add($client_autorization);

$app->post('/config', function ($request, $response, $args) {

    require_once __DIR__ . '/../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    $data = $request->getParsedBody();
    
    $userDb->updateUsers($data, array("idUsers" => $this->userId));
    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "paymentMeans" => $userDb->moyenPayment(),
        "theUser" => $userDb->selectUsersAll($this->userId)[0],
    );
    // var_dump($data);
    return $this->renderer->render($response, 'config.phtml', $args);
})->add($client_autorization);

$app->get('/dashboard', function ($request, $response, $args) {
    
        require_once __DIR__ . '/../Mapper/UsersMapper.php';
        $userDb = new UsersMapper();
        require_once __DIR__ . '/../Mapper/ClientMapper.php';
        $clientDb = new ClientMapper();
        require_once __DIR__ . '/../Mapper/TransactionMapper.php';
        $transactionDb = new TransactionMapper();
        require_once __DIR__ . '/../Mapper/ProductMapper.php';
        $productDb = new ProductMapper();
    
        $args = array(
            "nbClient" => $clientDb->nbClientToUser($this->userId)[0]["Count(*)"],
            "nbProduict" => $productDb->nbProductToUser($this->userId)[0]["Count(*)"],
            "nbTransactionFinish" => $transactionDb->nbTransactionToUser($this->userId, 2)[0]["total"],
            "nbTransactionCancel" => $transactionDb->nbTransactionToUser($this->userId, 3)[0]["total"],
            "sumTransactionToday" => $transactionDb->searchTransactionDate($this->userId, true, array(date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:59:59"))[0]["total"],
            "user" => $userDb->selectUsers($this->userId)[0],
        );
        // var_dump($args);
        return $this->renderer->render($response, 'dashboard.phtml', $args);
    
    })->add($client_autorization);

$app->get('/dailyReport', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../Mapper/ClientMapper.php';
    $clientDb = new ClientMapper();
    require_once __DIR__ . '/../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();

    $args = array(
        "totalAmount" => $transactionDb->searchTransactionDate($this->userId, true, array(date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:59:59"))[0]["total"],
        "paymentsBreakdown" => $transactionDb->transacticonHasPayments($this->userId,  array(date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:59:59"), 2),
        "employeeBreakdown" => $transactionDb->transacticonHasPayments($this->userId,  array(date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:59:59"), 1),
        "categoryBreakdown" => Array(),
    );
    
    return echoRespnse(200, $args, $response); 

})->add($caisse_autorization);

require_once __DIR__ . '/Admin/ClientRoutes.php';

require_once __DIR__ . '/Admin/EmployeeRoutes.php';

require_once __DIR__ . '/Admin/ProductRoutes.php';

require_once __DIR__ . '/Admin/CategorieRoutes.php';

require_once __DIR__ . '/Admin/TransactionRoutes.php';

require_once __DIR__ . '/Admin/PaymentRoutes.php';

require_once __DIR__ . '/Admin/AccountRoutes.php';
