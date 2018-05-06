<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// Register component on container twik view renderer
$container['view'] = function ($container) {
    $settings = $container->get('settings')['renderer'];
    $cache = $container->get('settings')['cache'];
    $view = new \Slim\Views\Twig($settings['template_path'], [
        // 'cache' => $cache['cache_path']
    ]);
    
    // session
    $view->getEnvironment()->addGlobal('session', $_SESSION);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    $view->addExtension(new App\Views\CsrfExtension($container['csrf']));

    return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Service factory for the ORM
$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

// CSRF
$container['csrf'] = function ($c) {
    $guard = new \Slim\Csrf\Guard();
    $guard->setFailureCallable(function ($request, $response, $next) {
        $request = $request->withAttribute("csrf_status", false);
        return $next($request, $response);
    });
    return $guard;
};

$container['csrf.header'] = function($c) {
    return new App\Middleware\CsrfResponseHeader($c->get('csrf'));
};

// Register Controller
$container['Controller'] = function ($c) {
    return new App\Controller\Controller($c);
};

$container['DefaultController'] = function($c) {
    return new App\Controller\DefaultController($c->get('csrf'), $c->get('logger'), $c->get('view'));
};

$container['AuthController'] = function($c) {
    return new App\Controller\AuthController($c->get('csrf'), $c->get('logger'), $c->get('view'), $c->get('db'));
};

$container['ManageController'] = function($c) {
    return new App\Controller\ManageController($c->get('csrf'), $c->get('logger'), $c->get('view'), $c->get('db'));
};

$container['PostController'] = function($c) {
    return new App\Controller\PostController($c->get('csrf'), $c->get('logger'), $c->get('view'), $c->get('db'));
};

$container['CategoriesController'] = function($c) {
    return new App\Controller\CategoriesController($c->get('csrf'), $c->get('logger'), $c->get('view'), $c->get('db'));
};

$container['PagesController'] = function($c) {
    return new App\Controller\PagesController($c->get('csrf'), $c->get('logger'), $c->get('view'), $c->get('db'));
};

$container['ProfileController'] = function($c) {
    return new App\Controller\ProfileController($c->get('csrf'), $c->get('logger'), $c->get('view'), $c->get('db'));
};
