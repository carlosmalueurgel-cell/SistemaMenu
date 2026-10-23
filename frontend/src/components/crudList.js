import { api } from '../utils/api.js';

const esc = (value) => String(value ?? '').replace(/[&<>"']/g, char => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
}[char]));

export async function mountCrudList({ rootId, endpoint, title, columns, fields }) {
    const root = document.getElementById(rootId);
    const section = root.closest('section');
    const toolbar = document.createElement('div');
    toolbar.className = 'flex flex-wrap items-center gap-3 border-b border-slate-200 px-6 py-4';
    toolbar.innerHTML = `<label class="sr-only" for="${rootId}-search">Buscar ${esc(title.toLowerCase())}</label><input id="${rootId}-search" type="search" class="min-w-48 flex-1 rounded-md border border-slate-300 px-3 py-2" placeholder="Buscar ${esc(title.toLowerCase())}"><button type="button" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Nuevo</button>`;
    section.insertBefore(toolbar, section.querySelector('.overflow-x-auto'));
    const status = document.createElement('p');
    status.className = 'px-6 py-2 text-sm';
    status.setAttribute('role', 'status');
    section.insertBefore(status, section.querySelector('.overflow-x-auto'));

    const dialog = document.createElement('dialog');
    dialog.className = 'w-[min(36rem,calc(100%-2rem))] rounded-xl p-0 shadow-xl backdrop:bg-slate-950/50';
    dialog.innerHTML = `<form class="space-y-4 p-6"><h3 class="text-lg font-semibold"></h3><div class="grid gap-4 sm:grid-cols-2"></div><p class="text-sm text-red-700" role="alert"></p><div class="flex justify-end gap-3"><button type="button" data-cancel class="rounded-md border px-4 py-2">Cancelar</button><button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-white">Guardar</button></div></form>`;
    section.append(dialog);

    let rows = [];
    let editing = null;
    const search = toolbar.querySelector('input');
    const table = root.closest('table');
    const actionHeader = document.createElement('th');
    actionHeader.className = 'px-6 py-3';
    actionHeader.textContent = 'Acciones';
    table.querySelector('thead tr').append(actionHeader);

    const render = () => {
        const query = search.value.trim().toLocaleLowerCase();
        const filtered = rows.filter(row => columns.some(column => String(row[column.key] ?? '').toLocaleLowerCase().includes(query)));
        if (!filtered.length) {
            root.innerHTML = `<tr><td class="px-6 py-4 text-slate-500" colspan="${columns.length + 1}">${rows.length ? 'No hay resultados.' : `No hay ${esc(title.toLowerCase())} registrados.`}</td></tr>`;
            return;
        }
        root.innerHTML = filtered.map(row => `<tr>${columns.map(column => `<td class="px-6 py-4">${esc(column.value ? column.value(row) : row[column.key])}</td>`).join('')}<td class="whitespace-nowrap px-6 py-4"><button type="button" data-edit="${esc(row.id)}" class="mr-3 text-blue-700 hover:underline">Editar</button><button type="button" data-delete="${esc(row.id)}" class="text-red-700 hover:underline">Eliminar</button></td></tr>`).join('');
    };

    const load = async () => {
        root.innerHTML = `<tr><td class="px-6 py-4 text-slate-500" colspan="${columns.length + 1}">Cargando ${esc(title.toLowerCase())}…</td></tr>`;
        try { rows = await api.get(endpoint); render(); }
        catch (error) { root.innerHTML = `<tr><td class="px-6 py-4 text-red-700" colspan="${columns.length + 1}">${esc(error.message)}</td></tr>`; }
    };

    const openForm = (row = null) => {
        editing = row;
        const form = dialog.querySelector('form');
        dialog.querySelector('h3').textContent = `${row ? 'Editar' : 'Nuevo'} ${title.toLowerCase()}`;
        const fieldsRoot = dialog.querySelector('.grid');
        fieldsRoot.replaceChildren();
        for (const field of fields) {
            const label = document.createElement('label');
            label.className = 'block text-sm font-medium text-slate-700';
            label.textContent = field.label;
            let input;
            if (field.type === 'select') {
                input = document.createElement('select');
                for (const option of field.options) {
                    const el = document.createElement('option'); el.value = option.value; el.textContent = option.label; input.append(el);
                }
            } else {
                input = document.createElement('input'); input.type = field.type || 'text';
            }
            input.name = field.name;
            input.className = 'mt-1 block w-full rounded-md border border-slate-300 px-3 py-2';
            if (field.required && !(row && field.optionalOnEdit)) input.required = true;
            if (field.min !== undefined) input.min = String(field.min);
            if (field.step !== undefined) input.step = String(field.step);
            if (field.maxLength !== undefined) input.maxLength = field.maxLength;
            input.value = row?.[field.name] ?? field.default ?? '';
            if (field.type === 'password' && row) input.placeholder = 'Deja vacío para conservar la contraseña';
            label.append(input); fieldsRoot.append(label);
        }
        dialog.querySelector('[role="alert"]').textContent = '';
        dialog.showModal();
        fieldsRoot.querySelector('input, select')?.focus();
    };

    toolbar.querySelector('button').addEventListener('click', () => openForm());
    search.addEventListener('input', render);
    dialog.querySelector('[data-cancel]').addEventListener('click', () => dialog.close());
    root.addEventListener('click', async event => {
        const editButton = event.target.closest('[data-edit]');
        const deleteButton = event.target.closest('[data-delete]');
        if (editButton) openForm(rows.find(row => String(row.id) === editButton.dataset.edit));
        if (deleteButton && window.confirm(`¿Eliminar este registro de ${title.toLowerCase()}?`)) {
            try { await api.delete(`${endpoint}/${encodeURIComponent(deleteButton.dataset.delete)}`); status.textContent = 'Registro eliminado.'; await load(); }
            catch (error) { status.textContent = error.message; status.className = 'px-6 py-2 text-sm text-red-700'; }
        }
    });
    dialog.querySelector('form').addEventListener('submit', async event => {
        event.preventDefault();
        const formData = Object.fromEntries(new FormData(event.currentTarget));
        for (const field of fields) {
            if (field.type === 'number' && formData[field.name] !== '') formData[field.name] = Number(formData[field.name]);
        }
        if (editing && fields.some(field => field.type === 'password' && field.optionalOnEdit) && !formData.password) delete formData.password;
        try {
            if (editing) await api.put(`${endpoint}/${encodeURIComponent(editing.id)}`, formData);
            else await api.post(endpoint, formData);
            dialog.close(); status.textContent = 'Cambios guardados correctamente.'; status.className = 'px-6 py-2 text-sm text-emerald-700'; await load();
        } catch (error) { dialog.querySelector('[role="alert"]').textContent = error.message; }
    });
    await load();
}
