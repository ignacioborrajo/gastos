<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

$app = new \Slim\Slim;

$app->get('/hello/:name', function ($name) use ($app) {
    
    $app->response->write("Hello, $name");
    
});


$app->run();