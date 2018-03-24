<?php


$app->get('/transaction-csv', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();
    
    $verif = array();
    $modes = array();


    $transaction = $transactionDb->PrintTransaction($this->userId,$_SESSION["extract"]);
 
    $tmpName = tempnam(sys_get_temp_dir(), "transaction");
    $file = fopen($tmpName, 'w');
    $arrayT =  array('Id Transaction',
                     'Id Client',
                     'Nom Client',
                     'Email Client',
                     'Tél Client', 
                     'Statut', 
                     'Annulée',
                     'Créée', 
                     'Maj',
                     'Employé',
                     'Total',
                     'Id Produit',
                     'Nom Produit',
                     'Prix Produit',
                     'Moyen de paiement',
                     'Montant');
    
    fputcsv($file, $arrayT , ";");
  
    foreach ($transaction as $line => $val) 
    {
       
              if($val['stat'] == 'Annuler') $val['stat']  = 'Annulée';
              if($val['stat'] == 'Terminer') $val['stat'] = 'Terminée';
              if($val['canc'] == '0') $val['canc'] = 'Non';
              if($val['canc'] == '1') $val['canc'] = 'Oui';
              
              $val['pdt_nom'] = utf8_decode($val['pdt_nom']);
              $val['lab']     = utf8_decode($val['lab']);
         
         
         if (!in_array($val['id'], $verif)) 
         {
              $verif[] = $val['id'];
              $mods[$val['id']][] = $val['id_p']; 
              
              $val['id_p'] = '';
              fputcsv($file, $val,";");
         } 
         else
         {
             if (!in_array($val['id_p'], $mods[$val['id']])) 
             {
               $temp = array('','','','','','','','','','','',$val['pdt'],$val['pdt_nom'],$val['pdt_price'],$val['lab'],$val['p_p']); 
               $mods[$val['id']][] = $val['id_p'];  
             }
             
             else  $temp = array('','','','','','','','','','','',$val['pdt'],$val['pdt_nom'],$val['pdt_price'],'','');
            
             fputcsv($file, $temp,";");
         }
     
    }

  
    fclose($file);
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename=transaction.csv');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($tmpName));
    
    ob_clean();
    flush();
    readfile($tmpName);
    
    unlink($tmpName);


})->add($client_autorization);




$app->get('/transaction-text', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();

    $transaction = $transactionDb->PrintTransaction($this->userId,$_SESSION["extract"]);
    
   $verif = array();
    $modes = array();


    $transaction = $transactionDb->PrintTransaction($this->userId,$_SESSION["extract"]);
 
    $tmpName = tempnam(sys_get_temp_dir(), "transaction");
    $file = fopen($tmpName, 'w');
    $arrayT =  array('Id Transaction',
                     'Id Client',
                     'Nom Client',
                     'Email Client',
                     'Tél Client', 
                     'Statut', 
                     'Annulée',
                     'Créée', 
                     'Maj',
                     'Employé',
                     'Total',
                     'Id Produit',
                     'Nom Produit',
                     'Prix Produit',
                     'Moyen de paiement',
                     'Montant');
    
    fputcsv($file, $arrayT , ";");
  
    foreach ($transaction as $line => $val) 
    {
       
              if($val['stat'] == 'Annuler') $val['stat']  = 'Annulée';
              if($val['stat'] == 'Terminer') $val['stat'] = 'Terminée';
              if($val['canc'] == '0') $val['canc'] = 'Non';
              if($val['canc'] == '1') $val['canc'] = 'Oui';
              
              $val['pdt_nom'] = utf8_decode($val['pdt_nom']);
              $val['lab']     = utf8_decode($val['lab']);
         
         
         if (!in_array($val['id'], $verif)) 
         {
              $verif[] = $val['id'];
              $mods[$val['id']][] = $val['id_p']; 
              
              $val['id_p'] = '';
              fputcsv($file, $val,";");
         } 
         else
         {
             if (!in_array($val['id_p'], $mods[$val['id']])) 
             {
               $temp = array('','','','','','','','','','','',$val['pdt'],$val['pdt_nom'],$val['pdt_price'],$val['lab'],$val['p_p']); 
               $mods[$val['id']][] = $val['id_p'];  
             }
             
             else  $temp = array('','','','','','','','','','','',$val['pdt'],$val['pdt_nom'],$val['pdt_price'],'','');
            
             fputcsv($file, $temp,";");
         }
     
    }

  
    fclose($file);
    
    header('Content-Description: File Transfer');
    header('Content-Type: text/txt; charset=utf-8');
    header('Content-Disposition: attachment; filename=transaction.txt');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($tmpName));
    
    ob_clean();
    flush();
    readfile($tmpName);
    
    unlink($tmpName);

})->add($client_autorization);






