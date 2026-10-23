# Sistema Menu

API PHP y panel web para administrar usuarios, clientes y productos.

## Requisitos

- PHP 8.1 o superior con las extensiones `pdo` y `pdo_mysql`.
- MySQL 8 o compatible.
- Node.js 18 o superior para servir el frontend.

## Configuración

1. Crea `api/.env` (no se debe subir al repositorio) con `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_PORT` y `DB_CHARSET`, y completa sus valores con las credenciales de tu base de datos. `FRONTEND_ORIGIN` es opcional y por defecto vale `http://localhost:3000`; configúralo con el origen exacto del frontend si usas otro puerto o dominio.
2. Selecciona esa base de datos e importa, en este orden, `api/data/DBmenu.sql` y `api/data/llenarDatosDBmenu.sql`.
   El script de datos es para una base vacía y no debe importarse repetidamente. Los registros de usuario del ejemplo contienen contraseñas de demostración sin hash: no uses esos datos en producción.
   Para una base existente, revisa los duplicados indicados al inicio de `api/data/migrations/001_unique_customer_and_product_codes.sql` y ejecuta esa migración una sola vez si no hay duplicados.
3. Ejecuta la API desde la raíz del proyecto:

   ```powershell
   php -S localhost:8000 -t api/public
   ```

4. En otra terminal, sirve el frontend:

   ```powershell
   npx serve frontend
   ```

   Para regenerar los estilos, instala las dependencias en `frontend/` con `npm ci` y ejecuta allí `npm run dev`.

5. El frontend consulta `http://api.sistemamenu/` por defecto. Si inicias la API con el servidor PHP del paso 3, declara `window.SISTEMA_MENU_API_URL = 'http://localhost:8000/'` **antes** de cargar `src/main.js` en `frontend/index.html`. La API solo permite por CORS el origen exacto indicado en `FRONTEND_ORIGIN`; si no se define, permite `http://localhost:3000`.

## Rutas principales

- `GET|POST /users`, `GET|PUT|DELETE /users/{id}`
- `GET|POST /clientes`, `GET|PUT|DELETE /clientes/{id}`
- `GET|POST /productos`, `GET|PUT|DELETE /productos/{id}`

Los ejemplos de cada endpoint están en `api/doc/`.
