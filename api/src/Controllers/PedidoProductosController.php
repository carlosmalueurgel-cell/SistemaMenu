<?php

require_once __DIR__ . '/../Models/PedidoProductos.php';

class PedidoProductosController
{
    public function getAll(): void
    {
        $this->respuesta(200, PedidoProductos::all());
    }

    public function getById(int $id): void
    {
        $detalle = PedidoProductos::find($id);
        if (count($detalle) === 0) {
            $this->respuesta(404, ['estado' => false, 'mensaje' => 'Detalle de pedido no encontrado']);
            return;
        }
        $this->respuesta(200, $detalle[0]);
    }

    public function add(): void
    {
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = PedidoProductos::validar($data);
        if ($errores !== []) {
            $this->respuesta(400, ['estado' => false, 'errores' => $errores]);
            return;
        }
        $id = PedidoProductos::add($data);
        $this->respuesta(201, ['estado' => true, 'mensaje' => 'Detalle de pedido creado correctamente', 'id' => (int) $id]);
    }

    public function actualizar(int $id): void
    {
        if (count(PedidoProductos::find($id)) === 0) {
            $this->respuesta(404, ['estado' => false, 'mensaje' => 'Detalle de pedido no encontrado']);
            return;
        }
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = PedidoProductos::validar($data);
        if ($errores !== []) {
            $this->respuesta(400, ['estado' => false, 'errores' => $errores]);
            return;
        }
        PedidoProductos::update($id, $data);
        $this->respuesta(200, ['estado' => true, 'mensaje' => 'Detalle de pedido actualizado correctamente']);
    }

    public function eliminar(int $id): void
    {
        if (count(PedidoProductos::find($id)) === 0) {
            $this->respuesta(404, ['estado' => false, 'mensaje' => 'Detalle de pedido no encontrado']);
            return;
        }
        PedidoProductos::delete($id);
        $this->respuesta(200, ['estado' => true, 'mensaje' => 'Detalle de pedido eliminado correctamente']);
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