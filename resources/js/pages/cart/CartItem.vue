<script setup>
import { ref, getCurrentInstance, defineEmits } from "vue"

const { appContext } = getCurrentInstance()
const emitter = appContext.config.globalProperties.emitter

const props = defineProps({
    item: Object,
    cart: Object,
    index: Number,
})

const emit = defineEmits(['remove'])

const loading = ref(false)
const quantity = ref(props.item.quantity)

function add() {

}

function rest() {

}
</script>
<template>
    <v-card theme="dark" :disabled="loading" :loading="loading" class="mx-auto h-full relative w-full" max-width="374">
        <template v-slot:loader="{ isActive }">
            <v-progress-linear :active="isActive" color="blue" height="4" indeterminate></v-progress-linear>
        </template>

        <v-img height="250" :src="item.item.image || '/src/image_placeholder.jpg'" cover></v-img>

        <div class="absolute top-2 right-2">
            <v-btn size="x-small" color="red" @click="emit('remove')" icon="mdi-trash-can"></v-btn>
        </div>

        <v-card-item>
            <v-card-title>
                <div class="text-subtitle-1 float-end">
                    <span>S/.{{ Number(item.item.local_price).toFixed(2) }}</span>
                </div>
                {{ item.item.name }}
            </v-card-title>

            <v-card-subtitle>
                <span class="me-1">{{ item.item.category.name }}</span>

                <v-icon color="error" icon="mdi-fire-circle" size="small"></v-icon>
            </v-card-subtitle>
        </v-card-item>



        <v-divider class="mx-4 mb-1"></v-divider>

        <v-card-actions class="mt-2">
            <v-row dense align="center" justify="center">
                <v-col cols="2">
                    <v-btn :disabled="item.quantity == 1" @click="cart.update(index, Number(item.quantity) - 1)"
                        aria-label="Quitar item del carrito" size="x-small" icon="mdi-minus" color="blue"
                        variant="tonal"></v-btn>
                </v-col>
                <v-col cols="2">
                    <p class="text-white w-full text-center pe-3">{{ quantity }}</p>
                </v-col>
                <v-col cols="2">
                    <v-btn @click="cart.update(index, Number(item.quantity) + 1)" aria-label="Agregar item al carrito"
                        size="x-small" icon="mdi-plus" color="blue" variant="tonal"></v-btn>
                </v-col>
            </v-row>
        </v-card-actions>
    </v-card>
</template>
