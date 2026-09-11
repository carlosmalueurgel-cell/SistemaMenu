import { api } from '../utils/api.js';

const texto = (valor) => valor ?? '—';

export const getClientesList = async () => {
    const container = document.getElementById('clientesTableList');
    container.innerHTML = '<tr><td class="px-6 py-4 text-slate-500" colspan="6">Cargando...</td></tr>';
    try {
        const clientes = await api.get('clientes');
        if (clientes.length === 0) {
            container.innerHTML = '<tr><td class="px-6 py-4 text-slate-500" colspan="6">No hay clientes registrados.</td></tr>';
            return;
        }
        container.innerHTML = clientes.map(cliente => `<tr><td class="px-6 py-4">${cliente.id}</td><td class="px-6 py-4">${texto(cliente.ci)}</td><td class="px-6 py-4">${texto(cliente.nombre)}</td><td class="px-6 py-4">${texto(cliente.apellidos)}</td><td class="px-6 py-4">${texto(cliente.direccion)}</td><td class="px-6 py-4">${texto(cliente.telefono)}</td></tr>`).join('');
    } catch (error) {
        container.innerHTML = '<tr><td class="px-6 py-4 text-red-600" colspan="6">Error al cargar clientes.</td></tr>';
    }
};
