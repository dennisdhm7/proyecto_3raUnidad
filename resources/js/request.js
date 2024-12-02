import axios from "axios";

import Utils from "./Utils";
const token = Utils.token();
axios.defaults.headers.common = {
    Authorization: "Bearer " + token,
    Accept: "application/json",
};

axios.interceptors.response.use(
    (response) => {
        if (response.config.method != "get") {
            if (window.emitter != undefined) {
                window.emitter.emit("notifications-update");
            }
        }
        return response;
    },
    (error) => {
        //TODO (Quitar depuración)
        console.log(error.response);
        if (error.response.config.method != "get") {
            Utils.error(error.response);
        }
        // Puedes manejar otros códigos de estado aquí si es necesario
        throw error;
    }
);

export default axios;
