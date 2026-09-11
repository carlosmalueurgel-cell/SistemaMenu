<?php

require_once __DIR__ . '/../config/conexionDB.php';

class Clientes
{
    public static function all(): array
    {
        return ConexionPDO::query('SELECT id, ci, nombre, apellidos, direccion, telefono FROM CLIENTES ORDER BY id');
    }

    public static function find(int $id): array
    {
        return ConexionPDO::query('SELECT id, ci, nombre, apellidos, direccion, telefono FROM CLIENTES WHERE id = :id', [':id' => $id]);
    }

    public static function add(array $data)
    {
        return ConexionPDO::execute(
            'INSERT INTO CLIENTES (ci, nombre, apellidos, direccion, telefono) VALUES (:ci, :nombre, :apellidos, :direccion, :telefono)',
            self::parametros($data),
            true
        );
    }

    public static function update(int $id, array $data): bool
    {
        return ConexionPDO::execute(
            'UPDATE CLIENTES SET ci = :ci, nombre = :nombre, apellidos = :apellidos, direccion = :direccion, telefono = :telefono WHERE id = :id',
            self::parametros($data) + [':id' => $id],
            false
        );
    }

    public static function delete(int $id): bool
    {
        return ConexionPDO::execute('DELETE FROM CLIENTES WHERE id = :id', [':id' => $id], false);
    }

    public static function validar(array $data): array
    {
        $errores = [];
        foreach (['ci', 'nombre', 'apellidos'] as $campo) {
            if (!isset($data[$campo]) || !is_string($data[$campo]) || trim($data[$campo]) === '') {
                $errores[] = "El campo $campo es obligatorio";
            }
        }
        foreach (['ci' => 20, 'nombre' => 50, 'apellidos' => 50, 'direccion' => 250, 'telefono' => 15] as $campo => $limite) {
            if (isset($data[$campo]) && (!is_string($data[$campo]) || strlen(trim($data[$campo])) > $limite)) {
                $errores[] = "El campo $campo no puede superar $limite caracteres";
            }
        }
        return $errores;
    }

    private static function parametros(array $data): array
    {
        return [
            ':ci' => trim($data['ci']),
            ':nombre' => trim($data['nombre']),
            ':apellidos' => trim($data['apellidos']),
            ':direccion' => isset($data['direccion']) ? trim((string) $data['direccion']) : null,
            ':telefono' => isset($data['telefono']) ? trim((string) $data['telefono']) : null,
        ];
    }
}
