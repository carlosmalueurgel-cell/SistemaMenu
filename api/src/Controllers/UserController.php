<?php

require_once __DIR__ . '/../Models/Users.php';

class UserController
{
    public function getAll(): void
    {
        try {
            $this->respuesta(200, Users::all());
        } catch (Throwable $error) {
            $this->respuesta(503, ['estado' => false, 'mensaje' => 'No se pudo consultar la base de datos']);
        }
    }

    public function getById(int $id): void
    {
        try {
            $usuarios = Users::find($id);
            if (count($usuarios) === 0) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Usuario no encontrado']); return; }
            $this->respuesta(200, $usuarios[0]);
        } catch (Throwable $error) {
            $this->respuesta(503, ['estado' => false, 'mensaje' => 'No se pudo consultar la base de datos']);
        }
    }

    public function add(): void
    {
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Users::validar($data);
        if ($errores !== []) { $this->respuesta(400, ['estado' => false, 'errores' => $errores]); return; }
        try {
            if (Users::usernameExiste($data['username'])) {
                $this->respuesta(409, ['estado' => false, 'mensaje' => 'El username ya está registrado']);
                return;
            }
            if (!Users::empleadoExiste((int) $data['cod_empleado'])) {
                $this->respuesta(422, ['estado' => false, 'mensaje' => "No existe un empleado con id {$data['cod_empleado']}. Cree el empleado primero o envíe un cod_empleado válido"]);
                return;
            }
            $id = Users::add($data);
            $this->respuesta(201, ['estado' => true, 'mensaje' => 'Usuario creado correctamente', 'id' => (int) $id]);
        } catch (Throwable $error) {
            $this->respuestaErrorBaseDatos($error, 'crear');
        }
    }

    public function actualizar(int $id): void
    {
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Users::validar($data);
        if ($errores !== []) { $this->respuesta(400, ['estado' => false, 'errores' => $errores]); return; }
        try {
            if (Users::find($id) === []) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Usuario no encontrado']); return; }
            if (Users::usernameExiste($data['username'], $id)) { $this->respuesta(409, ['estado' => false, 'mensaje' => 'El username ya está registrado']); return; }
            if (!Users::empleadoExiste((int) $data['cod_empleado'])) { $this->respuesta(422, ['estado' => false, 'mensaje' => "No existe un empleado con id {$data['cod_empleado']}. Cree el empleado primero o envíe un cod_empleado válido"]); return; }
            Users::update($id, $data);
            $this->respuesta(200, ['estado' => true, 'mensaje' => 'Usuario actualizado correctamente']);
        } catch (Throwable $error) {
            $this->respuestaErrorBaseDatos($error, 'actualizar');
        }
    }

    public function eliminar(int $id): void
    {
        try {
            if (count(Users::find($id)) === 0) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Usuario no encontrado']); return; }
            if (Users::tieneRegistrosRelacionados($id)) {
                $this->respuesta(409, ['estado' => false, 'mensaje' => 'No se puede eliminar el usuario porque tiene registros asociados']);
                return;
            }
            if (!Users::delete($id)) { $this->respuesta(409, ['estado' => false, 'mensaje' => 'No se pudo eliminar el usuario']); return; }
            $this->respuesta(200, ['estado' => true, 'mensaje' => 'Usuario eliminado correctamente']);
        } catch (Throwable $error) {
            $this->respuestaErrorBaseDatos($error, 'eliminar');
        }
    }

    private function datosJson(): ?array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) { $this->respuesta(400, ['estado' => false, 'errores' => ['El cuerpo debe ser un JSON válido']]); return null; }
        return $data;
    }

    private function respuesta(int $estado, array $datos): void
    {
        http_response_code($estado);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos);
    }

    private function respuestaErrorBaseDatos(Throwable $error, string $accion): void
    {
        $codigo = $error instanceof PDOException ? (string) $error->getCode() : (string) $error->getPrevious()?->getCode();
        if ($codigo === '23000') {
            $this->respuesta(409, ['estado' => false, 'mensaje' => "No se pudo $accion el usuario porque ya existe o tiene datos relacionados"]);
            return;
        }
        $this->respuesta(503, ['estado' => false, 'mensaje' => "No se pudo $accion el usuario por un problema de conexión o base de datos"]);
    }
}
