<script setup>
import MenuLayout from "../../components/layout/MenuLayout.vue"
import { ref, onMounted, getCurrentInstance } from "vue"
import axios from "../../request"
import Cart from '../../Cart.js'
import Utils from '../../Utils'

import MessageSnackBar from '../../components/common/MessageSnackBar.vue'
import RowFilter from '../../components/common/table/RowFilter.vue'
import EmptyData from '../../components/common/table/EmptyData.vue'
import ItemsTable from '../orders/details/ItemsTable.vue'

const { appContext } = getCurrentInstance()
const emitter = appContext.config.globalProperties.emitter

const cart = new Cart(emitter)

/**
 * Columnas de la tabla por defecto
 */
const columns = [
    { index: 0, title: 'ID', key: 'order_id' },
    { index: 1, title: 'Usuario', key: 'user', minWidth: 300 },
    { index: 2, title: 'Detalles de pago', key: 'total', minWidth: 200 },
    { index: 3, title: 'Estado', key: 'status' },
    { index: 4, title: 'Creación', key: 'created_at', minWidth: 150 },
    { index: 5, title: 'Acciones', key: 'actions', minWidth: 150, sortable: false },
]

/**
 * * Definir las columnas visibles de la tabla
*/
const headers = ref([])

/**
 * Aplicar un filtro a los datos de la tabla
 */
const search = ref('')

/**
* Datos de la tabla
*/
const items = ref([])

/**
 * Datos de la fila que se está eliminando en la tabla
 */
const destroy = ref({
    //Define si el diálogo de confirmación de eliminar elemento está abierto
    open: false,
    //Id del item a eliminar
    id: 0,
})

/**
 * True si se está realizando alguna operación en el backend y se está esperando respuesta
 */
const loading = ref(false)

/**
* Utilizado para mostrar mensajes exitosos
*/
const message = ref({
    //True si se muestra el snackbar
    open: false,
    //Mensaje a mostrar
    text: '',
})

/**
* Items por página
*/
const itemsPerPage = ref(10)

/**
 * Total de items en la base de datos
 */
const totalItems = ref(0)

/**
 * Filas que se muestran los detalles
 */
const expanded = ref([])

/**
 * Eliminar un item
 */
function remove() {
    //Mostrar identificación visual de petición al backend
    loading.value = true
    //Configurar la petición
    const data = new FormData()
    //Agregar método de la petición (Ver documentación de Laravel para más información)
    data.append('_method', 'delete')
    //Realizar la petición
    axios.post(`api/orders/${destroy.value.id}`, data).then(() => {
        //Eliminar el item del frontend
        for (let i in items.value) {
            if (items.value[i].id == destroy.value.id) {
                items.value[i].status = 'Cancelled'
                //Mostrar mensaje
                message.value = {
                    open: true,
                    text: 'Se ha cancelado correctamente el pedido'
                }
                // emitter.value.emit('categories_deleted')
                break
            }
        }
        //Ocultar identificación visual de petición al backend
        loading.value = false
        //Ocultar el modal de confirmación de eliminación
        destroy.value = {
            open: false,
            id: 0
        }

    }).catch((error) => {
        //Ocultar identificación visual de petición al backend
        loading.value = false
    })
}

/**
 * Actualizar los datos de la página de los pedidos
*/
function load(data) {
    const page = data.page, itemsPerPage = data.itemsPerPage, search = data.search, sort = data.sortBy
    let sortKey = '', sortOrder = ''
    if (sort.length > 0) {
        sortKey = sort[0].key
        sortOrder = sort[0].order
    }
    //Mostrar identificación visual de petición al backend
    loading.value = true
    //Hacer la petición al backend
    axios.get(`api/client-orders?page=${page}&per_page=${itemsPerPage}&search=${search}&sort_key=${sortKey}&sort_order=${sortOrder}`).then((response) => {
        //Establecer los items de la página
        items.value = response.data.data
        //Establecer el total de items
        totalItems.value = response.data.total
        //Ocultar identificación visual de petición al backend
        loading.value = false
    }).catch(() => {
        //Ocultar identificación visual de petición al backend
        loading.value = false
    })
}

