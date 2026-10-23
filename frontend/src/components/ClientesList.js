import { mountCrudList } from './crudList.js';

export const getClientesList = () => mountCrudList({
    rootId: 'clientesTableList', endpoint: 'clientes', title: 'Clientes',
    columns: [
        { key: 'id' }, { key: 'ci' },
        { key: 'nombre', value: row => `${row.nombre ?? ''} ${row.apellidos ?? ''}`.trim() },
        { key: 'direccion' }, { key: 'telefono' },
    ],
    fields: [
        { name: 'ci', label: 'CI', required: true, maxLength: 20 },
        { name: 'nombre', label: 'Nombre', required: true, maxLength: 50 },
        { name: 'apellidos', label: 'Apellidos', required: true, maxLength: 50 },
        { name: 'direccion', label: 'Dirección', maxLength: 250 },
        { name: 'telefono', label: 'Teléfono', maxLength: 15 },
    ],
});
