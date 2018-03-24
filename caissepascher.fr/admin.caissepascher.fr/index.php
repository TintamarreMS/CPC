<?php
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

    date_default_timezone_set( 'Europe/Paris' );
    if (PHP_SAPI == 'cli-server') {
        // To help the built-in PHP dev server, check if the request was actually for
        // something which should probably be served as a static file
        $url  = parse_url($_SERVER['REQUEST_URI']);
        $file = __DIR__ . $url['path'];
        if (is_file($file)) {
            return false;
        }
    }

    $retour = null;
    global $userId;

    require_once __DIR__ . '/vendor/autoload.php';

    \Stripe\Stripe::setApiKey('sk_test_u1K2I7h3trhklhL5PVLUK3W2');

    session_start();

    // Instantiate the app
    $settings = require_once __DIR__ . '/src/settings.php';
    $app = new \Slim\App($settings);

    // Set up constant
    require_once __DIR__ . '/src/include/Const.php';

    // Set up SimplifiedDBPDO
    require __DIR__ . '/src/include/SimplifiedDBPDO.php';

    // Set up PassHash
    require __DIR__ . '/src/include/PassHash.php';

    // Set up dependencies
    require_once __DIR__ . '/src/dependencies.php';

    // Set up  middleware
    require_once __DIR__ . '/src/middleware.php';

    // Set up  mapper
    require_once __DIR__ . '/src/Mapper.php';

    // Set up  routes
    require_once __DIR__ . '/src/routes.php';

    // Run app
    $app->run();
