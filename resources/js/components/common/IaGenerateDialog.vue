<script setup>
import { defineProps, defineEmits, defineModel, ref, watchEffect } from 'vue'
import Utils from '../../Utils.js'
import axios from '../../request'

const open = defineModel()

const props = defineProps({
    prompt: String,
    text: String
})



const loading = ref(false)

const result = ref('')

const instructions = ref(props.prompt)

watchEffect(() => {
    instructions.value = props.prompt
}, props.prompt)

const emit = defineEmits(['cancel', 'confirm'])

function generate() {
    loading.value = true
    axios.post('/api/generate', { message: instructions.value }).then((response) => {
        console.log(response)
        const data = JSON.parse(response.data.response)
        result.value = data.choices[0].message.content
        loading.value = false
    }).catch(() => {
        loading.value = false
    })
}

</script>
<template>
    <v-dialog theme="dark" v-model="open" max-width="320" persistent>
        <v-list v-show="loading" class="py-2" color="orange-darken-4" elevation="12" rounded="lg">
            <v-list-item prepend-icon="mdi-creation" title="Generando...">
                <template v-slot:prepend>
                    <div class="pe-4">
                        <v-icon color="orange-darken-4" size="x-large"></v-icon>
                    </div>
                </template>

                <template v-slot:append>
                    <v-progress-circular color="orange-darken-4" indeterminate="disable-shrink" size="16"
                        width="2"></v-progress-circular>
                </template>
            </v-list-item>
        </v-list>
        <v-card v-show="!loading" prepend-icon="mdi-creation" :text="text" title="Generar con Inteligencia Artificial">
            <div class="p-4">
                <v-textarea :rules="[Utils.RULES.required]" :disabled="loading" v-model="instructions" variant="solo-filled"
                    label="Prompt" required>
                </v-textarea>
                <div class="flex justify-center mb-4">
                    <v-btn :loading="loading" @click="generate" variant="tonal" prepend-icon="mdi-creation"
                        color="orange-darken-4">Generar</v-btn>
                </div>
                <v-textarea :rules="[Utils.RULES.required]" :disabled="loading" v-model="result" variant="solo-filled"
                    label="Respuesta" required>
                </v-textarea>
            </div>
            <template v-slot:actions>
                <v-spacer></v-spacer>

                <v-btn color="blue" @click="open = false">
                    Cancelar
                </v-btn>

                <v-btn color="red" @click="emit('confirm', result)">
                    Confirmar
                </v-btn>
            </template>
        </v-card>
    </v-dialog>
</template>
