<script setup>
import { defineProps } from 'vue'

defineProps({ text: String })
</script>
<template>
    <v-empty-state icon="mdi-star">
        <template v-slot:media>
            <v-icon color="surface-variant"></v-icon>
        </template>

        <template v-slot:headline>
            <div class="text-h4">
                ¡Necesitas actualizar tu plan!
            </div>
        </template>

        <template v-slot:text>
            <div class="text-medium-emphasis text-caption">
                Esta función solo está disponible para el plan Business o Enterprise
            </div>
            <div class="mt-2">
                <v-btn to="/billing" color="orange-darken-4" variant="tonal">Actualizar plan</v-btn>
            </div>
        </template>
    </v-empty-state>
</template>
