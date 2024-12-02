<script setup>
import { defineProps, defineEmits, ref, onMounted } from 'vue'
import axios from '../../../request'
import IaGenerateDialog from '../../../components/common/IaGenerateDialog.vue'
import ImageEditor from '../../../components/common/editor/ImageEditor.vue'

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
})

/**
 * Estado de verificación del formulario
 */
const form = ref(false)

/**
 * Categorías activas disposibles
 */
const categories = ref([])

/**
 * Image input
 */
const image = ref(null)

/**
 * Image preview
 */
const preview = ref(null)

/**
 * Variaciones del producto
 */
const variations = ref([])

const autocomplete = ref([])

/**
 * Datos del campo de imagen que hay en el editor
 */
const editor = ref({
    /**
     * True si está abierto el editor de imágenes
     */
    open: false,
    /**
     * Datos de la imagen seleccionada con el navegador
     */
    image: null,
    /**
     * Instancia al recortador de imagen
     */
    cropper: null,
})

function update(value) {
    if (value != '') {
        autocomplete.value = [value]
    } else {
        autocomplete.value = []
    }
}

function send(e) {
    if (!form.value) return
    //Obtener datos del formulario
    const data = new FormData(e.target)
    if (!props.item.image.startsWith('http')) {
        data.append('image', props.item.image)
    }
    emit('submit', data)
}

function openFileWindow() {
    image.value.click()
}

function updatePreviewImage(e) {
    const file = e.target.files[0];
    const reader = new FileReader();

    reader.addEventListener(
        "load",
        () => {
            preview.value.src = reader.result;
            //Abrir editor de imagen
            editor.value = {
                open: true,
                image: reader.result,
                preview: preview.value
            }
        },
        false
    );

    if (file) {
        reader.readAsDataURL(file);
    }
}

const dialog = ref(false)

function confirm(text) {
    props.item.description = text
    dialog.value = false
}

/**
 * Guardar edición de imagen del editor
 */
function saveEdition() {
    //Establecer imagen editada en la vista previa
    props.item.image = editor.value.cropper.getCroppedCanvas().toDataURL()
    //Cerrar editor
    editor.value.open = false
}

