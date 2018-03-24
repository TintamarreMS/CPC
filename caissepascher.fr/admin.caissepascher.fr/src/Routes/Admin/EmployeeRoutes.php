<?php

$app->get('/employee', function ($request, $response, $args) {
    
    require_once __DIR__ . '/../../Mapper/EmployeeMapper.php';
    $employeeDb = new EmployeeMapper();
    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "employees" => $employeeDb->searchEmployee(array("cash_desk_users_idUsers"=>$this->userId)),
    );
    // var_dump($args);
    return $this->renderer->render($response, 'liste/employee.phtml', $args);

})->add($client_autorization);


$app->get('/employee/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $employeeId = $route->getArgument('id');

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/EmployeeMapper.php';
    $employeeDb = new EmployeeMapper();
    
    if(!$employeeDb->isEmployeeToUser($this->userId, $employeeId))
        return $response->withRedirect('/dashboard');

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "employee" => $employeeDb->searchEmployee(array("cash_desk_users_idUsers" => $this->userId, "idEmployee" => $employeeId))[0],
    );
    // var_dump($args);
    return $this->renderer->render($response, 'form/editEmployee.phtml', $args);

})->add($client_autorization);


$app->post('/employee/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $employeeId = $route->getArgument('id');

    $data = $request->getParsedBody();

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/EmployeeMapper.php';
    $employeeDb = new EmployeeMapper();

    if(!$employeeDb->isEmployeeToUser($this->userId, $employeeId))
        return $response->withRedirect('/dashboard');


    $theEmployee = array();
    if(strlen($data['firstname']) >= 2 && strlen($data['firstname']) <= 44)
        $theEmployee['firstname'] = trim($data['firstname']);
    else{
        $error_fields .= 'nom, ';
        $error = true;
    }if(strlen($data['lastname']) >= 2 && strlen($data['lastname']) <= 44)
        $theEmployee['lastname'] = trim($data['lastname']);
    else{
        $error_fields .= 'lastname, ';
        $error = true;
    }
    $theEmployee['isActive'] = $data['isActive'];

    $employeeDb->updateEmployee($theEmployee, array("idEmployee" => $employeeId));
    

    $args = array(
        "user" => $userDb->selectUsers($this->userId)[0],
        "employee" => $employeeDb->searchEmployee(array("cash_desk_users_idUsers" => $this->userId, "idEmployee" => $employeeId))[0],
    );
    // var_dump($data);
    return $this->renderer->render($response, 'form/editEmployee.phtml', $args);

})->add($client_autorization);

$app->get('/employee-delete/{id}', function ($request, $response, $args) {
    $route = $request->getAttribute('route');
    $employeeId = $route->getArgument('id');

    $data = $request->getParsedBody();

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/EmployeeMapper.php';
    $employeeDb = new EmployeeMapper();

    if(!$employeeDb->isEmployeeToUser($this->userId, $employeeId))
        return $response->withRedirect('/dashboard');

    $employeeDb->updateEmployee(array("isDelete" => 1), array("idEmployee" => $employeeId));
    

    $employeeDb->createEmployee($theEmployee); 
        return $response->withRedirect('/employee'); 

})->add($client_autorization);





$app->get('/employee-add', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/EmployeeMapper.php';
    $employeeDb = new EmployeeMapper();

    return $this->renderer->render($response, 'form/addEmployee.phtml', $args);

})->add($client_autorization);





$app->post('/employee-add', function ($request, $response, $args) {

    require_once __DIR__ . '/../../Mapper/UsersMapper.php';
    $userDb = new UsersMapper();
    require_once __DIR__ . '/../../Mapper/EmployeeMapper.php';
    $employeeDb = new EmployeeMapper();

    $data = $request->getParsedBody();


    $theEmployee = array();
    if(strlen($data['firstname']) >= 2 && strlen($data['firstname']) <= 44)
        $theEmployee['firstname'] = trim($data['firstname']);
    else{
        $error_fields .= 'nom, ';
        $error = true;
    }if(strlen($data['lastname']) >= 2 && strlen($data['lastname']) <= 44)
        $theEmployee['lastname'] = trim($data['lastname']);
    else{
        $error_fields .= 'lastname, ';
        $error = true;
    }
    $theEmployee['isActive'] = $data['isActive'];
    
    $theEmployee['cash_desk_users_idUsers'] = $this->userId;

    $employeeDb->createEmployee($theEmployee); 
        return $response->withRedirect('/employee'); 

})->add($client_autorization);