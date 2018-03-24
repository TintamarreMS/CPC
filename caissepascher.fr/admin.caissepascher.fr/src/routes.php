<?php

$app->get('/', function ($request, $response, $args) {
    
    if(isset($_COOKIE["token"]) || !empty($_COOKIE["token"]))
        return $response->withRedirect('/dashboard');

    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});



require_once __DIR__ . '/Routes/DashboardRoute.php';
require_once __DIR__ . '/Routes/Admin/RegisterRoutes.php';
require_once __DIR__ . '/Routes/UsersRoutes.php';