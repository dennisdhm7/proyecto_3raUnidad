import Alert from "./components/alert/Alert";
import AlertConfig from "./components/alert/AlertConfig";

export default class Utils {
    /**
     * Días de la semana
     */
    static DAY_OFF_WEEK = [
        "Domingo",
        "Lunes",
        "Martes",
        "Miércoles",
        "Jueves",
        "Viernes",
        "Sábado",
    ];

    /**
     * Meses del año
     */
    static MONTHS = [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
    ];

    /**
     * Configuración para usar en la función datetime
     */
    static FULL_DATE_TIME = {
        day: "2-digit",
        month: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    };

    /**
     * Retorna la fecha y hora en formato String
     * @param {Date|undefined} datetime Fecha y hora o undefined si se usará la fecha y hora actual
     * @param {Object} format Formato de la cadena resultante
     *
     * @returns Fecha y hora de datetime en formato de texto
     */
    static datetime(datetime, format) {
        if (datetime.length <= 11) {
            datetime += " 00:00:00";
        }
        const date = new Date(datetime);
        return date.toLocaleDateString("es", format);
    }

    /**
     * Retorna una cadena de texto con la fecha de date o "Sin especificar" si date es null o undefined
     * @param {Date} date Fecha a convertir en texto
     * @returns Fecha de date en formato de texto
     */
    static date(date) {
        if (date == null || date == undefined) {
            return "Sin especificar";
        }
        if (date.length <= 11) {
            date += " 00:00:00";
        }
        date = new Date(date);
        return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
    }

    /**
     * Retorna una cadena de texto con el precio formateado
     * @param {Number} price Precio a formatear
     * @returns {String} Precio formateado
     */
    static currency(price) {
        let currency = "USD";

        return `${Number(price)} ${currency}`;
    }

    /**
     * Retorna el token de la sesión
     * @returns {String} Retorna el token de la sesión
     */
    static token() {
        return localStorage.getItem("jwt");
    }

    /**
     * Retorna los datos del usuario logueado
     *
     * @returns {Object|null} Datos del usuario o null si no se encontró un usuario logueado
     */
    static user() {
        if (localStorage.getItem("user") != null) {
            return JSON.parse(localStorage.getItem("user"));
        } else {
            return null;
        }
    }

    /**
     * Retorna los datos del negocio logueado
     *
     * @returns {Object|null} Datos del usuario o null si no se encontró un usuario logueado
     */
    static business() {
        if (localStorage.getItem("user") != null) {
            return JSON.parse(localStorage.getItem("user")).business;
        } else {
            return null;
        }
    }