$app->get('/transaction', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();

    $_SESSION["transaction"] = $transactionDb->searchTransaction(array("transaction.cash_desk_users_idUsers" => $this->userId));
    $_SESSION["extract"]= "";

    $args = array(
        "user" => $userDb->selectUsers($this->userId[0]),
        "title" => "Transactions - Toutes",
        "transactions" => $_SESSION["transaction"],
    );
    return $this->renderer->render($response, 'liste/transaction.phtml', $args);

})->add($client_autorization);

$app->get('/paymentMean', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();

    $data = $transactionDb->paymentMean();

    return echoRespnse(200, array("paymentMeans"=>$data), $response);

});

$app->post('/transaction', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();

    $data = $request->getParsedBody();

    $data["cash_desk_users_idUsers"] =  $this->userId;
    $data["status"] =  "Terminer";

    $id = $transactionDb->createTransaction(array("cash_desk_users_idUsers" => $data["cash_desk_users_idUsers"], "status" => $data["status"], "amount" => $data["amount"], "employee_idEmployee" => $data["employee_idEmployee"]));

    $payments = json_decode($data["payments"]);
    $transactionDb->transactionHasPayments($id, $payments);

    return echoRespnse(200, array("success"=>true), $response);

})->add($client_autorization);;

$app->post('/transaction/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $transactionId = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();

    $where = array("cash_desk_users_idUsers" => $this->userId, "reference_client" => $transactionId);
    $data = $transactionDb->DemandeCancelTransaction($where);

    return echoRespnse(200, array("success"=>true), $response);
})->add($client_autorization);;

$app->post('/transactions/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $transactionId = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();

    $where = array("cash_desk_users_idUsers" => $this->userId, "reference_client" => $transactionId);
    $data = $transactionDb->cancelTransaction($where);

    return echoRespnse(200, array("success"=>true), $response);
})->add($client_autorization);;

$app->get('/transaction-cancel', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();

    $_SESSION["transaction"] = $transactionDb->searchTransaction(array("transaction.cash_desk_users_idUsers" => $this->userId, "status" => "Annuler", "cancelled" => 1));
     $_SESSION["extract"]= "Annuler";

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "title" => "Transactions - Annul&eacute;es",
        "transactions" => $_SESSION["transaction"],
    );
    return $this->renderer->render($response, 'liste/transaction.phtml', $args);

})->add($client_autorization);

$app->get('/transaction-end', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();

    $_SESSION["transaction"] = $transactionDb->searchTransaction(array("transaction.cash_desk_users_idUsers" => $this->userId, "status" => "Terminer"));
     $_SESSION["extract"]= "Terminer";

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "title" => "Transactions - Valid&eacute;es",
        "transactions" => $_SESSION["transaction"],
    );
    return $this->renderer->render($response, 'liste/transaction.phtml', $args);

})->add($client_autorization);

$app->get('/transaction-InProgress', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();

    $_SESSION["transaction"] = $transactionDb->searchTransaction(array("transaction.cash_desk_users_idUsers" => $this->userId, "status" => "En cours"));

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "title" => "Transactions - En cours",
        "transactions" => $_SESSION["transaction"],
    );
    return $this->renderer->render($response, 'liste/transaction.phtml', $args);

})->add($client_autorization);

$app->get('/transaction-nb', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/TransactionMapper.php';
    $transactionDb = new TransactionMapper();

    $data = $transactionDb->numTransactionToUser($this->userId);

    return echoRespnse(200, array("newTransactionID"=>$data+1), $response);

})->add($caisse_autorization);;