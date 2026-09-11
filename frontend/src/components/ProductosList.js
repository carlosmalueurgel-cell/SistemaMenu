import { api } from '../utils/api.js';

export const getProductosList = async () => {
    const container = document.getElementById('productosTableList');
    container.innerHTML = '<tr><td class="px-6 py-4 text-slate-500" colspan="5">Cargando...</td></tr>';
    try {
        const productos = await api.get('productos');
        if (productos.length === 0) {
            container.innerHTML = '<tr><td class="px-6 py-4 text-slate-500" colspan="5">No hay productos registrados.</td></tr>';
            return;
        }
        container.innerHTML = productos.map(producto => `<tr><td class="px-6 py-4">${producto.id}</td><td class="px-6 py-4">${producto.codBarras}</td><td class="px-6 py-4">${producto.descripcion}</td><td class="px-6 py-4">${producto.stock}</td><td class="px-6 py-4">Bs ${Number(producto.precio_unitario).toFixed(2)}</td></tr>`).join('');
    } catch (error) {
        container.innerHTML = '<tr><td class="px-6 py-4 text-red-600" colspan="5">Error al cargar productos.</td></tr>';
    }
};