    /**
     * Reglas de validación comunes para los formularios
     */
    static RULES = {
        required: (value) => !!value || "Este campo es obligatorio",
        email: (value) =>
            !!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ||
            "El correo electrónico no es válido",
        phone: (value) =>
            !!/^\+?[1-9]\d{1,14}$/.test(value) ||
            "El número de teléfono no es válido",
        username: (value) =>
            !!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/.test(value) ||
            "El campo no puede contener caracteres especiales, numéricos o espacios en blanco",
        path: (value) =>
            !!/^[a-zA-Z0-9áéíóúÁÉÍÓÚüÜñÑ-]+$/.test(value) ||
            "El campo no puede contener caracteres especiales o espacios en blanco",
        name: (value) =>
            !!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s-]+$/.test(value) ||
            "El campo no puede contener caracteres especiales ni numéricos",
        fullname: (value) =>
            !!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\d-]+$/.test(value) ||
            "El campo no puede contener caracteres especiales ni numéricos",
        currency: (value) =>
            !!/^\d+(,\d{3})*(\.\d{2})?$/.test(value) ||
            "El campo no tiene un formato de moneda válido",
        alphanumeric: (value) =>
            !!/^[A-Za-z\d]+$/.test(value) ||
            "El campo solo puede contener caracteres alfanuméricos",
        rut: (value) =>
            !!/^\d{1,2}\.\d{3}\.\d{3}-[\dkK]$/.test(value) ||
            "El campo no tiene un formato RUT válido (XX.XXX.XXX-Y)",
        number: (value) =>
            !!/^\d+$/.test(value) ||
            "El campo no tiene un formato de número válido",
        password: (value) =>
            !!/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/.test(value) ||
            "La longitud de la contraseña debe ser mínimo 6 caracteres, con al menos una mayúscula y una minúscula",
        card: (value) =>
            !!/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|3[47][0-9]{13}|6(?:011|5[0-9]{2})[0-9]{12}|(?:2131|1800|35\d{3})\d{11})(?:[\s-]?[0-9]{4})*$/.test(
                value
            ) || "Debes proporcionar un número de tarjeta válido",
        expire: (value) =>
            !!/^(0[1-9]|1[0-2])\/(20[2-9][0-9])$/.test(
                value
            ) || "La fecha de vencimiento no es válida",
        ccv: (value) =>
            !!/^\d{3,4}$/.test(
                value
            ) || "El CCV no es válido",
    };

    /**
     * Verificar si el usuario logueado tiene permiso para realizar una acción determinada según su permiso o su rol
     * @param {String} action
     * @returns <code>true</code> si el usuario tiene permiso para realizar la acción o <code>false</code> si no
     */
    static isAuthorized(action) {
        const user = Utils.user();
        if (user.is_admin == 1) {
            return true;
        } else {
            for (let permission of user.permissions) {
                if (permission.name == action) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Obtener un identificador completado con ceros (0) y con un identificador de texto al inicio (Ejemplo D0005)
     * @param {Number} code Código identificativo
     * @param {String} first Letra o letras iniciales de la cadena
     * @param {Number} length Tamaño máximo de la cadena resultante
     * @returns
     */
    static getCode(code, first, length) {
        return first + code.toString().padStart(length, "0");
    }

    /**
     *
     * @param {Array} columns Array con las columnas por defecto que tiene el módulo
     * @param {String} name Identificador del módulo (para leer las columnas guardadas en el localstorage)
     * @returns
     */
    static loadColumns(columns, name) {
        //Obtener las columnas visibles guardadas en el navegador o todas las columnas
        const saved = localStorage.getItem(`${name}_columns`);
        if (saved != null) {
            try {
                return JSON.parse(saved).slice();
            } catch (error) {
                return columns.slice();
            }
        } else {
            return columns.slice();
        }
    }

    /**
     * Ocultar o mostrar una columna de una tabla
     *
     * @param {Boolean} checked true si es para mostrar o false para ocultar
     * @param {Array} headers Instancia a la propiedad header de la tabla
     * @param {Object} column Datos de la columna original
     * @param {Sring} id Id de la columna a mostrar u ocultar
     */
    static toggleColumn(checked, headers, column, id) {
        if (checked) {
            headers.push(column);
            headers = headers.sort((a, b) => a.index - b.index);
        } else {
            for (let i in headers) {
                if (column.key == headers[i].key) {
                    headers.splice(i, 1);
                    break;
                }
            }
        }
        localStorage.setItem(`${id}_columns`, JSON.stringify(headers));
    }

    /**
     * Obtener la visibilidad de una columna en la tabla
     *
     * @param {Array} headers Instancia a las columnas de la tabla
     * @param {String} key Identificador de la columna
     * @returns <code>true</code> si la columna es visible o <code>false</code> si no
     */
    static isColumnVisibility(headers, key) {
        return headers.find((item) => item.key == key) != undefined;
    }

    static error(response) {
        let title = "",
            text = "";
        switch (response.status) {
            case 500:
                title = "¡Error en el servidor!";
                text =
                    "Ha ocurrido un error al comunicarse con el servidor, intente nuevamente";
                break;
            case 422:
                title = "¡Error de validación!";
                text = response.data.message;
                break;
            case 401:
                title = "¡Error de autenticación!";
                text =
                    "No está autenticado o no tiene permisos para realizar esta acción";
                break;
            default:
                title = "¡Error desconocido!";
                text = "Lo sentimos, tuvimos un error desconocido";
                break;
        }
        const alert = new Alert(
            new AlertConfig({
                title: title,
                text: text,
                icon: "mdi mdi-alert-octagon-outline",
                color: "#f55555",
            })
        );
        alert.fire();
    }

    static notify(title, text, icon, color) {
        const alert = new Alert(
            new AlertConfig({
                title: title,
                text: text,
                icon: icon,
                color: color,
            })
        );
        alert.fire();
    }

    static copyToClipboard(content, message) {
        if (!navigator.clipboard) {
            const alert = new Alert(
                new AlertConfig({
                    title: "¡Error al copiar!",
                    text: "Su navegador no soporta la función de copiar al portapapeles.",
                    icon: "mdi mdi-alert-octagon-outline",
                    color: "#f55555",
                })
            );
            alert.fire();
            return;
        } else {
            navigator.clipboard
                .writeText(content)
                .then(() => {
                    message.text = "Se ha copiado al portapapeles";
                    message.open = true;
                })
                .catch((error) => {
                    console.log(error);
                });
        }
    }
}
