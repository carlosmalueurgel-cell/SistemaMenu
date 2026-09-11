<?php

include_once __DIR__ . "/../config/conexionDB.php";

class Pedidos
{
    public static function all(): array
    {
        return ConexionPDO::query('SELECT id, cod_cliente, fecha_compra, cantidad, cod_empleado, creado_por FROM PEDIDOS ORDER BY id');
    }

    public static function find(int $id): array
    {
        return ConexionPDO::query(
            'SELECT id, cod_cliente, fecha_compra, cantidad, cod_empleado, creado_por FROM PEDIDOS WHERE id = :id',
            [':id' => $id]
        );
    }

    public static function add(array $data)
    {
        return ConexionPDO::execute(
            'INSERT INTO PEDIDOS (cod_cliente, fecha_compra, cantidad, cod_empleado, creado_por) VALUES (:cod_cliente, :fecha_compra, :cantidad, :cod_empleado, :creado_por)',
            self::parametros($data),
            true
        );
    }

    public static function update(int $id, array $data): bool
    {
        return ConexionPDO::execute(
            'UPDATE PEDIDOS SET cod_cliente = :cod_cliente, fecha_compra = :fecha_compra, cantidad = :cantidad, cod_empleado = :cod_empleado, creado_por = :creado_por WHERE id = :id',
            self::parametros($data) + [':id' => $id],
            false
        );
    }

    public static function delete(int $id): bool
    {
        return ConexionPDO::execute('DELETE FROM PEDIDOS WHERE id = :id', [':id' => $id], false);
    }

    public static function validar(array $data): array
    {
        $errores = [];
        foreach (['cod_cliente', 'fecha_compra', 'cantidad', 'cod_empleado'] as $campo) {
            if (!array_key_exists($campo, $data) || $data[$campo] === '' || $data[$campo] === null) {
                $errores[] = "El campo $campo es obligatorio";
            }
        }

        foreach (['cod_cliente', 'cantidad', 'cod_empleado'] as $campo) {
            if (isset($data[$campo]) && filter_var($data[$campo], FILTER_VALIDATE_INT) === false) {
                $errores[] = "El campo $campo debe ser un numero entero";
            } elseif (isset($data[$campo]) && (int) $data[$campo] <= 0) {
                $errores[] = "El campo $campo debe ser mayor a 0";
            }
        }

        if (isset($data['fecha_compra']) && !self::fechaValida($data['fecha_compra'])) {
            $errores[] = 'El campo fecha_compra debe tener el formato YYYY-MM-DD HH:MM:SS';
        }

        if (array_key_exists('creado_por', $data) && $data['creado_por'] !== null && $data['creado_por'] !== '') {
            if (filter_var($data['creado_por'], FILTER_VALIDATE_INT) === false || (int) $data['creado_por'] <= 0) {
                $errores[] = 'El campo creado_por debe ser un numero entero mayor a 0 o null';
            }
        }

        return $errores;
    }

    private static function fechaValida($fecha): bool
    {
        if (!is_string($fecha)) return false;
        $fechaDate = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
        return $fechaDate !== false && $fechaDate->format('Y-m-d H:i:s') === $fecha;
    }

    private static function parametros(array $data): array
    {
        return [
            ':cod_cliente' => (int) $data['cod_cliente'],
            ':fecha_compra' => $data['fecha_compra'],
            ':cantidad' => (int) $data['cantidad'],
            ':cod_empleado' => (int) $data['cod_empleado'],
            ':creado_por' => array_key_exists('creado_por', $data) && $data['creado_por'] !== '' ? $data['creado_por'] : null,
        ];
    }
}