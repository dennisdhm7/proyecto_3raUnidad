<script setup>
import { defineProps } from 'vue'

const props = defineProps({
    text: String,
})

</script>
<template>
    <v-bottom-sheet inset>
        <template v-slot:activator="{ props }">
            <v-icon v-bind="props" icon="mdi-information-outline" class="cursor-pointer"></v-icon>
        </template>
        <template v-slot:default="{ isActive }">
            <v-card class="rounded-t-lg" title="Información" prepend-icon="mdi-information-outline"
                :text="text">
                <v-card-actions>
                    <v-btn @click="isActive.value = false" variant="tonal" color="red">
                        Cerrar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </template>
    </v-bottom-sheet>
</template>
