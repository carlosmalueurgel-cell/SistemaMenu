<?php

$path = dirname(__DIR__, 2) . '/.env';
$config = parse_ini_file($path);
if ($config === false) throw new RuntimeException('No se pudo leer el archivo .env');
foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_PORT', 'DB_CHARSET'] as $clave) {
    if (!isset($config[$clave]) || $config[$clave] === '') throw new RuntimeException("Falta la configuración $clave");
}
define('HOST', $config['DB_HOST']);
define('DATABASE', $config['DB_NAME']);
define('USERNAME', $config['DB_USER']);
define('PASSWORD', $config['DB_PASSWORD']);
define('PORT', $config['DB_PORT']);
define('CHARSET', str_replace('charset=', '', $config['DB_CHARSET']));


	