onMounted(() => {
    axios.get('/api/categories/active').then((response) => {
        categories.value = response.data
    })

    if (props.item.variations != undefined) {
        //Instanciar las variaciones
        for (const variation of props.item.variations) {
            let exist = false
            for (let item of variations.value) {
                if (item.type == variation.type) {
                    item.values.push(variation.name)
                    exist = true
                    break
                }
            }
            if (!exist) {
                variations.value.push({ type: variation.type, values: [variation.name] })
            }

        }
    }
})
</script>
<template>
    <div>
        <v-form v-model="form" @submit.prevent="send" class="max-w-[50rem]" id="item-form">
            <v-row dense>
                <v-col cols="12" md="6" sm="6">
                    <div class="flex justify-center min-w-72">
                        <div class="relative pb-4 pe-4">
                            <img class="user-profile-image h-60 min-w-64 rounded-3xl" ref="preview" alt="user-profile-image"
                                :src="item.image || 'src/image_placeholder.jpg'" />
                            <v-btn @click="openFileWindow" class="absolute bottom-[-0.25rem] right-[-0.25rem]" color="blue"
                                icon="mdi-camera"></v-btn>
                        </div>
                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit position-absolute end-0 bottom-0">
                            <input @change="updatePreviewImage" ref="image" id="profile-img-file-input" type="file"
                                class="profile-img-file-input d-none" accept="image/png, image/jpeg, image/webp">
                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs cursor-pointer">
                                <span class="avatar-title rounded-circle bg-light text-body">
                                    <i class="ri-camera-fill"></i>
                                </span>
                            </label>
                        </div>
                    </div>
                    <v-list-item title="Comida vegana" subtitle="Marca este check si su plantillo es vegano">
                        <template v-slot:prepend>
                            <v-checkbox v-model="item.vegan" :true-value="1" :false-value="0" name="vegan"
                                :hide-details="true" color="orange-darken-4" class="mt-1"></v-checkbox>
                        </template>
                    </v-list-item>
                    <v-list-item title="Entrega a domicilio" subtitle="Marca este check si se puede pedir a domicilio">
                        <template v-slot:prepend>
                            <v-checkbox v-model="item.delivery" :true-value="1" :false-value="0" name="delivery"
                                :hide-details="true" color="orange-darken-4" class="mt-1"></v-checkbox>
                        </template>
                    </v-list-item>
                </v-col>
                <v-col cols="12" md="6" sm="6">
                    <v-text-field :rules="[Utils.RULES.required]" :disabled="loading" v-model="item.name" name="name"
                        variant="solo-filled" label="Nombre" required></v-text-field>

                    <v-select :rules="[Utils.RULES.required]" v-model="item.category_id" item-value="id" item-title="name"
                        :items="categories" name="category_id" variant="solo-filled" label="Categoría"></v-select>

                    <v-text-field :rules="[Utils.RULES.required, Utils.RULES.currency]" :disabled="loading"
                        v-model="item.local_price" name="local_price" variant="solo-filled" label="Precio"
                        required></v-text-field>

                    <div class="relative">
                        <v-textarea :disabled="loading" v-model="item.description" name="description" variant="solo-filled"
                            label="Descripción" required></v-textarea>
                    </div>
                </v-col>

                <!-- <v-col cols="12" md="6" sm="6">

                </v-col>

                <v-col cols="12" md="6" sm="6">
                    <v-text-field :rules="[Utils.RULES.required, Utils.RULES.currency]" :disabled="loading"
                        v-model="item.foreign_price" name="foreign_price" variant="solo-filled" label="Precio USD"
                        required></v-text-field>
                </v-col> -->

                <!-- <v-col cols="12">
                    <v-list-item class="px-0 mb-2" title="Variantes"
                        subtitle="Añadir variaciones de su producto o servicio (sabores, colores, tamaños, etc)">
                        <template v-slot:append>
                            <v-btn class="mt-1" variant="tonal" color="orange-darken-4"
                                @click="variations.push({ type: '', values: [] })">Añadir</v-btn>
                        </template>
                    </v-list-item>
                    <div class="px-2">
                        <template v-for="(variation, i) of variations">
                            <v-row dense>
                                <v-col cols="12" md="4" sm="4">
                                    <v-text-field :hide-details="true" :rules="[Utils.RULES.required, Utils.RULES.name]"
                                        :disabled="loading" v-model="variation.type" :name="`variations[${i}][type]`"
                                        variant="solo-filled" label="Tipo de variación" required>
                                        <template v-slot:prepend-inner>
                                            <v-btn @click="variations.splice(i, 1)" variant="text" icon="mdi-trash-can"
                                                color="red"></v-btn>
                                        </template>
                                    </v-text-field>
                                </v-col>
                                <v-col cols="12" md="8" sm="8">
                                    <v-autocomplete v-model="variation.values" :hide-details="true"
                                        @update:search="(value) => update(value)" chips
                                        :label="`${variation.type} (Variantes)`" :items="autocomplete" multiple
                                        variant="solo-filled" :clear-on-select="true" :closable-chips="true"
                                        :hide-no-data="true" :hide-selected="true" item-color="blue">
                                    </v-autocomplete>
                                    <input v-for="(value, j) in variation.values" :name="`variations[${i}][name][${j}]`"
                                        :value="value" type="hidden" />
                                </v-col>
                            </v-row>
                            <v-divider class="my-2"></v-divider>
                        </template>
                    </div>
                </v-col> -->
            </v-row>
        </v-form>
        <IaGenerateDialog v-if="item.name != undefined && item.name != ''" @confirm="confirm" v-model="dialog"
            :prompt="`Genera una descripción llamativa para mi menú llamado ${item.name} que no exceda los 250 caracteres. Solo envíame la descripción en el texto. En Español.`"
            text="Generar descripción con nuestro modelo de Inteligencia Artificial" />

        <!-- Formulario de editar imagen -->
        <v-dialog width="auto" scrollable v-model="editor.open">
            <template v-slot:default="{ isActive }">
                <v-card title="Editor de imágenes" subtitle="Recorta la imagen y ajústala según tus necesidades">
                    <v-divider></v-divider>
                    <v-card-text class="px-4">
                        <ImageEditor v-model="editor.cropper" :image="editor.image" :aspectRatio="4 / 3" />
                    </v-card-text>
                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-btn text="Cerrar" prepend-icon="mdi-close" color="red" @click="isActive.value = false"></v-btn>

                        <v-spacer></v-spacer>

                        <v-btn @click="saveEdition" color="blue" prepend-icon="mdi-content-save-move"
                            text="Guardar"></v-btn>
                    </v-card-actions>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>
