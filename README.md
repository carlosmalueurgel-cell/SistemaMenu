# Sistema Menu

API PHP y panel web para administrar usuarios, clientes y productos.

## Requisitos

- PHP 8.1 o superior con las extensiones `pdo` y `pdo_mysql`.
- MySQL 8 o compatible.
- Node.js 18 o superior para servir el frontend.

## Configuración

1. Copia `api/.env.example` como `api/.env` y completa las credenciales de tu base de datos. El archivo `.env` no se debe subir al repositorio.
2. Selecciona esa base de datos e importa, en este orden, `api/data/DBmenu.sql` y `api/data/llenarDatosDBmenu.sql`.
3. Ejecuta la API desde la raíz del proyecto:

   ```powershell
   php -S localhost:8000 -t api/public
   ```

4. En otra terminal, sirve el frontend:

   ```powershell
   npx serve frontend
   ```

5. Antes de cargar el frontend, configura la URL local de la API en la consola del navegador y recarga la página:

   ```javascript
   window.SISTEMA_MENU_API_URL = 'http://localhost:8000/'
   ```

## Rutas principales

- `GET|POST /users`, `GET|PUT|DELETE /users/{id}`
- `GET|POST /clientes`, `GET|PUT|DELETE /clientes/{id}`
- `GET|POST /productos`, `GET|PUT|DELETE /productos/{id}`

Los ejemplos de cada endpoint están en `api/doc/`.