onMounted(() => {
    headers.value = Utils.loadColumns(columns, 'orders')
})
</script>
<template>
    <main class="bg-gradient-to-tr from-gray-900 to-black">
        <MenuLayout :cart="cart" title="Menú">
            <div class="w-full min-h-[calc(100vh-100px)]">
                <div class="flex flex-col sm:flex-row gap-2 justify-between items-center mb-2">
                    <div></div>
                    <div class="w-48 md:w-64">
                        <RowFilter v-model="search" id="client-orders" :headers="headers" :columns="columns" />
                    </div>
                </div>

                <!-- Tabla con los datos  -->
                <v-data-table-server class=" rounded-md" v-model:search="search" :headers="headers"
                    :items="items" @update:options="load"
                    v-model:items-per-page="itemsPerPage" :items-length="totalItems" :loading="loading" hover
                    items-per-page-text="Pedidos por página" prev-page-label="Página anterior"
                    next-page-label="Página siguiente" last-page-label="Última página" first-page-label="Primera página"
                    no-data-text="No se encontraron pedidos" loading-text="Cargando pedidos" v-model:expanded="expanded"
                    item-value="id"
                    :items-per-page-options="[{ title: '5', value: 5 }, { title: '10', value: 10 }, { title: '15', value: 15 }, { title: '20', value: 20 }, { title: '50', value: 50 }, { title: '100', value: 100 }]">
                    <template v-slot:expanded-row="{ columns, item }">
                        <tr>
                            <td :colspan="columns.length">
                                <div class="py-2">
                                    <ItemsTable :items="item.items" />
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template v-slot:item="{ item, toggleExpand, isExpanded, internalItem }">
                        <tr>
                            <td v-if="Utils.isColumnVisibility(headers, columns[0].key)">
                                {{ item.order_id }}
                            </td>
                            <td v-if="Utils.isColumnVisibility(headers, columns[1].key)">
                                <v-list-item v-if="item.user != null" class="px-0"
                                    :prepend-avatar="item.user.image || '/src/user_placeholder.jpg'" :title="item.user.name"
                                    :subtitle="item.user.email">
                                </v-list-item>
                                <template v-else>
                                    <p v-if="item.client != null">{{ item.client }} ({{ item.phone }})</p>
                                            <p v-else>Pedido en el local</p>
                                    <p class="text-sm">{{ item.address }}</p>
                                </template>
                            </td>
                            <td v-if="Utils.isColumnVisibility(headers, columns[2].key)">
                                <v-list-item class="px-0" :title="`Total: ${item.total}`" :subtitle="`Vuelto: ${item.rest}`"
                                    :prepend-icon="item.payment_method == 'Cash' ? 'mdi-cash' : 'mdi-credit-card'">
                                </v-list-item>
                            </td>
                            <td class="text-center" v-if="Utils.isColumnVisibility(headers, columns[3].key)">
                                <v-chip v-if="item.status == 'Cancelled'" color="red" variant="tonal">Cancelado</v-chip>
                                <v-chip v-else-if="item.status == 'Created'" color="gray"
                                    variant="tonal">Registrado</v-chip>
                                <v-chip v-else color="green" variant="tonal">
                                    {{ item.status == 'Paid' ? 'Pagado' : 'Entregado' }}
                                </v-chip>
                            </td>
                            <td v-if="Utils.isColumnVisibility(headers, columns[4].key)">
                                <template v-if="item.created_at == null">
                                    No disponible
                                </template>
                                <template v-else>
                                    {{ Utils.datetime(item.created_at, Utils.FULL_DATE_TIME) }}
                                </template>
                            </td>
                            <td v-if="Utils.isColumnVisibility(headers, columns[5].key)">
                                <v-btn
                                    :disabled="item.status == 'Cancelled' || item.status == 'Delivered'"
                                    v-tooltip="'Cancelar pedido'" icon="mdi-trash-can"
                                    @click="destroy = { open: true, id: item.id }" size="x-small" rounded="lg" class="me-1"
                                    color="grey-lighten-1" variant="tonal">
                                </v-btn>
                                <v-btn v-tooltip="'Mostrar items del pedido'" @click="toggleExpand(internalItem)"
                                    :icon="isExpanded(internalItem) ? 'mdi-chevron-up' : 'mdi-chevron-down'" size="x-small"
                                    rounded="lg" color="grey-lighten-1" variant="tonal">
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                    <template v-slot:no-data>
                        <EmptyData text="No hemos encontrado ningún pedido" />
                    </template>
                </v-data-table-server>
            </div>

            <!-- Snackbar con mensajes a mostrar -->
            <MessageSnackBar :message="message" />
        </MenuLayout>
    </main>
</template>
