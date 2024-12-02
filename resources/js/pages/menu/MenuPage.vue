<script setup>
    import MenuLayout from "../../components/layout/MenuLayout.vue"
    import ItemCard from './ItemCard.vue'
    import { ref, onMounted, getCurrentInstance } from "vue"
    import axios from "axios"
    import Cart from '../../Cart.js'

    const { appContext } = getCurrentInstance()
    const emitter = appContext.config.globalProperties.emitter

    const categories = ref({})

    const cart = new Cart(emitter)

    onMounted(() => {
        axios.get("/api/menu/items").then((response) => {
            categories.value = response.data
        })

        /**
         * Añadir producto al carrito de compras
         */
         emitter.on('add-to-cart', ({ item, quantity }) => {
            setTimeout(() => {
                cart.add(item, quantity, null)
            }, 550)
        })
    })
</script>
<template>
    <main class="bg-gradient-to-tr from-gray-900 to-black">
        <MenuLayout :cart="cart" title="Menú">
            <div class="flex overflow-auto gap-2">
                <v-chip v-for="(items, category) in categories" color="blue">{{ category }}</v-chip>
            </div>

            <div class="mt-8">
                <div v-for="(items, category) in categories">
                    <p class="text-gray-200 mb-2">{{ category }}</p>
                    <div class="grid gap-4 grid-cols-3">
                        <ItemCard v-for="item in items" :item="item" :cart="cart" />
                    </div>
                    <v-divider class="my-3"></v-divider>
                </div>
            </div>
        </MenuLayout>

    </main>
</template>
