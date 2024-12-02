<script setup>
import axios from '../../request';
import Utils from '../../Utils'

import MessageSnackBar from '../../components/common/MessageSnackBar.vue';
import RowFilter from '../../components/common/table/RowFilter.vue';
import EmptyData from '../../components/common/table/EmptyData.vue';
import TableForm from './form/TableForm.vue';

import { ref, defineProps, defineModel, onMounted } from 'vue'
import ConfirmDialog from '../../components/common/table/ConfirmDialog.vue';

/**
 * Columnas de la tabla por defecto
 */
const columns = [
    { index: 0, title: 'Código', key: 'id', width: 100 },
    { index: 1, title: 'Nombre', key: 'name' },
    { index: 2, title: 'Capacidad', key: 'description' },
    { index: 3, title: 'Creación', key: 'created_at', minWidth: 140 },
    { index: 4, title: 'Acciones', key: 'actions', minWidth: 150, sortable: false, width: 100 },
]

const props = defineProps({
    /**
     * Listado de mesas
     */
    items: Array,
})

/**
 * True si se está realizando alguna operación en el backend y se está esperando respuesta
 */
const loading = defineModel()

/**
 * * Definir las columnas visibles de la tabla
*/
const headers = ref([])

/**
 * Aplicar un filtro a los datos de la tabla
 */
const search = ref('')

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
    axios.post(`api/tables/${destroy.value.id}`, data).then(() => {
        //Eliminar el item del frontend
        for (let i in props.items) {
            if (props.items[i].id == destroy.value.id) {
                props.items.splice(i, 1)
                //Mostrar mensaje
                message.value = {
                    open: true,
                    text: 'Se ha eliminado correctamente la mesa'
                }
                // emitter.value.emit('tables_deleted')
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
    axios.post(item.value.id == undefined ? 'api/tables' : `api/tables/${item.value.id}`, data).then((response) => {
        if (item.value.id == undefined) {
            //Agregar item al frontend
            props.items.unshift(response.data)
            //Mostrar mensaje
            message.value = {
                open: true,
                text: 'Se ha creado correctamente la mesa'
            }
            // emitter.value.emit('tables_added')
        } else {
            //Actualizar item en el frontend
            for (let i in props.items) {
                if (props.items[i].id == item.value.id) {
                    props.items[i] = response.data
                    //Mostrar mensaje
                    message.value = {
                        open: true,
                        text: 'Se han actualizado correctamente los datos de la mesa'
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

onMounted(() => {
    headers.value = Utils.loadColumns(columns, 'tables')
})
</script>

<template>
    <div>
        <div class="flex">
            <!-- Formulario de agregar/editar item -->
            <v-dialog width="auto" scrollable v-model="dialog">
                <template v-slot:activator="{ props: activatorProps }">
                    <v-btn class="mb-2" :disabled="!Utils.isAuthorized('Agregar mesas')" v-bind="activatorProps"
                        @click="item = {}" prepend-icon="mdi-plus" color="orange-darken-4" variant="tonal">
                        Agregar
                    </v-btn>
                </template>
                <template v-slot:default="{ isActive }">
                    <v-card :loading="loading" :title="item.id == undefined ? 'Agregar mesa' : 'Actualizar mesa'"
                        :subtitle="`Completa el formulario para ${item.id == undefined ? 'agregar una nueva mesa' : 'actualizar los datos de la mesa'}`">
                        <v-divider></v-divider>
                        <v-card-text class="px-4" style="height: 130px;">
                            <TableForm @submit="createOrUpdate" :item="item" :loading="loading" />
                        </v-card-text>
                        <v-divider></v-divider>

                        <v-card-actions>
                            <v-btn text="Cerrar" prepend-icon="mdi-close" color="red"
                                @click="isActive.value = false"></v-btn>

                            <v-spacer></v-spacer>

                            <v-btn color="blue"
                                :prepend-icon="item.id == undefined ? 'mdi-content-save-move' : 'mdi-content-save-edit'"
                                type="submit" :text="item.id == undefined ? 'Guardar' : 'Actualizar'"
                                form="table-form"></v-btn>
                        </v-card-actions>
                    </v-card>
                </template>
            </v-dialog>
        </div>

        <!-- Tabla con los datos  -->
        <v-data-table class=" rounded-md" v-model:search="search" :headers="headers"
            :items="items" :loading="loading" hover items-per-page-text="mesas por página" prev-page-label="Página anterior"
            next-page-label="Página siguiente" last-page-label="Última página" first-page-label="Primera página"
            no-data-text="No se encontraron mesas" loading-text="Cargando mesas"
            :items-per-page-options="[{ title: '5', value: 5 }, { title: '10', value: 10 }, { title: '15', value: 15 }, { title: '20', value: 20 }, { title: '50', value: 50 }, { title: '100', value: 100 }]">
            <template v-slot:item="{ item }">
                <tr>
                    <td v-if="Utils.isColumnVisibility(headers, columns[0].key)">
                        {{ Utils.getCode(item.id, 'M', 2) }}
                    </td>
                    <td v-if="Utils.isColumnVisibility(headers, columns[1].key)">{{ item.name }}</td>
                    <td v-if="Utils.isColumnVisibility(headers, columns[2].key)">{{ item.capacity }} {{ item.capacity == 1 ?
                        'persona' : 'personas' }}
                    </td>
                    <td v-if="Utils.isColumnVisibility(headers, columns[3].key)">
                        <template v-if="item.created_at == null">
                            No disponible
                        </template>
                        <template v-else>
                            {{ Utils.datetime(item.created_at, Utils.FULL_DATE_TIME) }}
                        </template>
                    </td>
                    <td v-if="Utils.isColumnVisibility(headers, columns[4].key)">
                        <v-btn :disabled="!Utils.isAuthorized('Editar mesas')" v-tooltip="'Editar los datos de la mesa'"
                            icon="mdi-pencil" @click="edit(item)" size="x-small" class="me-1" rounded="lg"
                            color="grey-lighten-1" variant="tonal">
                        </v-btn>
                        <v-btn :disabled="!Utils.isAuthorized('Eliminar mesas')" v-tooltip="'Eliminar mesa'"
                            icon="mdi-trash-can" @click="destroy = { open: true, id: item.id }" size="x-small" rounded="lg"
                            color="grey-lighten-1" variant="tonal">
                        </v-btn>
                    </td>
                </tr>
            </template>
            <template v-slot:no-data>
                <EmptyData text="No hemos encontrado ninguna mesa" />
            </template>
        </v-data-table>

        <!-- Snackbar con mensajes a mostrar -->
        <MessageSnackBar :message="message" />

        <ConfirmDialog @confirm="remove" v-model="destroy.open" action="Eliminando mesa" title="Confirmar eliminación"
            :loading="loading" question="¿Está seguro que desea eliminar esta mesa?. Esta operación es irreversible."
            icon="mdi-trash-can" />
    </div>
</template>
