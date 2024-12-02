import AlertConfig from "./AlertConfig";
import Toast from "./Toast";

export default {
    props: {
        /**
         * Configuración de la notificación
         */
        config: AlertConfig,
    },
    data() {
        return {
            /**
             * <code>true</code> si el mouse está arriba de la alerta y <code>false</code> si no
             */
            toastMouseHover: false,
        };
    },
    methods: {
        close() {
            this.$el.classList.remove("show");
        },
    },
    mounted() {
        const toast = new Toast(this.$el, {
            delay: 3500,
        });
        toast.show();
        //Actualizar barra de progreso
        const progress = this.$el.querySelector(".toast-progress");
        let progressWidth = 100;
        const interval = setInterval(() => {
            progress.style.width = `${progressWidth}%`;
            if (progressWidth < 0) {
                clearInterval(interval);
                this.close();
            }
            if (!this.toastMouseHover) progressWidth--;
        }, 30);
        //Detectar si el puntero se encuentra encima o fuera de la notificación
        this.$el.addEventListener("mouseover", () => {
            this.toastMouseHover = true;
            progressWidth = 100;
        });
        this.$el.addEventListener("mouseleave", () => {
            this.toastMouseHover = false;
        });
    },
};
