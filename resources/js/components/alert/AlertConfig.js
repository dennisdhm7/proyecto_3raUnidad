/**
 * @class Maneja las opciones de configuración y personalización de las alertas
 */
export default class AlertConfig {
    /**
     * @param {String} title: Título de la notificación
     * @param {String} text: Contenido de la notificación
     * @param {String} icon: Ícono a mostrar en la notificación
     * @param {String} color: Color a aplicar sobre el ícono de la notificación y la barra de progreso en formato "#argb"
     */
    constructor({ title = "Información", text = "", icon = "", color = "" }) {
        /**
         * @member {String}: Título de la notificación
         */
        this.title = title
        /**
         * @member {String}: Contenido de la notificación
         */
        this.text = text
        /**
         * @member {String}: Ícono a mostrar en la notificación
         */
        this.icon = icon
        /**
         * @member {String}: Color a aplicar sobre el ícono de la notificación y la barra de progreso en formato "#argb"
         */
        this.color = color
    }
}
