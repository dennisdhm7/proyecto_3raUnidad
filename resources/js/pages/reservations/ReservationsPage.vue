<script setup>
import { ref, onMounted } from 'vue'
import Utils from '../../Utils'
import axios from '../../request'

import LayoutComponent from '../../components/layout/LayoutComponent.vue';
import MessageSnackBar from '../../components/common/MessageSnackBar.vue'
import RowFilter from '../../components/common/table/RowFilter.vue'
import EmptyData from '../../components/common/table/EmptyData.vue'
import UpgradeSuscription from '../../components/common/UpgradeSuscription.vue'
import ReservationForm from './form/ReservationForm.vue';
import TablesList from './TablesList.vue';
import ConfirmDialog from '../../components/common/table/ConfirmDialog.vue';

const color = ref('#E65100')

/**
 * Columnas de la tabla por defecto
 */
const columns = [
    { index: 0, title: 'ID', key: 'reservation_id' },
    { index: 1, title: 'Cliente', key: 'client' },
    { index: 2, title: 'Fecha y hora', key: 'reservation_datetime', minWidth: 250 },
    { index: 3, title: 'Mesas', key: 'tables', sortable: false, minWidth: 200, },
    { index: 4, title: 'Estado', key: 'status', },
    { index: 5, title: 'Creación', key: 'created_at', minWidth: 150 },
    { index: 6, title: 'Acciones', key: 'actions', minWidth: 150, sortable: false },
]

/**
 * Definir las columnas visibles de la tabla
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
* Mesas disponibles
*/
const tables = ref([])

/**
* Datos de la fila que se está editando en la tabla
*/
const item = ref({})

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
 * Pestaña seleccionada
 */
const tab = ref(0)

