import "./bootstrap";
import "../css/app.css";
import "cropperjs/dist/cropper.css";

//Importar íconos
import "@mdi/font/css/materialdesignicons.css";

//Importar y configurar axios
import axios from "axios";
axios.defaults.baseURL = import.meta.env.VITE_APP_URL;

//Importar la aplicación base de Vue
import { createApp } from "vue";
import App from "./App.vue";

//Importar clase de utilidades
import Utils from "./Utils";

//Importar manejador de eventos
import mitt from "mitt";

//Configurar manejador de eventos
const app = createApp(App);
app.config.globalProperties.Utils = Utils;
const emitter = mitt();
app.config.globalProperties.emitter = emitter;

//Crear y configurar las rutas de vue-router
import { createWebHistory, createRouter } from "vue-router";
import routes from "./router/routes";

//Configirar rutas
const router = createRouter({
    history: createWebHistory("/"),
    routes,
    //Usar el path como ruta (require configuración en Laravel)
    mode: "history",
    //Simule un comportamiento de desplazamiento similar al nativo al navegar a una nueva
    //ruta y utilizar los botones de avance/retroceso.
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0, left: 0 };
        }
    },
});

//Obtener usuario logueado cada vez que se lea una ruta o retornar al login si da error
router.beforeEach(async (routeTo, routeFrom, next) => {
    const authRequired = routeTo.matched.some(
        (route) => route.meta.authRequired
    );

    if (!authRequired) return next();

    //Incluir el token de sesión
    axios.defaults.headers.common["authorization"] =
        "Bearer " + localStorage.getItem("jwt");
    try {
        //Hacer petición para obtener el usuario
        await axios
            .get("api/user")
            .then((response) => {
                localStorage.setItem("userid", response.data.id);
                localStorage.setItem("user", JSON.stringify(response.data));
                window.emitter = emitter;
                next();
            })
            .catch(() => {
                //Retornar al login
                next({
                    name: "login",
                    query: { redirectFrom: routeTo.fullPath },
                });
            });
    } catch (e) {}
});

//Mostrar y ocultar animación de carga
router.beforeEach((routeTo, routeFrom, next) => {
    //Comenzar animación de carga
    emitter.emit("loading", true);
    next();
});

//Configuración adicional de vue-router (revisar)
router.beforeResolve(async (routeTo, routeFrom, next) => {
    // Create a `beforeResolve` hook, which fires whenever
    // `beforeRouteEnter` and `beforeRouteUpdate` would. This
    // allows us to ensure data is fetched even when params change,
    // but the resolved route does not. We put it in `meta` to
    // indicate that it's a hook we created, rather than part of
    // Vue Router (yet?).

    try {
        // For each matched route...
        for (const route of routeTo.matched) {
            await new Promise((resolve, reject) => {
                // If a `beforeResolve` hook is defined, call it with
                // the same arguments as the `beforeEnter` hook.
                if (route.meta && route.meta.beforeResolve) {
                    route.meta.beforeResolve(routeTo, routeFrom, (...args) => {
                        // If the user chose to redirect...
                        if (args.length) {
                            // If redirecting to the same route we're coming from...
                            // Complete the redirect.
                            next(...args);
                            reject(new Error("Redirected"));
                        } else {
                            resolve();
                        }
                    });
                } else {
                    // Otherwise, continue resolving the route.
                    resolve();
                }
            });
        }
        // If a `beforeResolve` hook chose to redirect, just return.
    } catch (error) {
        return;
    }
    //Establecer nombre de la ruta
    //TODO Obtener nombre del negocio
    document.title = routeTo.meta.title + " | " + "Restaurante Capitán Chino" ;
    // If we reach this point, continue resolving the route.
    next();
});

//Ocultar animación de carga
router.afterEach(() => {
    emitter.emit("loading", false);
});

//Correr la aplicación
app.use(router);

//Importar componentes UI prediseñados
import "vuetify/styles";
import { createVuetify } from "vuetify";

const vuetify = createVuetify();
app.use(vuetify);
app.mount("#app");
