<?php

include_once __DIR__ . "/../config/conexionDB.php";

class Empleados
{
    private const CAMPOS = ['ci', 'nombre', 'apellidos'];

    public static function all(): array
    {
        return ConexionPDO::query('SELECT id, ci, nombre, apellidos FROM EMPLEADOS ORDER BY id');
    }

    public static function find(int $id): array
    {
        return ConexionPDO::query('SELECT id, ci, nombre, apellidos FROM EMPLEADOS WHERE id = :id', [':id' => $id]);
    }

    public static function add(array $data)
    {
        return ConexionPDO::execute(
            'INSERT INTO EMPLEADOS (ci, nombre, apellidos) VALUES (:ci, :nombre, :apellidos)',
            self::parametros($data),
            true
        );
    }

    public static function update(int $id, array $data): bool
    {
        return ConexionPDO::execute(
            'UPDATE EMPLEADOS SET ci = :ci, nombre = :nombre, apellidos = :apellidos WHERE id = :id',
            self::parametros($data) + [':id' => $id],
            false
        );
    }

    public static function delete(int $id): bool
    {
        return ConexionPDO::execute('DELETE FROM EMPLEADOS WHERE id = :id', [':id' => $id], false);
    }

    public static function validar(array $data): array
    {
        $errores = [];
        foreach (self::CAMPOS as $campo) {
            if (!isset($data[$campo]) || !is_string($data[$campo]) || trim($data[$campo]) === '') {
                $errores[] = "El campo $campo es obligatorio";
            }
        }

        if (isset($data['ci']) && is_string($data['ci']) && strlen(trim($data['ci'])) > 20) {
            $errores[] = 'El campo ci no puede superar 20 caracteres';
        }
        if (isset($data['nombre']) && is_string($data['nombre']) && strlen(trim($data['nombre'])) > 50) {
            $errores[] = 'El campo nombre no puede superar 50 caracteres';
        }
        if (isset($data['apellidos']) && is_string($data['apellidos']) && strlen(trim($data['apellidos'])) > 50) {
            $errores[] = 'El campo apellidos no puede superar 50 caracteres';
        }
        return $errores;
    }

    private static function parametros(array $data): array
    {
        return [':ci' => trim($data['ci']), ':nombre' => trim($data['nombre']), ':apellidos' => trim($data['apellidos'])];
    }
}
