<script setup>
import axios from '../../request';
import Utils from '../../Utils'

import MessageSnackBar from '../../components/common/MessageSnackBar.vue'
import RowFilter from '../../components/common/table/RowFilter.vue'
import EmptyData from '../../components/common/table/EmptyData.vue'
import LayoutComponent from '../../components/layout/LayoutComponent.vue'
import ItemsTable from './details/ItemsTable.vue'
import OrderForm from './form/OrderForm.vue'

import { ref, onMounted } from 'vue'
import ConfirmDialog from '../../components/common/table/ConfirmDialog.vue'

/**
 * Columnas de la tabla por defecto
 */
const columns = [
    { index: 0, title: 'ID', key: 'order_id' },
    { index: 1, title: 'Usuario', key: 'user', minWidth: 300 },
    { index: 2, title: 'Detalles de pago', key: 'total', minWidth: 200 },
    { index: 3, title: 'Estado', key: 'status' },
    { index: 4, title: 'Creación', key: 'created_at', minWidth: 150 },
    { index: 5, title: 'Acciones', key: 'actions', minWidth: 180, sortable: false },
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
* Datos de la fila que se está editando en la tabla
*/
const item = ref({
    items: []
})

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
 * Datos de la fila que se está eliminando en la tabla
 */
const statusItem = ref({
    //Define si el diálogo de confirmación de eliminar elemento está abierto
    open: false,
    //Id del item a eliminar
    item: {},
})

/**
 * True si se está realizando alguna operación en el backend y se está esperando respuesta
 */
const loading = ref(false)

/**
* True si se muestra el formulario de adición/edición de un item
*/
const dialog = ref(false)

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
 * Productos o servicios disponibles para el carrito de compras
 */
const products = ref([])

/**
 * Pestaña seleccionada
 */
const tab = ref(0)

/**
 * Filtrar pedidos por tipo
 */
const type = ref('All')

/**
* Abrir el formulario con los datos de un item para editar
*/
function edit(data) {
    item.value = JSON.parse(JSON.stringify(data))
    let subtotal = 0
    for (let data of item.value.items) {
        subtotal += data.quantity * data.price
    }
    item.value.subtotal = subtotal
    tab.value = 1
}

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
 * Agregar o actualizar un item en el backend
 */
function createOrUpdate(data) {
    //Mostrar identificación visual de petición al backend
    loading.value = true
    //Establecer el método de la petición PUT para actualizar o POST para agregar
    data._method = item.value.id == undefined ? 'post' : 'put'
    data.type = 'Sale'
    //Realizar la petición
    axios.post(item.value.id == undefined ? 'api/orders' : `api/orders/${item.value.id}`, data).then((response) => {
        if (item.value.id == undefined) {
            //Agregar item al frontend
            items.value.unshift(response.data)
            //Mostrar mensaje
            message.value = {
                open: true,
                text: 'Se ha registrado correctamente el pedido'
            }
            // emitter.value.emit('categories_added')
        } else {
            //Actualizar item en el frontend
            for (let i in items.value) {
                if (items.value[i].id == item.value.id) {
                    items.value[i] = response.data
                    //Mostrar mensaje
                    message.value = {
                        open: true,
                        text: 'Se han actualizado correctamente los datos del pedido'
                    }
                    break
                }
            }
        }
        item.value = {
            items: [],
            payment_method: 'Cash'
        }
        //Ocultar identificación visual de petición al backend
        loading.value = false
        //Ocultar formulario
        dialog.value = false
        tab.value = 0
    }).catch((error) => {
        //Ocultar identificación visual de petición al backend
        loading.value = false
    })
}

/**
 * Cambiar el estado de un item
 */
function updateStatus() {
    //Mostrar identificación visual de petición al backend
    loading.value = true
    //Obtener datos del formulario
    const data = new FormData()
    data.append('status', statusItem.value.item.status);
    //Establecer el método de la petición (Ver documentación de Laravel para más información)
    data.append('_method', 'put')
    //Realizar la petición
    axios.post(`api/orders/status/${statusItem.value.item.id}`, data).then((response) => {
        //Actualizar item en el frontend
        for (let i in items.value) {
            if (items.value[i].id == statusItem.value.item.id) {
                items.value[i] = response.data
                //Mostrar mensaje
                message.value = {
                    open: true,
                    text: 'Se ha actualizado correctamente el estado del pedido'
                }
                break
            }
        }

        //Ocultar identificación visual de petición al backend
        loading.value = false
        statusItem.value = {
            open: false,
            item: {}
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
    axios.get(`api/orders?page=${page}&per_page=${itemsPerPage}&search=${search}&sort_key=${sortKey}&sort_order=${sortOrder}`).then((response) => {
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
    //Mostrar identificación visual de petición al backend
    loading.value = true

    axios.get('/api/items/active').then((response) => {
        for (let product of response.data) {
            product.quantity = 0
            products.value.push(product)
        }
    })
})
</script>

<template>
    <section class="bg-cover">
        <!-- Layout principal -->
        <LayoutComponent title="Pedidos">
            <div class="w-full min-h-[calc(100vh-100px)]">
                <div class="flex flex-col sm:flex-row gap-2 justify-between items-center mb-2">
                    <div>
                        <v-tabs theme="dark" v-model="tab" align-tabs="start" class="mb-4" height="30" :show-arrows="false"
                            color="orange-darken-4">
                            <v-tab class="text-white" size="small" :value="0">Pedidos</v-tab>
                            <v-tab class="text-white" size="small" :value="1">
                                Carrito
                                <v-badge class="ms-5" color="error" :content="item.items.length">
                                </v-badge>
                            </v-tab>
                        </v-tabs>
                    </div>
                    <div>
                        <v-select v-model="type" variant="solo-filled" density="compact" item-value="id" item-title="name"
                            label="Filtrar" class="min-w-[150px]" :hide-details="true"
                            :items="[{ id: 'All', name: 'Todos los pedidos' }, { id: 'Delivery', name: 'Delivery' }, { id: 'Local', name: 'En local' }, { id: 'Sale', name: 'Venta' }]"></v-select>
                    </div>
                    <div class="w-48 md:w-64">
                        <RowFilter v-model="search" id="orders" :headers="headers" :columns="columns" />
                    </div>
                </div>

                <v-tabs-window v-model="tab">
                    <v-tabs-window-item :key="0" :value="0">
                        <!-- Tabla con los datos  -->
                        <v-data-table-server class=" rounded-md" v-model:search="search" :headers="headers"
                            :items="items.filter(data => type == 'All' || data.type == type)" @update:options="load"
                            v-model:items-per-page="itemsPerPage" :items-length="totalItems" :loading="loading" hover
                            items-per-page-text="Pedidos por página" prev-page-label="Página anterior"
                            next-page-label="Página siguiente" last-page-label="Última página"
                            first-page-label="Primera página" no-data-text="No se encontraron pedidos"
                            loading-text="Cargando pedidos" v-model:expanded="expanded" item-value="id"
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
                                            :prepend-avatar="item.user.image || '/src/user_placeholder.jpg'"
                                            :title="item.user.name" :subtitle="item.user.email">
                                        </v-list-item>
                                        <template v-else>
                                            <p v-if="item.client != null">{{ item.client }} ({{ item.phone }})</p>
                                            <p v-else>Pedido en el local</p>
                                            <p class="text-sm">{{ item.address }}</p>
                                        </template>
                                    </td>
                                    <td v-if="Utils.isColumnVisibility(headers, columns[2].key)">
                                        <v-list-item class="px-0" :title="`Total: ${item.total}`"
                                            :subtitle="`Vuelto: ${item.rest}`"
                                            :prepend-icon="item.payment_method == 'Cash' ? 'mdi-cash' : 'mdi-credit-card'">
                                        </v-list-item>
                                    </td>
                                    <td class="text-center" v-if="Utils.isColumnVisibility(headers, columns[3].key)">
                                        <v-chip v-if="item.status == 'Cancelled'" color="red"
                                            variant="tonal">Cancelado</v-chip>
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
                                            :disabled="!Utils.isAuthorized('Eitar pedidos') || item.status == 'Cancelled' || item.status == 'Delivered'"
                                            v-tooltip="'Modificar estado del pedido'" icon="mdi-toggle-switch"
                                            @click="statusItem = { open: true, item: item }" size="x-small" rounded="lg"
                                            class="me-1" color="grey-lighten-1" variant="tonal">
                                        </v-btn>
                                        <v-btn
                                            :disabled="!Utils.isAuthorized('Editar pedidos') || item.status == 'Cancelled' || item.status == 'Delivered'"
                                            v-tooltip="'Editar los datos del pedido'" icon="mdi-pencil" @click="edit(item)"
                                            size="x-small" class="me-1" rounded="lg" color="grey-lighten-1" variant="tonal">
                                        </v-btn>
                                        <v-btn
                                            :disabled="!Utils.isAuthorized('Cancelar pedidos') || item.status == 'Cancelled' || item.status == 'Delivered'"
                                            v-tooltip="'Cancelar pedido'" icon="mdi-trash-can"
                                            @click="destroy = { open: true, id: item.id }" size="x-small" rounded="lg"
                                            class="me-1" color="grey-lighten-1" variant="tonal">
                                        </v-btn>
                                        <v-btn v-tooltip="'Mostrar items del pedido'" @click="toggleExpand(internalItem)"
                                            :icon="isExpanded(internalItem) ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                                            size="x-small" rounded="lg" color="grey-lighten-1" variant="tonal">
                                        </v-btn>
                                    </td>
                                </tr>
                            </template>
                            <template v-slot:no-data>
                                <EmptyData text="No hemos encontrado ningún pedido" />
                            </template>
                        </v-data-table-server>
                    </v-tabs-window-item>
                    <v-tabs-window-item :key="1" :value="1">
                        <OrderForm @submit="createOrUpdate" :item="item" :search="search" :products="products"
                            :loading="loading" v-model="dialog" />
                    </v-tabs-window-item>
                </v-tabs-window>
            </div>

            <!-- Snackbar con mensajes a mostrar -->
            <MessageSnackBar :message="message" />

            <!-- Diálogo de confirmación de eliminación de item -->
            <!-- Formulario de agregar/editar item -->
            <v-dialog width="auto" scrollable v-model="statusItem.open">
                <template v-slot:default="{ isActive }">
                    <v-card :loading="loading" title="Actualizar estado" :subtitle="`Elige el nuevo estado del pedido`">
                        <v-divider></v-divider>
                        <v-card-text class="px-4" style="height: 120px;">
                            <v-form id="status-form" @submit.prevent="updateStatus">
                                <v-select
                                    :items="[{ name: 'Created', value: 'Creado' }, { value: 'Accepted', name: 'Aceptado' }, { value: 'Prepared', name: 'Preparado' }, { value: 'Paid', 'name': 'Pagado' }, { value: 'Delievered', name: 'Entregado' }, { value: 'Cancelled', name: 'Cancelado' }]"
                                    :rules="[Utils.RULES.required, Utils.RULES.name]" :disabled="loading" item-value="value"
                                    item-title="name" name="status" v-model="statusItem.item.status" variant="solo-filled"
                                    label="Estado" required></v-select>
                            </v-form>
                        </v-card-text>
                        <v-divider></v-divider>

                        <v-card-actions>
                            <v-btn text="Cerrar" prepend-icon="mdi-close" color="red"
                                @click="isActive.value = false"></v-btn>

                            <v-spacer></v-spacer>

                            <v-btn color="blue" prepend-icon="mdi-content-save-edit" type="submit" text="Actualizar"
                                form="status-form"></v-btn>
                        </v-card-actions>
                    </v-card>
                </template>
            </v-dialog>
        </LayoutComponent>
        <ConfirmDialog @confirm="remove" v-model="destroy.open" action="Cancelando pedido" title="Confirmar cancelación"
            :loading="loading" question="¿Está seguro que desea cancelar este pedido?. Esta operación es irreversible."
            icon="mdi-trash-can" />
    </section>
</template>
<style scoped>
section {
    background: url(../../assets/login-bg.svg);
}
</style>
