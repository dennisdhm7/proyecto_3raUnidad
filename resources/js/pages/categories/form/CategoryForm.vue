<script setup>
import { defineProps, defineEmits, ref } from 'vue'

/**
 * Definir el evento de envío del form
 */
const emit = defineEmits(['submit'])

defineProps({
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

function send(e) {
    if (!form.value) return
    //Obtener datos del formulario
    const data = new FormData(e.target)
    emit('submit', data)
}
</script>
<template>
    <div>
        <v-form v-model="form" @submit.prevent="send" id="category-form">
            <v-row dense>
                <v-col cols="12">
                    <v-text-field :rules="[Utils.RULES.required, Utils.RULES.name]" :disabled="loading" v-model="item.name"
                        name="name" variant="solo-filled" label="Nombre" required></v-text-field>
                </v-col>

                <v-col cols="12">
                    <v-textarea :rules="[Utils.RULES.required]" :disabled="loading" v-model="item.description"
                        name="description" variant="solo-filled" label="Descripción" required></v-textarea>
                </v-col>
            </v-row>
        </v-form>
    </div>
</template>
