<?php

require_once __DIR__ . '/../Models/Productos.php';

class ProductoController
{
    public function getAll(): void
    {
        try { $this->respuesta(200, Productos::all()); }
        catch (Throwable $error) { $this->respuesta(503, ['estado' => false, 'mensaje' => 'No se pudo consultar productos']); }
    }

    public function getById(int $id): void
    {
        try { $productos = Productos::find($id); }
        catch (Throwable $error) { $this->respuesta(503, ['estado' => false, 'mensaje' => 'No se pudo consultar productos']); return; }
        if ($productos === []) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Producto no encontrado']); return; }
        $this->respuesta(200, $productos[0]);
    }

    public function add(): void
    {
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Productos::validar($data);
        if ($errores !== []) { $this->respuesta(400, ['estado' => false, 'errores' => $errores]); return; }
        try {
            if (Productos::codigoExiste($data['codBarras'])) { $this->respuesta(409, ['estado' => false, 'mensaje' => 'El código de barras ya está registrado']); return; }
            $id = Productos::add($data);
        } catch (Throwable $error) { $this->errorBaseDatos($error, 'guardar'); return; }
        $this->respuesta(201, ['estado' => true, 'mensaje' => 'Producto creado correctamente', 'id' => (int) $id]);
    }

    public function actualizar(int $id): void
    {
        try { if (Productos::find($id) === []) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Producto no encontrado']); return; } }
        catch (Throwable $error) { $this->respuesta(503, ['estado' => false, 'mensaje' => 'No se pudo consultar productos']); return; }
        $data = $this->datosJson();
        if ($data === null) return;
        $errores = Productos::validar($data);
        if ($errores !== []) { $this->respuesta(400, ['estado' => false, 'errores' => $errores]); return; }
        try {
            if (Productos::codigoExiste($data['codBarras'], $id)) { $this->respuesta(409, ['estado' => false, 'mensaje' => 'El código de barras ya está registrado']); return; }
            Productos::update($id, $data);
        } catch (Throwable $error) { $this->errorBaseDatos($error, 'actualizar'); return; }
        $this->respuesta(200, ['estado' => true, 'mensaje' => 'Producto actualizado correctamente']);
    }

    public function eliminar(int $id): void
    {
        try {
            if (Productos::find($id) === []) { $this->respuesta(404, ['estado' => false, 'mensaje' => 'Producto no encontrado']); return; }
            Productos::delete($id);
        } catch (Throwable $error) { $this->errorBaseDatos($error, 'eliminar'); return; }
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
        echo json_encode($datos, JSON_INVALID_UTF8_SUBSTITUTE);
    }

    private function errorBaseDatos(Throwable $error, string $accion): void
    {
        $codigo = $error instanceof PDOException ? (string) $error->getCode() : (string) $error->getPrevious()?->getCode();
        if ($codigo === '23000') { $this->respuesta(409, ['estado' => false, 'mensaje' => 'No se puede completar la operación: hay datos duplicados o registros relacionados']); return; }
        $this->respuesta(503, ['estado' => false, 'mensaje' => "No se pudo $accion el producto por un problema de base de datos"]);
    }
}
