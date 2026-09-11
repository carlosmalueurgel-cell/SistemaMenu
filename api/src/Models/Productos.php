<?php

require_once __DIR__ . '/../config/conexionDB.php';

class Productos
{
    public static function all(): array
    {
        return ConexionPDO::query('SELECT id, codBarras, descripcion, stock, precio_unitario, creado_por, fecha_registro FROM PRODUCTOS ORDER BY id');
    }

    public static function find(int $id): array
    {
        return ConexionPDO::query('SELECT id, codBarras, descripcion, stock, precio_unitario, creado_por, fecha_registro FROM PRODUCTOS WHERE id = :id', [':id' => $id]);
    }

    public static function add(array $data)
    {
        return ConexionPDO::execute('INSERT INTO PRODUCTOS (codBarras, descripcion, stock, precio_unitario, creado_por) VALUES (:codBarras, :descripcion, :stock, :precio_unitario, :creado_por)', self::parametros($data), true);
    }

    public static function update(int $id, array $data): bool
    {
        return ConexionPDO::execute('UPDATE PRODUCTOS SET codBarras = :codBarras, descripcion = :descripcion, stock = :stock, precio_unitario = :precio_unitario, creado_por = :creado_por WHERE id = :id', self::parametros($data) + [':id' => $id], false);
    }

    public static function delete(int $id): bool
    {
        return ConexionPDO::execute('DELETE FROM PRODUCTOS WHERE id = :id', [':id' => $id], false);
    }

    public static function validar(array $data): array
    {
        $errores = [];
        foreach (['codBarras', 'descripcion', 'stock', 'precio_unitario'] as $campo) {
            if (!array_key_exists($campo, $data) || $data[$campo] === '' || $data[$campo] === null) $errores[] = "El campo $campo es obligatorio";
        }
        if (isset($data['codBarras']) && (!is_string($data['codBarras']) || strlen(trim($data['codBarras'])) > 100)) $errores[] = 'El campo codBarras no puede superar 100 caracteres';
        if (isset($data['descripcion']) && (!is_string($data['descripcion']) || strlen(trim($data['descripcion'])) > 100)) $errores[] = 'El campo descripcion no puede superar 100 caracteres';
        if (isset($data['stock']) && (filter_var($data['stock'], FILTER_VALIDATE_INT) === false || (int) $data['stock'] < 0)) $errores[] = 'El campo stock debe ser un entero mayor o igual a 0';
        if (isset($data['precio_unitario']) && (!is_numeric($data['precio_unitario']) || (float) $data['precio_unitario'] < 0)) $errores[] = 'El campo precio_unitario debe ser un número mayor o igual a 0';
        if (array_key_exists('creado_por', $data) && $data['creado_por'] !== null && $data['creado_por'] !== '' && (filter_var($data['creado_por'], FILTER_VALIDATE_INT) === false || (int) $data['creado_por'] < 1)) $errores[] = 'El campo creado_por debe ser un entero mayor a 0 o null';
        return $errores;
    }

    private static function parametros(array $data): array
    {
        return [
            ':codBarras' => trim($data['codBarras']),
            ':descripcion' => trim($data['descripcion']),
            ':stock' => (int) $data['stock'],
            ':precio_unitario' => number_format((float) $data['precio_unitario'], 2, '.', ''),
            ':creado_por' => array_key_exists('creado_por', $data) && $data['creado_por'] !== '' ? $data['creado_por'] : null,
        ];
    }
}
