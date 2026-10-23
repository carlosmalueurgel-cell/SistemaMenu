import { mountCrudList } from './crudList.js';

export const getUserList = () => mountCrudList({
    rootId: 'userTableList', endpoint: 'users', title: 'Usuarios',
    columns: [
        { key: 'id' }, { key: 'username' },
        { key: 'estado', value: row => Number(row.estado) === 1 ? 'Activo' : 'Inactivo' },
        { key: 'cod_empleado', value: row => `${row.cod_empleado} - ${[row.empleado_nombre, row.empleado_apellidos].filter(Boolean).join(' ')}` },
    ],
    fields: [
        { name: 'username', label: 'Nombre de usuario', required: true, maxLength: 50 },
        { name: 'password', label: 'Contraseña', type: 'password', required: true, optionalOnEdit: true },
        { name: 'cod_empleado', label: 'ID del empleado', type: 'number', min: 1, step: 1, required: true },
        { name: 'estado', label: 'Estado', type: 'select', required: true, default: '1', options: [{ value: '1', label: 'Activo' }, { value: '0', label: 'Inactivo' }] },
    ],
});
