<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../src/routes.php';

function autoload($className) {
    $className = str_replace('App', 'Src', $className);
    $classFile = str_replace(
        '/public','',__DIR__) . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
}

spl_autoload_register('autoload');

$route = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$route = $route ?: 'home';  
echo route($route);
