<?php

require_once __DIR__.'/../vendor/autoload.php';


// CONFIGURAZIONE

$app = new Silex\Application();

/** Routes */
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/** Twig **/
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

/** Estensioni Twig **/
$app["twig"] = $app->share($app->extend("twig", function (\Twig_Environment $twig, Silex\Application $app) {
    $twig->addExtension(new Hart\Twig\fbAvatarExtension($app));
    return $twig;
}));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'dbname' => 'ginocchia',
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'pita725ewq'
    ),
));

$app->register(new Silex\Provider\SessionServiceProvider());

$app['debug'] = true;


// ROTTE

$app->get(
    '/',
    'Hart\Controller\DefaultController::index'
)->bind('homepage');

$app->get(
    '/history',
    'Hart\Controller\DefaultController::history'
)->bind('history');

$app->post(
    '/count',
    'Hart\Controller\DefaultController::count'
)->bind('post_count');

$app->get(
    '/fb-login-template',
    'Hart\Controller\FacebookController::fbLoginTemplate'
)->bind('fb_login_template');

$app->post(
    '/fb-login',
    'Hart\Controller\FacebookController::fbLogin'
)->bind('fb_login');

$app->get(
    '/fb-logout',
    'Hart\Controller\FacebookController::fbLogout'
)->bind('fb_logout');

$app->run();
