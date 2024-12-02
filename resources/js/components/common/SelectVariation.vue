<script setup>
import { defineProps, ref, defineModel, onMounted } from 'vue'

const props = defineProps({
    item: Object,
    variations: Array,
})

const loading = ref(false)

const otherQuantity = ref(false)

const model = defineModel()

const updateQuantity = function () {
    setTimeout(() => {
        if (model.value == 'Otro') {
            otherQuantity.value = true
            model.value = 101
        }
    }, 200)
}

const cost = ref(0)
const price = ref(0)
const offers = ref([])
const stock = ref(0)

//Realizar petición para obtener el stock disponible del producto
const updateStock = function () {
    setTimeout(() => {
        //Contiene los IDs de las variaciones del producto
        let ids = props.variations.map(function (variation) {
            try {
                return variation.variations[variation.selected].id
            } catch (error) {
                return null
            }
        })
        //Relizar petición para obtener el stock
        axios.post('/api/stock/item', {
            //Se pasa el ID del producto
            item_id: props.item.id,
            //Se pasan los IDs de las variaciones elegidas
            variations: ids
        }).then((response) => {
            stock.value = response.data.stock
        })
        //Actualizar precios y costos
        if (props.item.prices.length > 0) {
            for (let priceItem of props.item.prices) {
                const compare = []
                for (let variation of priceItem.variations) {
                    compare.push(variation.item_variation_id)
                }
                if (ids.sort().toString() == compare.sort().toString()) {
                    price.value = priceItem.price
                    cost.value = priceItem.cost
                    offers.value = priceItem.offers
                    break
                }
            }
        } else {
            for (let i in result.ids) {
                price.value = 0
                cost.value = 0
                offers.value = []
            }
        }

    }, 200)
}

//Actualizar stock al inicio
onMounted(() => {
    updateStock()
})

</script>
<template>
    <v-card :disabled="loading" :loading="loading" class="mx-auto h-full" elevation="0" max-width="374">
        <template v-slot:loader="{ isActive }">
            <v-progress-linear :active="isActive" color="blue" height="4" indeterminate></v-progress-linear>
        </template>

        <!-- <v-img height="250" :src="item.image || 'src/image_placeholder.jpg'" cover></v-img> -->

        <v-card-item>
            <v-card-title>{{ item.name }}</v-card-title>

            <v-card-subtitle>
                <span class="me-1">{{ item.category.name }}</span>

                <v-icon color="error" icon="mdi-fire-circle" size="small"></v-icon>
            </v-card-subtitle>
        </v-card-item>

        <v-card-text>
            <v-row>
                <v-col cols="6"><v-list-item class="px-0" prepend-icon="mdi-cash-multiple" :subtitle="Utils.currency(cost)"
                        title="Costo"></v-list-item>
                </v-col>
                <v-col cols="6"><v-list-item class="px-0" prepend-icon="mdi-cash-multiple" :subtitle="Utils.currency(price)"
                        title="Precio"></v-list-item>
                </v-col>
                <v-col cols="6"><v-list-item class="px-0" prepend-icon="mdi-store" :subtitle="stock"
                        title="Stock"></v-list-item>
                </v-col>
                <v-col cols="6"><v-list-item class="px-0" prepend-icon="mdi-cash-multiple" :subtitle="Utils.currency(price)"
                    title="Precio unitario"></v-list-item>
            </v-col>
            </v-row>
        </v-card-text>

        <div class="px-2">
            <div v-for="(variation, index) in variations" :key="index" :value="index">
                <div>
                    <span class="text-sm">{{ variation.type }}</span>
                    <v-chip-group @click="updateStock" v-model="variation.selected" selected-class="bg-blue">
                        <v-chip size="small" v-for="item in variation.variations">{{ item.name }}</v-chip>
                    </v-chip-group>
                </div>
            </div>
        </div>

        <v-divider class="mx-4 my-1"></v-divider>

        <div class="flex justify-center items-center gap-4">
            <v-btn @click="model--" aria-label="Quitar item del carrito" size="x-small" icon="mdi-minus" color="blue"
                variant="tonal" :disabled="model <= 1"></v-btn>
            <div class="py-1 min-w-24">
                <v-select @update:model-value="updateQuantity" v-if="!otherQuantity || otherQuantity == undefined"
                    :items="[...Array.from({ length: 100 }, (x, i) => `${i + 1}`), 'Otro']" variant="solo-filled"
                    density="compact" :hide-details="true" required v-model="model">
                    <template v-slot:item="select">
                        <v-list-item v-bind="select.props" :title="select.item.raw"></v-list-item>
                    </template>
                </v-select>
                <v-text-field class="max-w-32" :clearable="true" @click:clear="otherQuantity = false" v-else
                    :disabled="loading" v-model="model" density="compact" :hide-details="true" variant="solo-filled"
                    type="number" required></v-text-field>
            </div>
            <v-btn @click="model++" aria-label="Agregar item al carrito" size="x-small" icon="mdi-plus" color="blue"
                variant="tonal"></v-btn>
        </div>
    </v-card>
</template>
