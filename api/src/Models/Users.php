<?php

require_once __DIR__ . '/../config/conexionDB.php';

class Users
{
    public static function all(): array
    {
        return ConexionPDO::query('SELECT id, username, estado, cod_empleado FROM USUARIOS ORDER BY id');
    }

    public static function find(int $id): array
    {
        return ConexionPDO::query('SELECT id, username, estado, cod_empleado FROM USUARIOS WHERE id = :id', [':id' => $id]);
    }

    public static function add(array $data)
    {
        return ConexionPDO::execute(
            'INSERT INTO USUARIOS (username, password_hash, estado, cod_empleado) VALUES (:username, :password_hash, :estado, :cod_empleado)',
            self::parametros($data),
            true
        );
    }

    public static function usernameExiste(string $username, ?int $idExcluir = null): bool
    {
        $sql = 'SELECT id FROM USUARIOS WHERE username = :username';
        $parametros = [':username' => trim($username)];
        if ($idExcluir !== null) {
            $sql .= ' AND id != :id';
            $parametros[':id'] = $idExcluir;
        }
        return ConexionPDO::query($sql, $parametros) !== [];
    }

    public static function empleadoExiste(int $id): bool
    {
        return ConexionPDO::query('SELECT id FROM EMPLEADOS WHERE id = :id', [':id' => $id]) !== [];
    }

    public static function tieneRegistrosRelacionados(int $id): bool
    {
        $producto = ConexionPDO::query('SELECT id FROM PRODUCTOS WHERE creado_por = :id LIMIT 1', [':id' => $id]);
        $pedido = ConexionPDO::query('SELECT id FROM PEDIDOS WHERE creado_por = :id LIMIT 1', [':id' => $id]);
        return $producto !== [] || $pedido !== [];
    }

    public static function update(int $id, array $data): bool
    {
        return ConexionPDO::execute(
            'UPDATE USUARIOS SET username = :username, password_hash = :password_hash, estado = :estado, cod_empleado = :cod_empleado WHERE id = :id',
            self::parametros($data) + [':id' => $id],
            false
        );
    }

    public static function delete(int $id): bool
    {
        return ConexionPDO::execute('DELETE FROM USUARIOS WHERE id = :id', [':id' => $id], false);
    }

    public static function validar(array $data): array
    {
        $errores = [];
        if (!isset($data['username']) || !is_string($data['username']) || trim($data['username']) === '') {
            $errores[] = 'El campo username es obligatorio';
        } elseif (strlen(trim($data['username'])) > 50) {
            $errores[] = 'El campo username no puede superar 50 caracteres';
        }
        if (!isset($data['password_hash']) || !is_string($data['password_hash']) || trim($data['password_hash']) === '') {
            $errores[] = 'El campo password_hash es obligatorio';
        } elseif (strlen($data['password_hash']) > 255) {
            $errores[] = 'El campo password_hash no puede superar 255 caracteres';
        }
        if (!array_key_exists('estado', $data) || !in_array($data['estado'], [0, 1, true, false, '0', '1'], true)) {
            $errores[] = 'El campo estado debe ser 0 o 1';
        }
        if (!isset($data['cod_empleado']) || filter_var($data['cod_empleado'], FILTER_VALIDATE_INT) === false || (int) $data['cod_empleado'] < 1) {
            $errores[] = 'El campo cod_empleado debe ser un entero mayor a 0';
        }
        return $errores;
    }

    private static function parametros(array $data): array
    {
        return [
            ':username' => trim($data['username']),
            ':password_hash' => $data['password_hash'],
            ':estado' => (int) $data['estado'],
            ':cod_empleado' => (int) $data['cod_empleado'],
        ];
    }
}
