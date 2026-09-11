<?php

require_once __DIR__ . '/../Models/Clientes.php';

class ClientesController
{
    public function getAll(): void { $this->respuesta(200, Clientes::all()); }

    public function getById(int $id): void
    {
        $clientes = Clientes::find($id);
        if (count($clientes) === 0) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Cliente no encontrado']); return; }
        $this->respuesta(200, $clientes[0]);
    }

    public function add(): void
    {
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Clientes::validar($data);
        if ($errores !== []) { $this->respuesta(400, ['estado' => false, 'errores' => $errores]); return; }
        $id = Clientes::add($data);
        $this->respuesta(201, ['estado' => true, 'mensaje' => 'Cliente creado correctamente', 'id' => (int) $id]);
    }

    public function actualizar(int $id): void
    {
        if (count(Clientes::find($id)) === 0) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Cliente no encontrado']); return; }
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Clientes::validar($data);
        if ($errores !== []) { $this->respuesta(400, ['estado' => false, 'errores' => $errores]); return; }
        Clientes::update($id, $data);
        $this->respuesta(200, ['estado' => true, 'mensaje' => 'Cliente actualizado correctamente']);
    }

    public function eliminar(int $id): void
    {
        if (count(Clientes::find($id)) === 0) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Cliente no encontrado']); return; }
        Clientes::delete($id);
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
        echo json_encode($datos);
    }
}
