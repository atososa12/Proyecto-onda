<?php
// Router mínimo y limpio (producción)
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$root = __DIR__;

$route = $_GET['r'] ?? 'home/index';
[$controller, $action] = array_pad(explode('/', $route, 2), 2, 'index');

$controllerFile  = $root . "/app/Controllers/{$controller}.php";
$controllerClass = ucfirst($controller) . 'Controller';

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

// Nota: si el método hace echo/headers (p.ej. JSON), no es obligatorio que retorne string
$out = $instance->$action();
if (is_string($out)) {
  echo $out;
}

