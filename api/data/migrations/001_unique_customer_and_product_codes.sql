-- Ejecutar una sola vez sobre bases ya existentes, después de respaldarlas.
-- Antes de continuar, comprobar que ambas consultas devuelvan cero filas:
-- SELECT ci, COUNT(*) FROM CLIENTES GROUP BY ci HAVING COUNT(*) > 1;
-- SELECT codBarras, COUNT(*) FROM PRODUCTOS GROUP BY codBarras HAVING COUNT(*) > 1;
-- Resolver duplicados manualmente antes de crear los índices únicos.
CREATE UNIQUE INDEX uq_clientes_ci ON CLIENTES (ci);
CREATE UNIQUE INDEX uq_productos_codBarras ON PRODUCTOS (codBarras);