/**
* Abrir el formulario con los datos de un item para editar
*/
function edit(data) {
    item.value = JSON.parse(JSON.stringify(data))
    dialog.value = true
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
    axios.post(`api/reservations/${destroy.value.id}`, data).then(() => {
        //Eliminar el item del frontend
        for (let i in items.value) {
            if (items.value[i].id == destroy.value.id) {
                items.value.splice(i, 1)
                //Mostrar mensaje
                message.value = {
                    open: true,
                    text: 'Se ha eliminado correctamente la reservación'
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
    data.append('_method', item.value.id == undefined ? 'post' : 'put')
    //Realizar la petición
    axios.post(item.value.id == undefined ? 'api/reservations' : `api/reservations/${item.value.id}`, data).then((response) => {
        if (item.value.id == undefined) {
            //Agregar item al frontend
            items.value.unshift(response.data)
            //Mostrar mensaje
            message.value = {
                open: true,
                text: 'Se ha creado correctamente la reservación'
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
                        text: 'Se han actualizado correctamente los datos de la reservación'
                    }
                    break
                }
            }
        }
        item.value = {}
        //Ocultar identificación visual de petición al backend
        loading.value = false
        //Ocultar formulario
        dialog.value = false
    }).catch((error) => {
        //Ocultar identificación visual de petición al backend
        loading.value = false
    })
}

/**
 * Cambiar el estado de un item
 */
function updateStatus(item) {
    //Mostrar identificación visual de petición al backend
    loading.value = true
    //Obtener datos del formulario
    const data = new FormData()
    data.append('status', 'Confirmed');
    //Establecer el método de la petición (Ver documentación de Laravel para más información)
    data.append('_method', 'put')
    //Realizar la petición
    axios.post(`api/reservations/status/${item.id}`, data).then((response) => {
        //Actualizar item en el frontend
        for (let i in items.value) {
            if (items.value[i].id == item.id) {
                items.value[i] = response.data
                //Mostrar mensaje
                message.value = {
                    open: true,
                    text: 'Se ha confirmado correctamente la reservación'
                }
                break
            }
        }

        //Ocultar identificación visual de petición al backend
        loading.value = false
    }).catch((error) => {
        //Ocultar identificación visual de petición al backend
        loading.value = false
    })
}

onMounted(() => {
    headers.value = Utils.loadColumns(columns, 'reservations')
    //Mostrar identificación visual de petición al backend
    loading.value = true
    //Obtener items del backend
    axios.get('api/reservations').then((response) => {
        items.value = response.data
        //Ocultar identificación visual de petición al backend
        loading.value = false
    }).catch(() => {
        //Ocultar identificación visual de petición al backend
        loading.value = false
    })

    axios.get('api/tables').then((response) => {
        tables.value = response.data
    })
})
</script>
<template>
    <section class="bg-cover">
        <!-- Layout principal -->
        <LayoutComponent title="Reservaciones">
            <div class="w-full min-h-[calc(100vh-100px)]">
                <div class="flex flex-col sm:flex-row gap-2 justify-between items-center mb-2">
                    <div>
                        <v-tabs theme="dark" v-model="tab" align-tabs="start" class="mb-4" height="30" :show-arrows="false"
                            color="orange-darken-4">
                            <v-tab class="text-white" size="small" :value="0">Reservaciones</v-tab>
                            <v-tab class="text-white" size="small" :value="1">
                                Mesas
                                <v-badge class="ms-5" color="error" :content="tables.length">
                                </v-badge>
                            </v-tab>
                        </v-tabs>
                    </div>
                    <div class="w-48 md:w-64">
                        <RowFilter v-model="search" id="reservations" :headers="headers" :columns="columns" />
                    </div>
                </div>

                <v-tabs-window v-model="tab">
                    <v-tabs-window-item :key="0" :value="0">
                        <!-- Formulario de agregar/editar item -->
                        <v-dialog width="auto" scrollable v-model="dialog">
                            <template v-slot:activator="{ props: activatorProps }">
                                <div class="flex mb-2">
                                    <v-btn :disabled="!Utils.isAuthorized('Agregar reservaciones')" v-bind="activatorProps"
                                        @click="item = {}" prepend-icon="mdi-plus" color="orange-darken-4" variant="tonal">
                                        Agregar
                                    </v-btn>
                                </div>
                            </template>
                            <template v-slot:default="{ isActive }">
                                <v-card :loading="loading"
                                    :title="item.id == undefined ? 'Agregar reservación' : 'Actualizar reservación'"
                                    :subtitle="`Completa el formulario para ${item.id == undefined ? 'agregar una nueva reservación' : 'actualizar los datos de la reservación'}`">
                                    <v-divider></v-divider>
                                    <v-card-text class="px-4" style="height: 300px;">
                                        <ReservationForm @submit="createOrUpdate" :item="item" :tables="tables"
                                            :loading="loading" />
                                    </v-card-text>
                                    <v-divider></v-divider>

                                    <v-card-actions>
                                        <v-btn text="Cerrar" prepend-icon="mdi-close" color="red"
                                            @click="isActive.value = false"></v-btn>

                                        <v-spacer></v-spacer>

                                        <v-btn color="blue"
                                            :prepend-icon="item.id == undefined ? 'mdi-content-save-move' : 'mdi-content-save-edit'"
                                            type="submit" :text="item.id == undefined ? 'Guardar' : 'Actualizar'"
                                            form="reservation-form"></v-btn>
                                    </v-card-actions>
                                </v-card>
                            </template>
                        </v-dialog>

                        <!-- Tabla con los datos  -->
                        <v-data-table class=" rounded-md" :headers="headers" :items="items" :loading="loading" hover
                            items-per-page-text="Pagos por página" prev-page-label="Página anterior"
                            next-page-label="Página siguiente" last-page-label="Última página"
                            first-page-label="Primera página" no-data-text="No se encontraron pagos"
                            loading-text="Cargando pagos" item-value="id"
                            :items-per-page-options="[{ title: '5', value: 5 }, { title: '10', value: 10 }, { title: '15', value: 15 }, { title: '20', value: 20 }, { title: '50', value: 50 }, { title: '100', value: 100 }]">
                            <template v-slot:item="{ item }">
                                <tr>
                                    <td v-if="Utils.isColumnVisibility(headers, columns[0].key)">
                                        {{ item.reservation_id }}
                                    </td>
                                    <td v-if="Utils.isColumnVisibility(headers, columns[1].key)">
                                        {{ item.client }}
                                    </td>
                                    <td v-if="Utils.isColumnVisibility(headers, columns[2].key)">
                                        <v-list-item class="px-0"
                                            :title="`${Utils.datetime(item.reservation_datetime, Utils.FULL_DATE_TIME)}`"
                                            :subtitle="`${item.reservation_time} minutos`" prepend-icon="mdi-calendar">
                                        </v-list-item>
                                    </td>
                                    <td class="text-center" v-if="Utils.isColumnVisibility(headers, columns[3].key)">
                                        <v-chip class="m-0.5" size="small" v-for="table in item.tables">
                                            {{ table.name }}
                                        </v-chip>
                                    </td>
                                    <td class="text-center" v-if="Utils.isColumnVisibility(headers, columns[4].key)">
                                        <v-chip v-if="item.status == 'Created'" size="small">Creada</v-chip>
                                        <v-chip v-else-if="item.status == 'Confirmed'" color="green" size="small">Confirmada</v-chip>
                                        <v-chip v-else-if="item.status == 'Cancelled'" color="red" size="small">Cancelada</v-chip>
                                    </td>
                                    <td v-if="Utils.isColumnVisibility(headers, columns[5].key)">
                                        <template v-if="item.created_at == null">
                                            No disponible
                                        </template>
                                        <template v-else>
                                            {{ Utils.datetime(item.created_at, Utils.FULL_DATE_TIME) }}
                                        </template>
                                    </td>
                                    <td v-if="Utils.isColumnVisibility(headers, columns[6].key)">
                                        <v-btn
                                            :disabled="!Utils.isAuthorized('Editar reservaciones') || item.status != 'Created'"
                                            v-tooltip="'Confirmar reservación'" icon="mdi-check" @click="updateStatus(item)"
                                            size="x-small" class="me-1" rounded="lg" color="grey-lighten-1" variant="tonal">
                                        </v-btn>
                                        <v-btn :disabled="!Utils.isAuthorized('Editar reservaciones')"
                                            v-tooltip="'Editar los datos de la reservación'" icon="mdi-pencil"
                                            @click="edit(item)" size="x-small" class="me-1" rounded="lg"
                                            color="grey-lighten-1" variant="tonal">
                                        </v-btn>
                                        <v-btn :disabled="!Utils.isAuthorized('Eliminar reservaciones')"
                                            v-tooltip="'Eliminar reservación'" icon="mdi-trash-can"
                                            @click="destroy = { open: true, id: item.id }" size="x-small" rounded="lg"
                                            color="grey-lighten-1" variant="tonal">
                                        </v-btn>
                                    </td>
                                </tr>
                            </template>
                            <template v-slot:no-data>
                                <EmptyData text="No hemos encontrado ninguna reservación" />
                            </template>
                        </v-data-table>
                    </v-tabs-window-item>
                    <v-tabs-window-item :key="1" :value="1">
                        <TablesList v-model="loading" :items="tables" />
                    </v-tabs-window-item>
                </v-tabs-window>
            </div>

            <!-- Snackbar con mensajes a mostrar -->
            <MessageSnackBar :message="message" />
        </LayoutComponent>
        <ConfirmDialog @confirm="remove" v-model="destroy.open" action="Eliminando reservación"
            title="Confirmar eliminación" :loading="loading"
            question="¿Está seguro que desea eliminar esta reservación?. Esta operación es irreversible."
            icon="mdi-trash-can" />
    </section>
</template>
<style scoped>
section {
    background: url(../../assets/login-bg.svg);
}
</style>
