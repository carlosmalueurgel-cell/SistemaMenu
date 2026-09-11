import { getUserList } from './components/UserList.js';
import { getClientesList } from './components/ClientesList.js';
import { getProductosList } from './components/ProductosList.js';

const app = document.getElementById('app');
const links = document.querySelectorAll('[data-view]');

const views = {
    home: async () => {
        const res = await fetch('./src/views/home.html');
        app.innerHTML = await res.text();
    },
    users: async () => {
        const res = await fetch('./src/views/user.html');
        app.innerHTML = await res.text();
        await getUserList();
    },
    clientes: async () => {
        const res = await fetch('./src/views/clientes.html');
        app.innerHTML = await res.text();
        await getClientesList();
    },
    productos: async () => {
        const res = await fetch('./src/views/productos.html');
        app.innerHTML = await res.text();
        await getProductosList();
    },
};

const setActiveLink = (view) => {
    links.forEach(link => {
        link.classList.toggle('bg-white/10', link.dataset.view === view);
        link.classList.toggle('text-white', link.dataset.view === view);
    });
};

links.forEach(link => {
    link.addEventListener('click', async (event) => {
        event.preventDefault();
        const view = link.dataset.view;
        if (views[view]) {
            await views[view]();
            setActiveLink(view);
        }
    });
});

views.home();
setActiveLink('home');
