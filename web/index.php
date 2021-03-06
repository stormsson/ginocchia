<?php

require_once __DIR__.'/../vendor/autoload.php';


// CONFIGURAZIONE

$app = new Silex\Application();

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'dbname' => 'ginocchia',
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'root',
    ),
));



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

$app->run();
