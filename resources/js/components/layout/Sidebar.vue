<script>
import axios from "../../request";
export default {
    props: {
        active: String,
    },
    data() {
        return {
            modules: [],
            submodules: [],
            events: []
        }
    },
    methods: {
        update() {
            // axios.get('/api/statistics/sidebar').then((response) => {
            //     this.statistics = response.data
            // })
        }
    },
    created() {
        this.update()

        const types = ['added', 'deleted'];
        for (let event of this.events) {
            for (let type of types) {
                this.emitter.on(`${event}_${type}`, () => {
                    // if (type == 'added') {
                    //     this.statistics[event]++
                    // } else {
                    //     this.statistics[event]--
                    // }
                })
            }
        }

        this.emitter.on('statistics_updated', () => {
            this.update()
        })
    }
}
</script>

<template>
    <v-list>
        <p class="pb-1 text-xs text-gray-400">Cuenta</p>
        <v-list-item color="orange-darken-4" :active="active === ''" rounded="lg" class="my-0 px-1 py-2 w-full" link
            prepend-icon="mdi-home" title="Dashboard" to="/" subtitle="Estadísticas y más"></v-list-item>

        <v-list-item color="orange-darken-4" :active="active === 'users'" rounded="lg" class="my-0 px-1 py-2 w-full" link
            prepend-icon="mdi-account-multiple" title="Usuarios" to="/users" subtitle="Usuarios y permisos"></v-list-item>

        <v-divider class="my-2"></v-divider>
        <p class="pb-1 mt-2 text-xs text-gray-400">Menú</p>

        <v-list-item color="orange-darken-4" :active="active === 'categories'" rounded="lg" class="my-0 px-1 pb-2 w-full"
            link prepend-icon="mdi-tag-multiple" title="Categorías" to="/categories"
            subtitle="Categorías de tu menú"></v-list-item>

        <v-list-item color="orange-darken-4" :active="active === 'items'" rounded="lg" class="my-0 px-1 py-2 w-full" link
            prepend-icon="mdi-card-bulleted" title="Menú" to="/items" subtitle="Productos o servicios"></v-list-item>

        <v-divider class="my-2"></v-divider>
        <p class="pb-1 mt-2 text-xs text-gray-400">Gestión</p>

        <v-list-item color="orange-darken-4" :active="active === 'orders'" rounded="lg" class="my-0 px-1 py-2 w-full" link
            prepend-icon="mdi-cash-register" title="Pedidos" to="/orders" subtitle="Ver tus pedidos y ventas"></v-list-item>

        <v-list-item color="orange-darken-4" :active="active === 'reservations'" rounded="lg" class="my-0 px-1 py-2 w-full"
            link prepend-icon="mdi-calendar" title="Reservaciones" to="/reservations"
            subtitle="Administrar reservaciones"></v-list-item>
    </v-list>
</template>
