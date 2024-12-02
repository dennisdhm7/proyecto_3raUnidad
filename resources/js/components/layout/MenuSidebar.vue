<script setup>
    import axios from "../../request"
    import { ref, onMounted, getCurrentInstance } from "vue"
    const { appContext } = getCurrentInstance()
    const emitter = appContext.config.globalProperties.emitter

    const props = defineProps({
        active: String,
        cart: Object,
    })

    const count = ref(0)

    function update() {
        let total = 0
        for (let item of props.cart.order.items) {
            total += item.quantity
        }
        count.value = total
    }

    onMounted(() => {
        emitter.on("cart-updated", (order) => {
            update()
        })

        update()
    })
</script>

<template>
    <v-list>
        <v-list-item color="orange-darken-4" :active="active === 'home'" rounded="lg" class="my-0 px-1 py-2 w-full" link prepend-icon="mdi-home" title="Inicio" to="/home" subtitle="Información sobre nosotros"></v-list-item>

        <v-list-item color="orange-darken-4" :active="active === 'cart'" rounded="lg" class="my-0 px-1 py-2 w-full" link prepend-icon="mdi-account-multiple" title="Carrito" to="/cart" subtitle="Ver tu carrito de compras">
            <template v-slot:append>
                <v-badge class="absolute right-6 top-9" color="error" :content="count" floating> </v-badge>
            </template>
        </v-list-item>

        <v-list-item color="orange-darken-4" :active="active === ''" rounded="lg" class="my-0 px-1 py-2 w-full" link prepend-icon="mdi-card-bulleted" title="Menú" to="/" subtitle="Consulta nuestro menú"></v-list-item>

        <v-list-item color="orange-darken-4" :active="active === 'client-orders'" rounded="lg" class="my-0 px-1 py-2 w-full" link prepend-icon="mdi-cash-register" title="Mis pedidos" to="/client-orders" subtitle="Ver tus pedidos realizados">
            
        </v-list-item>

        <v-list-item color="orange-darken-4" :active="active === 'client-reservations'" rounded="lg" class="my-0 px-1 py-2 w-full" link prepend-icon="mdi-calendar" title="Mis Reservaciones" to="/client-reservations" subtitle="Ver tus reservaciones realizadas"></v-list-item>
    </v-list>
</template>
