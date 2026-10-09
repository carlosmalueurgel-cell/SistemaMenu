// Puede redefinirse antes de cargar main.js, por ejemplo en un despliegue.
const API_URL = (window.SISTEMA_MENU_API_URL || 'http://api.sistemamenu/').replace(/\/?$/, '/');

export const api={
    //funcion para obtener data de la api
    get: async (endpoint) =>{
        try{
            const response = await fetch(`${API_URL}${endpoint.replace(/^\//, '')}`);
            const payload = await response.json().catch(() => null);
            if(!response.ok) {
                const detalle = payload?.mensaje || `HTTP ${response.status}`;
                throw new Error(detalle);
            }
            if (!Array.isArray(payload)) {
                throw new Error('La API devolvió un formato de usuarios no válido.');
            }
            return payload;
        }catch (error) {
            console.error('Error al obtener data:',error);
            throw error;
        }
    },

};

