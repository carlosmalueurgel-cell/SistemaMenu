import { api } from '../utils/api.js';

const escaparHtml = (valor) => String(valor ?? '').replace(/[&<>"']/g, caracter => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
}[caracter]));

const empleadoTexto = (user) => {
    const nombre = [user.empleado_nombre, user.empleado_apellidos].filter(Boolean).join(' ');
    return nombre ? `${user.cod_empleado} - ${nombre}` : (user.cod_empleado ?? 'Sin empleado relacionado');
};

export const getUserList = async () => {
    const container = document.getElementById('userTableList');
    container.innerHTML = '<tr><td class="px-6 py-4 text-slate-500" colspan="4">Cargando...</td></tr>';
    try {

        const users = await api.get('users');
        if (users.length === 0) {
            container.innerHTML = '<tr><td class="px-6 py-4 text-slate-500" colspan="4">No hay usuarios registrados.</td></tr>';
            return;
        }
        container.innerHTML = users.map(user => `
            <tr>
                <td class="px-6 py-4">${escaparHtml(user.id)}</td>
                <td class="px-6 py-4">${escaparHtml(user.username)}</td>
                <td class="px-6 py-4">${Number(user.estado) === 1 ? 'Activo' : 'Inactivo'}</td>
                <td class="px-6 py-4">${escaparHtml(empleadoTexto(user))}</td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
        container.innerHTML = `<tr><td class="px-6 py-4 text-red-600" colspan="4">No se pudieron cargar los usuarios: ${escaparHtml(error.message)}</td></tr>`;
    }
};
