<script setup>
import { defineProps, ref } from 'vue'

const props = defineProps({
    items: Array,
})

/**
* Definir las columnas de la tabla
*/
const headers = ref([
    { title: '#', key: 'index', width: 20 },
    { title: 'Producto', key: 'item', minWidth: 150 },
    { title: 'Precio', key: 'price', width: 60 },
    { title: 'Cant.', key: 'quantity', width: 60 },
    { title: 'Subtotal', key: 'subtotal', width: 60 },
])
</script>
<template>
    <div>
        <v-data-table-virtual class="child-table bg-transparent" density="compact" :disable-sort="true" :headers="headers"
            :items="items" no-data-text="No se encontraron productos">
            <template v-slot:item="{ index, item }">
                <tr class="last:border-b-0">
                    <td>{{ index + 1 }}</td>
                    <td>
                        {{ item.item.name }}
                        <!-- <br /> -->
                        <template v-if="item.variation != null">
                            - {{ item.variation.type }} ({{ item.variation.name }})
                        </template>
                    </td>
                    <td>
                        {{ item.price }}
                    </td>
                    <td>
                        {{ item.quantity }}
                    </td>
                    <td>
                        {{ Number(item.price) * Number(item.quantity) }}
                    </td>
                </tr>
            </template>
        </v-data-table-virtual>
    </div>
</template>
