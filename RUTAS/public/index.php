<?php
// public/index.php
declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root.'/app/Database.php';

// Resolver ruta ?r=controlador/accion
$route = $_GET['r'] ?? 'home/index';
[$controller, $action] = array_pad(explode('/', $route, 2), 2, 'index');

// Mapear a archivo de controlador
$controllerClass = ucfirst($controller).'Controller';
$controllerFile  = $root."/app/Controllers/{$controller}.php";

if (!file_exists($controllerFile)) {
    http_response_code(404);
    exit("Controlador no encontrado: {$controller}");
}
require_once $controllerFile;

if (!class_exists($controllerClass)) {
    http_response_code(500);
    exit("Clase de controlador inválida: {$controllerClass}");
}

$instance = new $controllerClass();
if (!method_exists($instance, $action)) {
    http_response_code(404);
    exit("Acción no encontrada: {$action}");
}

// Ejecutar acción
echo $instance->$action();

