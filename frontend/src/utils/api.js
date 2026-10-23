// Puede redefinirse antes de cargar main.js, por ejemplo en un despliegue.
const API_URL = (window.SISTEMA_MENU_API_URL || 'http://api.sistemamenu/').replace(/\/?$/, '/');
async function request(method, endpoint, data) {
    let response;
    try {
        response = await fetch(`${API_URL}${endpoint.replace(/^\//, '')}`, {
            method,
            headers: data ? { 'Content-Type': 'application/json' } : {},
            ...(data ? { body: JSON.stringify(data) } : {}),
        });
    } catch (error) {
        const frontendOrigin = window.location.origin;
        throw new Error(`No se pudo conectar con la API (${API_URL}) desde ${frontendOrigin}. Comprueba que la API sea accesible, que no haya bloqueo de contenido mixto y que FRONTEND_ORIGIN coincida exactamente con ${frontendOrigin}. Detalle: ${error.message}`);
    }
    const payload = await response.json().catch(() => null);
    if (!response.ok) throw new Error(payload?.mensaje || payload?.errores?.join(', ') || `Error HTTP ${response.status}`);
    return payload;
}

export const api={
    //funcion para obtener data de la api
    get: async (endpoint) =>{
        try{
            const payload = await request('GET', endpoint);
            if (!Array.isArray(payload)) {
                throw new Error('La API devolvió un formato de usuarios no válido.');
            }
            return payload;
        }catch (error) {
            console.error('Error al obtener data:',error);
            throw error;
        }
    },
    post: (endpoint, data) => request('POST', endpoint, data),
    put: (endpoint, data) => request('PUT', endpoint, data),
    delete: (endpoint) => request('DELETE', endpoint),

};

