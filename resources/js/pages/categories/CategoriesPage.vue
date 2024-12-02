<script setup>
import axios from '../../request';
import Utils from '../../Utils'

import MessageSnackBar from '../../components/common/MessageSnackBar.vue';
import RowFilter from '../../components/common/table/RowFilter.vue';
import EmptyData from '../../components/common/table/EmptyData.vue';
import LayoutComponent from '../../components/layout/LayoutComponent.vue';
import CategoryForm from './form/CategoryForm.vue';

import { ref, onMounted } from 'vue'
import ConfirmDialog from '../../components/common/table/ConfirmDialog.vue';

/**
 * Columnas de la tabla por defecto
 */
const columns = [
    { index: 0, title: 'Código', key: 'id' },
    { index: 1, title: 'Nombre', key: 'name' },
    { index: 2, title: 'Descripción', key: 'description', minWidth: 300 },
    { index: 3, title: 'Estado', key: 'active', value: item => item.active ? 'Activa' : 'Inactiva' },
    { index: 4, title: 'Creación', key: 'created_at', minWidth: 140 },
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
    axios.post(`api/categories/${destroy.value.id}`, data).then(() => {
        //Eliminar el item del frontend
        for (let i in items.value) {
            if (items.value[i].id == destroy.value.id) {
                items.value.splice(i, 1)
                //Mostrar mensaje
                message.value = {
                    open: true,
                    text: 'Se ha eliminado correctamente la categoría'
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
    axios.post(item.value.id == undefined ? 'api/categories' : `api/categories/${item.value.id}`, data).then((response) => {
        if (item.value.id == undefined) {
            //Agregar item al frontend
            items.value.unshift(response.data)
            //Mostrar mensaje
            message.value = {
                open: true,
                text: 'Se ha creado correctamente la categoría'
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
                        text: 'Se han actualizado correctamente los datos de la categoría'
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
    data.append('active', item.active == 1 ? 0 : 1);
    //Establecer el método de la petición (Ver documentación de Laravel para más información)
    data.append('_method', 'put')
    //Realizar la petición
    axios.post(`api/categories/status/${item.id}`, data).then((response) => {
        //Actualizar item en el frontend
        for (let i in items.value) {
            if (items.value[i].id == item.id) {
                items.value[i] = response.data
                //Mostrar mensaje
                message.value = {
                    open: true,
                    text: 'Se ha actualizado correctamente el estado de la categoría'
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
    headers.value = Utils.loadColumns(columns, 'categories')
    //Mostrar identificación visual de petición al backend
    loading.value = true
    //Obtener items del backend
    axios.get('api/categories').then((response) => {
        items.value = response.data
        //Ocultar identificación visual de petición al backend
        loading.value = false
    }).catch(() => {
        //Ocultar identificación visual de petición al backend
        loading.value = false
    })
})
</script>

<template>
    <section class="bg-cover">
        <!-- Layout principal -->
        <LayoutComponent title="Categorías">
            <div class="w-full min-h-[calc(100vh-100px)]">
                <div class="flex gap-2 justify-between items-center mb-2">

                    <!-- Formulario de agregar/editar item -->
                    <v-dialog width="auto" scrollable v-model="dialog">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn :disabled="!Utils.isAuthorized('Agregar categorías')" v-bind="activatorProps"
                                @click="item = {}" prepend-icon="mdi-plus" color="orange-darken-4" variant="tonal">
                                Agregar
                            </v-btn>
                        </template>
                        <template v-slot:default="{ isActive }">
                            <v-card :loading="loading"
                                :title="item.id == undefined ? 'Agregar categoría' : 'Actualizar categoría'"
                                :subtitle="`Completa el formulario para ${item.id == undefined ? 'agregar una nueva categoría' : 'actualizar los datos de la categoría'}`">
                                <v-divider></v-divider>
                                <v-card-text class="px-4" style="height: 300px;">
                                    <CategoryForm @submit="createOrUpdate" :item="item" :loading="loading" />
                                </v-card-text>
                                <v-divider></v-divider>

                                <v-card-actions>
                                    <v-btn text="Cerrar" prepend-icon="mdi-close" color="red"
                                        @click="isActive.value = false"></v-btn>

                                    <v-spacer></v-spacer>

                                    <v-btn color="blue"
                                        :prepend-icon="item.id == undefined ? 'mdi-content-save-move' : 'mdi-content-save-edit'"
                                        type="submit" :text="item.id == undefined ? 'Guardar' : 'Actualizar'"
                                        form="category-form"></v-btn>
                                </v-card-actions>
                            </v-card>
                        </template>
                    </v-dialog>
                    <div class="w-48 md:w-64">
                        <RowFilter v-model="search" id="categories" :headers="headers" :columns="columns" />
                    </div>
                </div>

                <!-- Tabla con los datos  -->
                <v-data-table class=" rounded-md" v-model:search="search" :headers="headers"
                    :items="items" :loading="loading" hover items-per-page-text="Categorías por página"
                    prev-page-label="Página anterior" next-page-label="Página siguiente" last-page-label="Última página"
                    first-page-label="Primera página" no-data-text="No se encontraron categorías"
                    loading-text="Cargando categorías"
                    :items-per-page-options="[{ title: '5', value: 5 }, { title: '10', value: 10 }, { title: '15', value: 15 }, { title: '20', value: 20 }, { title: '50', value: 50 }, { title: '100', value: 100 }]">
                    <template v-slot:item="{ item }">
                        <tr>
                            <td v-if="Utils.isColumnVisibility(headers, columns[0].key)">
                                {{ Utils.getCode(item.id, 'C', 3) }}
                            </td>
                            <td v-if="Utils.isColumnVisibility(headers, columns[1].key)">{{ item.name }}</td>
                            <td v-if="Utils.isColumnVisibility(headers, columns[2].key)">{{ item.description }}
                            </td>
                            <td v-if="Utils.isColumnVisibility(headers, columns[3].key)">
                                <v-chip :color="item.active == 1 ? 'green' : 'red'">
                                    <template v-if="item.active == 1">
                                        Activa
                                    </template>
                                    <template v-else>
                                        Inactiva
                                    </template>
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
                                <v-btn :disabled="!Utils.isAuthorized('Editar categorías')"
                                    v-tooltip="'Cambiar estado de la categoría'" icon="mdi-toggle-switch"
                                    @click="updateStatus(item)" size="x-small" class="me-1" rounded="lg"
                                    color="grey-lighten-1" variant="tonal">
                                </v-btn>
                                <v-btn :disabled="!Utils.isAuthorized('Editar categorías')"
                                    v-tooltip="'Editar los datos de la categoría'" icon="mdi-pencil" @click="edit(item)"
                                    size="x-small" class="me-1" rounded="lg" color="grey-lighten-1" variant="tonal">
                                </v-btn>
                                <v-btn :disabled="!Utils.isAuthorized('Eliminar categorías')"
                                    v-tooltip="'Eliminar categoría'" icon="mdi-trash-can"
                                    @click="destroy = { open: true, id: item.id }" size="x-small" rounded="lg"
                                    color="grey-lighten-1" variant="tonal">
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                    <template v-slot:no-data>
                        <EmptyData text="No hemos encontrado ninguna categoría" />
                    </template>
                </v-data-table>
            </div>

            <!-- Snackbar con mensajes a mostrar -->
            <MessageSnackBar :message="message" />

            <!-- Diálogo de confirmación de eliminación de item -->
        </LayoutComponent>
        <ConfirmDialog @confirm="remove" v-model="destroy.open" action="Eliminando categoría" title="Confirmar eliminación"
            :loading="loading" question="¿Está seguro que desea eliminar esta categoría?. Esta operación es irreversible."
            icon="mdi-trash-can" />
    </section>
</template>
<style scoped>
section {
    background: url(../../assets/login-bg.svg);
}
</style>
