<?php

// DIC configuration
$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Environment
$container['environment'] = function () {
    // Fix the Slim 3 subdirectory issue (#1529)
    // This fix makes it possible to run the app from localhost/slim3-app
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $_SERVER['REAL_SCRIPT_NAME'] = $scriptName;
    $_SERVER['SCRIPT_NAME'] = dirname(dirname($scriptName)) . '/' . basename($scriptName);
    return new Slim\Http\Environment($_SERVER);
};

// Database
$container['capsule'] = function ($c) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($c['settings']['db']);
    return $capsule;
};

// Twig
$container['view'] = function ($c) {
     $view = new \Slim\Views\Twig($c['settings']['view']['template_path'], $c['settings']['view']['twig']);
    // Add extensions
    $view->addExtension(new \Slim\Views\TwigExtension($c['router'], $c['request']->getUri()));
    $view->addExtension(new Twig_Extension_Debug());
    return $view;
};

// CSRF guard
$container['csrf'] = function ($c) {
    $guard = new \Slim\Csrf\Guard();
    $guard->setFailureCallable(function ($request, $response, $next) {
        $request = $request->withAttribute("csrf_status", false);
        return $next($request, $response);
    });
    return $guard;
};
// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------
// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// Flash messages
$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages;
};
// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------
$container['App\FrontController'] = function ($c) {
    return new App\FrontController($c['view'], $c['router'], $c['flash']);
};
