import { api } from '../utils/api.js';

export const getUserList = async () => {
    const container = document.getElementById('userTableList');
    container.innerHTML = '<tr><td class="px-6 py-4 text-slate-500" colspan="4">Cargando...</td></tr>';
    try {

        const users =await api.get('users');
        container.innerHTML = users.map(user => `
            <tr>
                <td class="px-6 py-4">${user.id}</td>
                <td class="px-6 py-4">${user.username}</td>
                <td class="px-6 py-4">${Number(user.estado) === 1 ? 'Activo' : 'Inactivo'}</td>
                <td class="px-6 py-4">${user.cod_empleado}</td>
            </tr>
        `).join('');
    } catch (error) {
        container.innerHTML = '<tr><td class="px-6 py-4 text-red-600" colspan="4">Error al cargar usuarios.</td></tr>';
    }
};
