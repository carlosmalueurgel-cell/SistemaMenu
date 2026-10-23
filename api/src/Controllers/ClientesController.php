<?php

require_once __DIR__ . '/../Models/Clientes.php';

class ClientesController
{
    public function getAll(): void
    {
        try { $this->respuesta(200, Clientes::all()); }
        catch (Throwable $error) { $this->respuesta(503, ['estado' => false, 'mensaje' => 'No se pudo consultar clientes']); }
    }

    public function getById(int $id): void
    {
        try { $clientes = Clientes::find($id); }
        catch (Throwable $error) { $this->respuesta(503, ['estado' => false, 'mensaje' => 'No se pudo consultar clientes']); return; }
        if (count($clientes) === 0) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Cliente no encontrado']); return; }
        $this->respuesta(200, $clientes[0]);
    }

    public function add(): void
    {
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Clientes::validar($data);
        if ($errores !== []) { $this->respuesta(400, ['estado' => false, 'errores' => $errores]); return; }
        try {
            if (Clientes::ciExiste($data['ci'])) { $this->respuesta(409, ['estado' => false, 'mensaje' => 'El CI ya está registrado']); return; }
            $id = Clientes::add($data);
        } catch (Throwable $error) { $this->errorBaseDatos($error, 'guardar'); return; }
        $this->respuesta(201, ['estado' => true, 'mensaje' => 'Cliente creado correctamente', 'id' => (int) $id]);
    }

    public function actualizar(int $id): void
    {
        try { if (count(Clientes::find($id)) === 0) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Cliente no encontrado']); return; } }
        catch (Throwable $error) { $this->respuesta(503, ['estado' => false, 'mensaje' => 'No se pudo consultar clientes']); return; }
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Clientes::validar($data);
        if ($errores !== []) { $this->respuesta(400, ['estado' => false, 'errores' => $errores]); return; }
        try {
            if (Clientes::ciExiste($data['ci'], $id)) { $this->respuesta(409, ['estado' => false, 'mensaje' => 'El CI ya está registrado']); return; }
            Clientes::update($id, $data);
        } catch (Throwable $error) { $this->errorBaseDatos($error, 'actualizar'); return; }
        $this->respuesta(200, ['estado' => true, 'mensaje' => 'Cliente actualizado correctamente']);
    }

    public function eliminar(int $id): void
    {
        try {
            if (count(Clientes::find($id)) === 0) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Cliente no encontrado']); return; }
            Clientes::delete($id);
        } catch (Throwable $error) { $this->errorBaseDatos($error, 'eliminar'); return; }
        $this->respuesta(200, ['estado' => true, 'mensaje' => 'Cliente eliminado correctamente']);
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
        echo json_encode($datos, JSON_INVALID_UTF8_SUBSTITUTE);
    }

    private function errorBaseDatos(Throwable $error, string $accion): void
    {
        $codigo = $error instanceof PDOException ? (string) $error->getCode() : (string) $error->getPrevious()?->getCode();
        if ($codigo === '23000') { $this->respuesta(409, ['estado' => false, 'mensaje' => 'No se puede completar la operación: hay datos duplicados o registros relacionados']); return; }
        $this->respuesta(503, ['estado' => false, 'mensaje' => "No se pudo $accion el cliente por un problema de base de datos"]);
    }
}
