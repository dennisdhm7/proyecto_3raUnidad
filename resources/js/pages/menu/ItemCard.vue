<script setup>
    import { ref, getCurrentInstance } from "vue"

    const { appContext } = getCurrentInstance()
    const emitter = appContext.config.globalProperties.emitter

    const props = defineProps({
        item: Object,
        cart: Object,
    })

    const loading = ref(false)
    const quantity = ref(1)

    function addToCart() {
        emitter.emit("add-to-cart", { item: props.item, element: null, quantity: quantity.value })
    }
</script>
<template>
    <v-card theme="dark" :disabled="loading" :loading="loading" class="mx-auto h-full relative" max-width="374">
        <template v-slot:loader="{ isActive }">
            <v-progress-linear :active="isActive" color="blue" height="4" indeterminate></v-progress-linear>
        </template>

        <v-img height="250" :src="item.image || '/src/image_placeholder.jpg'" cover></v-img>

        <v-card-item>
            <v-card-title>
                <div class="text-subtitle-1 float-end">
                    <span>${{ Number(item.local_price).toFixed(2) }}</span>
                </div>
                {{ item.name }}
            </v-card-title>

            <v-card-subtitle>
                <span class="me-1">{{ item.category.name }}</span>

                <v-icon color="error" icon="mdi-fire-circle" size="small"></v-icon>
            </v-card-subtitle>
        </v-card-item>

        <v-card-text>
            <v-row align="center" class="mx-0">
                <v-rating :model-value="5" color="amber" density="compact" size="small" half-increments readonly></v-rating>

                <div class="text-grey ms-4">
                    <!-- {{ item.review.rating }} ({{ item.review.reviews }}) -->
                </div>
            </v-row>

            <div class="min-h-16 mt-4">
                <template v-if="item.description.length > 80"> {{ item.description.substring(0, 80) }}... </template>
                <template v-else>
                    {{ item.description }}
                </template>
            </div>
        </v-card-text>

        <v-divider class="mx-4 mb-1"></v-divider>

        <v-card-actions class="mt-2">
            <v-row dense align="center" justify="center">
                <v-col cols="2">
                    <v-btn @click="quantity--" aria-label="Quitar item del carrito" size="x-small" icon="mdi-minus" color="blue" variant="tonal" :disabled="quantity <= 1"></v-btn>
                </v-col>
                <v-col cols="2">
                    <p class="text-white w-full text-center pe-3">{{ quantity }}</p>
                </v-col>
                <v-col cols="2">
                    <v-btn @click="quantity++" aria-label="Agregar item al carrito" size="x-small" icon="mdi-plus" color="blue" variant="tonal"></v-btn>
                </v-col>
                <v-col cols="12">
                    <v-btn color="blue" text="Añadir al carrito" block border @click="addToCart"></v-btn>
                </v-col>
            </v-row>
        </v-card-actions>
    </v-card>
</template>
