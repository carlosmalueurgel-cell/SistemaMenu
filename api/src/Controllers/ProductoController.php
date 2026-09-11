<?php

require_once __DIR__ . '/../Models/Productos.php';

class ProductoController
{
    public function getAll(): void { $this->respuesta(200, Productos::all()); }

    public function getById(int $id): void
    {
        $productos = Productos::find($id);
        if ($productos === []) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Producto no encontrado']); return; }
        $this->respuesta(200, $productos[0]);
    }

    public function add(): void
    {
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Productos::validar($data);
        if ($errores !== []) { $this->respuesta(400, ['estado' => false, 'errores' => $errores]); return; }
        $id = Productos::add($data);
        $this->respuesta(201, ['estado' => true, 'mensaje' => 'Producto creado correctamente', 'id' => (int) $id]);
    }

    public function actualizar(int $id): void
    {
        if (Productos::find($id) === []) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Producto no encontrado']); return; }
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Productos::validar($data);
        if ($errores !== []) { $this->respuesta(400, ['estado' => false, 'errores' => $errores]); return; }
        Productos::update($id, $data);
        $this->respuesta(200, ['estado' => true, 'mensaje' => 'Producto actualizado correctamente']);
    }

    public function eliminar(int $id): void
    {
        if (Productos::find($id) === []) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Producto no encontrado']); return; }
        Productos::delete($id);
        $this->respuesta(200, ['estado' => true, 'mensaje' => 'Producto eliminado correctamente']);
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
        echo json_encode($datos);
    }
}
