<script setup>
import { defineProps, defineEmits, defineModel } from 'vue'

// const open = defineModel('open')
const open = defineModel()

defineProps({ loading: Boolean, question: String, title: String, action: String, icon: String })

const emit = defineEmits(['cancel', 'confirm'])

</script>
<template>
    <v-dialog theme="dark" v-model="open" max-width="320" persistent>
        <v-list v-show="loading" class="py-2" color="orange-darken-4" elevation="12" rounded="lg">
            <v-list-item :prepend-icon="icon" :title="action">
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
        <v-card v-show="!loading" :prepend-icon="icon" :text="question" :title="title">
            <template v-slot:actions>
                <v-spacer></v-spacer>

                <v-btn color="blue" @click="open = false">
                    Cancelar
                </v-btn>

                <v-btn color="red" @click="emit('confirm')">
                    Confirmar
                </v-btn>
            </template>
        </v-card>
    </v-dialog>
</template>
