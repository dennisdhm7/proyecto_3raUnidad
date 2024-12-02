<script setup>
import { defineProps } from 'vue'

defineProps({ text: String })
</script>
<template>
    <v-empty-state icon="mdi-archive-search">
        <template v-slot:media>
            <v-icon color="surface-variant"></v-icon>
        </template>

        <template v-slot:headline>
            <div class="text-h4">
                ¡Ups!
            </div>
        </template>

        <template v-slot:text>
            <div class="text-medium-emphasis text-caption">
                {{ text }}
            </div>
        </template>
    </v-empty-state>
</template>
