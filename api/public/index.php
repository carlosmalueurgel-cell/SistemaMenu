<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';
require_once __DIR__ . '/../src/Controllers/ProductoController.php';
require_once __DIR__ . '/../src/Controllers/ClientesController.php';
require_once __DIR__ . '/../src/Controllers/EmpleadosController.php';
require_once __DIR__ . '/../src/Controllers/PedidosController.php';
require_once __DIR__ . '/../src/Controllers/PedidoProductosController.php';

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && hash_equals(FRONTEND_ORIGIN, $origin)) {
    header('Access-Control-Allow-Origin: ' . FRONTEND_ORIGIN);
    header('Vary: Origin');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

use App\Router;

$route=new Router();
//direccion para usuario
$route->add('GET','/','UserController@getAll');
$route->add('GET','/users','UserController@getAll');
$route->add('GET','/users/{id}','UserController@getById');
$route->add('POST','/users','UserController@add');
$route->add('POST','/login','UserController@login');
$route->add('PUT','/users/{id}','UserController@actualizar');
$route->add('DELETE','/users/{id}','UserController@eliminar');
//direccion de producto
$route->add('GET','/productos','ProductoController@getAll');
$route->add('GET','/productos/{id}','ProductoController@getById');
$route->add('PUT','/productos/{id}','ProductoController@actualizar');
$route->add('POST','/productos','ProductoController@add');
$route->add('DELETE','/productos/{id}','ProductoController@eliminar');
//clientes
$route->add('GET','/clientes','ClientesController@getAll');
$route->add('GET','/clientes/{id}','ClientesController@getById');
$route->add('POST','/clientes','ClientesController@add');
$route->add('PUT','/clientes/{id}','ClientesController@actualizar');
$route->add('DELETE','/clientes/{id}','ClientesController@eliminar');
// empleados
$route->add('GET', '/empleados', 'EmpleadosController@getAll');
$route->add('GET', '/empleados/{id}', 'EmpleadosController@getById');
$route->add('POST', '/empleados', 'EmpleadosController@add');
$route->add('PUT', '/empleados/{id}', 'EmpleadosController@actualizar');
$route->add('DELETE', '/empleados/{id}', 'EmpleadosController@eliminar');





// pedidos
$route->add('GET', '/pedidos', 'PedidosController@getAll');
$route->add('GET', '/pedidos/{id}', 'PedidosController@getById');
$route->add('POST', '/pedidos', 'PedidosController@add');
$route->add('PUT', '/pedidos/{id}', 'PedidosController@actualizar');
$route->add('DELETE', '/pedidos/{id}', 'PedidosController@eliminar');

// detalle de pedidos
$route->add('GET', '/pedido-productos', 'PedidoProductosController@getAll');
$route->add('GET', '/pedido-productos/{id}', 'PedidoProductosController@getById');
$route->add('POST', '/pedido-productos', 'PedidoProductosController@add');
$route->add('PUT', '/pedido-productos/{id}', 'PedidoProductosController@actualizar');
$route->add('DELETE', '/pedido-productos/{id}', 'PedidoProductosController@eliminar');

try {
    $route->run();
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['estado' => false, 'mensaje' => 'Error interno del servidor']);
}

