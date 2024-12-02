<script setup>
import { defineProps, defineEmits, ref } from 'vue'

const CAPACITIES = []
for (let i = 1; i <= 20; i++) {
    CAPACITIES.push({
        id: i,
        name: `${i} ${i == 1 ? 'persona' : 'personas'}`
    })
}

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
    /**
     * Tablas disponibles
     */
    tables: Array,
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
        <v-form v-model="form" @submit.prevent="send" id="table-form">
            <v-row dense>
                <v-col cols="12" md="6" sm="6">
                    <v-text-field :rules="[Utils.RULES.required]" :disabled="loading" v-model="item.name" name="name"
                        variant="solo-filled" label="Nombre" required></v-text-field>
                </v-col>

                <v-col cols="12" md="6" sm="6">
                    <v-select :rules="[Utils.RULES.required]" v-model="item.capacity" item-value="id" item-title="name"
                        :items="CAPACITIES" name="capacity" variant="solo-filled" label="Capacidad"></v-select>
                </v-col>
            </v-row>
        </v-form>
    </div>
</template>
