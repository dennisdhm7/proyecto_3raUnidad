<script setup>
import { defineProps, ref, defineEmits } from 'vue'
import EmptyData from '../../../components/common/table/EmptyData.vue'

const emit = defineEmits(['add', 'rest'])

const props = defineProps({
    item: Object
})

/**
 * Columnas de la tabla de productos
 */
const headers = ref([
    { title: 'Nombre', key: 'name', minWidth: 350 },
    { title: 'Cantidad', key: 'quantity', width: 150 },
    { title: 'Subtotal', key: 'subtotal', width: 100 },
    { title: 'Notas', key: 'notes', minWidth: 200 },
])

function update(value) {
    props.item.rest = Number(value) - props.item.subtotal
}

</script>
<template>
    <div>
        <v-row dense class="mb-2">
            <v-col cols="12" md="4" sm="4">
                <v-list-item class="px-0" :prepend-icon="item.payment_method == 'Cash' ? 'mdi-cash' : 'mdi-credit-card'"
                    title="Monto total" :subtitle="`${item.subtotal} CUP`"></v-list-item>
            </v-col>
            <v-col cols="12" md="4" sm="4">
                <v-text-field class="max-w-[10rem]" :prepend-icon="item.payment_method == 'Cash' ? 'mdi-cash' : 'mdi-credit-card'" @update:model-value="update" :hide-details="true" density="comfortable" v-model="item.total"
                    variant="solo-filled" label="Pagado"></v-text-field>
            </v-col>
            <v-col cols="12" md="4" sm="4">
                <v-list-item class="px-0" prepend-icon="mdi-cash" title="Vuelto" :subtitle="`${item.rest} CUP`"></v-list-item>
            </v-col>
        </v-row>
        <v-data-table class=" rounded-md" :headers="headers" :items="item.items" hover
            items-per-page-text="Productos o servicios por página" prev-page-label="Página anterior"
            next-page-label="Página siguiente" last-page-label="Última página" first-page-label="Primera página"
            no-data-text="No se encontraron productos o servicios" :items-per-page="10"
            loading-text="Cargando productos o servicios"
            :items-per-page-options="[{ title: '5', value: 5 }, { title: '10', value: 10 }, { title: '15', value: 15 }, { title: '20', value: 20 }, { title: '50', value: 50 }, { title: '100', value: 100 }]">
            <template v-slot:item="{ item, index }">
                <tr>
                    <td>
                        <v-list-item class="px-0" :title="item.item.name" :subtitle="`${item.item.category.name}`">
                            <template v-slot:prepend>
                                <div class="me-2">
                                    <v-avatar v-if="item.item.image == null" color="red">
                                        <span class="text-h5">{{ item.item.name.charAt(0).toUpperCase() }}</span>
                                    </v-avatar>
                                    <v-avatar v-else :image="item.item.image">
                                    </v-avatar>
                                </div>
                            </template>
                        </v-list-item>
                    </td>
                    <td>
                        <div class="flex gap-2 items-center">
                            <v-btn variant="tonal" :disabled="item.quantity == 0" @click="emit('rest', item.item)"
                                size="x-small" icon="mdi-minus"></v-btn>
                            {{ item.quantity }}
                            <v-btn variant="tonal" @click="emit('add', item.item)" size="x-small" icon="mdi-plus"></v-btn>
                        </div>
                    </td>
                    <td>
                        {{ item.quantity * item.price }}
                    </td>
                    <td>
                        <v-text-field :hide-details="true" density="compact" v-model="item.notes"
                            variant="solo-filled"></v-text-field>
                    </td>
                </tr>
            </template>
            <template v-slot:no-data>
                <EmptyData text="No hemos encontrado ningún poducto o servicio" />
            </template>
        </v-data-table>
    </div>
</template>
