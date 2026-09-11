<?php

require_once __DIR__ . '/../Models/Pedidos.php';

class PedidosController
{
    public function getAll(): void
    {
        $this->respuesta(200, Pedidos::all());
    }

    public function getById(int $id): void
    {
        $pedidos = Pedidos::find($id);
        if (count($pedidos) === 0) {
            $this->respuesta(404, ['estado' => false, 'mensaje' => 'Pedido no encontrado']);
            return;
        }
        $this->respuesta(200, $pedidos[0]);
    }

    public function add(): void
    {
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Pedidos::validar($data);
        if ($errores !== []) {
            $this->respuesta(400, ['estado' => false, 'errores' => $errores]);
            return;
        }
        $id = Pedidos::add($data);
        $this->respuesta(201, ['estado' => true, 'mensaje' => 'Pedido creado correctamente', 'id' => (int) $id]);
    }

    public function actualizar(int $id): void
    {
        if (count(Pedidos::find($id)) === 0) {
            $this->respuesta(404, ['estado' => false, 'mensaje' => 'Pedido no encontrado']);
            return;
        }
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Pedidos::validar($data);
        if ($errores !== []) {
            $this->respuesta(400, ['estado' => false, 'errores' => $errores]);
            return;
        }
        Pedidos::update($id, $data);
        $this->respuesta(200, ['estado' => true, 'mensaje' => 'Pedido actualizado correctamente']);
    }

    public function eliminar(int $id): void
    {
        if (count(Pedidos::find($id)) === 0) {
            $this->respuesta(404, ['estado' => false, 'mensaje' => 'Pedido no encontrado']);
            return;
        }
        Pedidos::delete($id);
        $this->respuesta(200, ['estado' => true, 'mensaje' => 'Pedido eliminado correctamente']);
    }

    private function datosJson(): ?array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $this->respuesta(400, ['estado' => false, 'errores' => ['El cuerpo debe ser un JSON valido']]);
            return null;
        }
        return $data;
    }

    private function respuesta(int $estado, array $datos): void
    {
        http_response_code($estado);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos);
    }
}