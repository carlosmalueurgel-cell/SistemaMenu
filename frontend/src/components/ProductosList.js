import { mountCrudList } from './crudList.js';

export const getProductosList = () => mountCrudList({
    rootId: 'productosTableList', endpoint: 'productos', title: 'Productos',
    columns: [
        { key: 'id' }, { key: 'codBarras' }, { key: 'descripcion' },
        { key: 'stock' }, { key: 'precio_unitario', value: row => `Bs ${Number(row.precio_unitario).toFixed(2)}` },
    ],
    fields: [
        { name: 'codBarras', label: 'Código de barras', required: true, maxLength: 100 },
        { name: 'descripcion', label: 'Descripción', required: true, maxLength: 100 },
        { name: 'stock', label: 'Stock', type: 'number', min: 0, step: 1, required: true, default: 0 },
        { name: 'precio_unitario', label: 'Precio unitario', type: 'number', min: 0, step: '0.01', required: true, default: 0 },
    ],
});
