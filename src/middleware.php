<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$container = $app->getContainer();

// Register middleware for all routes
// If you are implementing per-route checks you must not add this
$app->add($container->get('csrf'));
// $app->add($container->get('csrf.header'));


// This is the middleware
// It will add the Access-Control-Allow-Methods header to every request

// $app->add(function($request, $response, $next) {

//     $response = $next($request, $response);

//     return $response
//             ->withHeader('Access-Control-Allow-Origin', '*')
//             ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
//             ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');;
// });