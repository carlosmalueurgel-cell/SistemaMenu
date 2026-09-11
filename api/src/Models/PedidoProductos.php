<?php

include_once __DIR__ . "/../config/conexionDB.php";

class PedidoProductos
{
    public static function all(): array
    {
        return ConexionPDO::query('SELECT Id, cod_producto, cod_pedido, cantidad, precio_unitario, descuento FROM PEDIDO_PRODUCTOS ORDER BY Id');
    }

    public static function find(int $id): array
    {
        return ConexionPDO::query(
            'SELECT Id, cod_producto, cod_pedido, cantidad, precio_unitario, descuento FROM PEDIDO_PRODUCTOS WHERE Id = :id',
            [':id' => $id]
        );
    }

    public static function add(array $data)
    {
        return ConexionPDO::execute(
            'INSERT INTO PEDIDO_PRODUCTOS (cod_producto, cod_pedido, cantidad, precio_unitario, descuento) VALUES (:cod_producto, :cod_pedido, :cantidad, :precio_unitario, :descuento)',
            self::parametros($data),
            true
        );
    }

    public static function update(int $id, array $data): bool
    {
        return ConexionPDO::execute(
            'UPDATE PEDIDO_PRODUCTOS SET cod_producto = :cod_producto, cod_pedido = :cod_pedido, cantidad = :cantidad, precio_unitario = :precio_unitario, descuento = :descuento WHERE Id = :id',
            self::parametros($data) + [':id' => $id],
            false
        );
    }

    public static function delete(int $id): bool
    {
        return ConexionPDO::execute('DELETE FROM PEDIDO_PRODUCTOS WHERE Id = :id', [':id' => $id], false);
    }

    public static function validar(array $data): array
    {
        $errores = [];
        foreach (['cod_producto', 'cod_pedido', 'cantidad', 'precio_unitario'] as $campo) {
            if (!array_key_exists($campo, $data) || $data[$campo] === '' || $data[$campo] === null) {
                $errores[] = "El campo $campo es obligatorio";
            }
        }

        foreach (['cod_producto', 'cod_pedido', 'cantidad'] as $campo) {
            if (isset($data[$campo]) && filter_var($data[$campo], FILTER_VALIDATE_INT) === false) {
                $errores[] = "El campo $campo debe ser un numero entero";
            } elseif (isset($data[$campo]) && (int) $data[$campo] <= 0) {
                $errores[] = "El campo $campo debe ser mayor a 0";
            }
        }

        foreach (['precio_unitario', 'descuento'] as $campo) {
            if (array_key_exists($campo, $data) && $data[$campo] !== '' && $data[$campo] !== null) {
                if (!is_numeric($data[$campo]) || (float) $data[$campo] < 0) {
                    $errores[] = "El campo $campo debe ser un numero mayor o igual a 0";
                } elseif (round((float) $data[$campo], 2) != (float) $data[$campo]) {
                    $errores[] = "El campo $campo solo admite hasta 2 decimales";
                }
            }
        }

        return $errores;
    }

    private static function parametros(array $data): array
    {
        return [
            ':cod_producto' => (int) $data['cod_producto'],
            ':cod_pedido' => (int) $data['cod_pedido'],
            ':cantidad' => (int) $data['cantidad'],
            ':precio_unitario' => number_format((float) $data['precio_unitario'], 2, '.', ''),
            ':descuento' => array_key_exists('descuento', $data) && $data['descuento'] !== '' ? number_format((float) $data['descuento'], 2, '.', '') : '0.00',
        ];
    }
}