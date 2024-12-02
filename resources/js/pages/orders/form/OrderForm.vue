<script setup>
import EmptyData from '../../../components/common/table/EmptyData.vue'
import CartTable from './CartTable.vue'
import { defineProps, defineEmits, ref, defineModel } from 'vue'

/**
 * Definir el evento de envío del form
 */
const emit = defineEmits(['submit'])

const props = defineProps({
    /**
     * Datos del item a actualizar
     */
    item: Object,
    /**
     * True si se está realizando alguna petición al backend
     */
    loading: Boolean,
    /**
     * Productos a agregar al carrito de la orden
     */
    products: Array,
    /**
     * Filtrar producto por el nombre
     */
    search: String,
})

/**
 * Estado de verificación del formulario
 */
const form = ref(false)

/**
 * Columnas de la tabla de productos
 */
const headers = ref([
    { title: 'Código', key: 'id', width: 100 },
    { title: 'Nombre', key: 'name', minWidth: 350 },
    { title: 'Cantidad', key: 'quantity', width: 150 },
    { title: 'Precio', key: 'local_price', width: 150 },
])

/**
 * True si está abierto el diálogo
 */
const dialog = defineModel()

function send(e) {
    if (!form.value) return
    emit('submit', props.item)
}

function add(product) {
    let quan = quantity(product)
    console.log(quan)
    if (quan == 0) {
        props.item.items.push({
            item_id: product.id,
            item: product,
            item_variation_id: null,
            quantity: 1,
            price: product.local_price,
            notes: ''
        })
    } else {
        for (let data of props.item.items) {
            if (data.item_id == product.id) {
                data.quantity++
                break
            }
        }
    }
    update()
}

function rest(product) {
    let quan = quantity(product)
    console.log(quan)
    if (quan == 1) {
        for (let i in props.item.items) {
            if (props.item.items[i].item_id == product.id) {
                props.item.items.splice(i, 1)
                break
            }
        }
    } else {
        for (let data of props.item.items) {
            if (data.item_id == product.id) {
                data.quantity--
                break
            }
        }
    }
    update()
}

function update() {
    let subtotal = 0
    for (let item of props.item.items) {
        subtotal += item.quantity * item.price
    }
    props.item.subtotal = subtotal
    props.item.total = subtotal
    props.item.rest = 0
}

function quantity(product) {
    for (let item of props.item.items) {
        if (item.item_id == product.id) {
            return item.quantity
        }
    }
    return 0
}

</script>
<template>
    <div>
        <v-form theme="dark" v-model="form" class="mb-2" @submit.prevent="send" id="order-form">
            <v-row dense>
                <v-col cols="6" md="4" sm="4">
                    <v-radio-group v-model="item.payment_method" color="orange-darken-4" :hide-details="true" theme="dark"
                        inline>
                        <v-radio theme="dark" base-color="white" class="text-white" label="Efectivo" value="Cash"></v-radio>
                        <v-radio theme="dark" base-color="white" class="text-white" label="Transferencia"
                            value="Card"></v-radio>
                    </v-radio-group>
                </v-col>
                <v-col cols="6" md="8" sm="8">
                    <div class="flex justify-end items-center">
                        <v-btn :disabled="!Utils.isAuthorized('Registrar pedidos')" @click="dialog = true"
                            prepend-icon="mdi-plus" color="orange-darken-4" variant="tonal">
                            Registrar
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
        </v-form>

        <!-- Tabla con los datos  -->
        <v-data-table class=" rounded-md" :search="search" :headers="headers"
            :items="products" :loading="loading" hover items-per-page-text="Productos o servicios por página"
            prev-page-label="Página anterior" next-page-label="Página siguiente" last-page-label="Última página"
            first-page-label="Primera página" no-data-text="No se encontraron productos o servicios" :items-per-page="5"
            loading-text="Cargando productos o servicios"
            :items-per-page-options="[{ title: '5', value: 5 }, { title: '10', value: 10 }, { title: '15', value: 15 }, { title: '20', value: 20 }, { title: '50', value: 50 }, { title: '100', value: 100 }]">
            <template v-slot:item="{ item }">
                <tr>
                    <td>
                        {{ Utils.getCode(item.id, 'I', 4) }}
                    </td>
                    <td>
                        <v-list-item class="px-0" :title="item.name" :subtitle="`${item.category.name}`">
                            <template v-slot:prepend>
                                <div class="me-2">
                                    <v-avatar v-if="item.image == null" color="red">
                                        <span class="text-h5">{{ item.name.charAt(0).toUpperCase() }}</span>
                                    </v-avatar>
                                    <v-avatar v-else :image="item.image">
                                    </v-avatar>
                                </div>
                            </template>
                        </v-list-item>
                    </td>
                    <td>
                        <div class="flex gap-2 items-center">
                            <v-btn variant="tonal" :disabled="quantity(item) == 0" @click="rest(item)" size="x-small"
                                icon="mdi-minus"></v-btn>
                            {{ quantity(item) }}
                            <v-btn variant="tonal" @click="add(item)" size="x-small" icon="mdi-plus"></v-btn>
                        </div>
                    </td>
                    <td>
                        <v-list-item class="px-0" :title="`${item.local_price} CUP`"
                            :subtitle="`${item.foreign_price || (item.local_price / 330).toFixed(2)} USD`">

                        </v-list-item>
                    </td>
                </tr>
            </template>
            <template v-slot:no-data>
                <EmptyData text="No hemos encontrado ningún poducto o servicio" />
            </template>
        </v-data-table>

        <!-- Formulario de agregar/editar item -->
        <v-dialog width="auto" scrollable v-model="dialog">
            <template v-slot:default="{ isActive }">
                <v-card :loading="loading" :title="item.id == undefined ? 'Registrar pedido' : 'Actualizar pedido'"
                    subtitle="Confirma los datos del pedido">
                    <v-divider></v-divider>
                    <v-card-text class="px-4" style="height: 300px;">
                        <CartTable :item="item" @add="add" @rest="rest" />
                    </v-card-text>
                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-btn text="Cerrar" prepend-icon="mdi-close" color="red" @click="isActive.value = false"></v-btn>

                        <v-spacer></v-spacer>

                        <v-btn color="blue"
                            :prepend-icon="item.id == undefined ? 'mdi-content-save-move' : 'mdi-content-save-edit'"
                            type="submit" :text="item.id == undefined ? 'Guardar' : 'Actualizar'" form="order-form"></v-btn>
                    </v-card-actions>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>
