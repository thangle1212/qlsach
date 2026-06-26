<?php
$_SERVER = [
    'REQUEST_METHOD' => 'POST',
    'REQUEST_URI' => '/qlsach/api/login',
    'SCRIPT_NAME' => '/qlsach/index.php',
    'HTTP_HOST' => 'localhost',
    'CONTENT_TYPE' => 'application/json'
];
$_GET = ['url' => '/api/login'];
require 'config.php';
require 'Router/Router.php';
$router = new Router();
require 'Router/Route.php';
ob_start();
$router->run();
$output = ob_get_clean();
file_put_contents('tmp_debug_api_output.txt', "OUTPUT:\n".$output."\n");
