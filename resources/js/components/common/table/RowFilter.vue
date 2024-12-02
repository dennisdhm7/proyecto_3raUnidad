<script setup>
import { defineModel, defineProps } from 'vue'

const model = defineModel();
defineProps(['headers', 'columns', 'id']);

</script>
<template>
    <v-text-field v-model="model" density="compact" label="Buscar" prepend-inner-icon="mdi-magnify" variant="solo-filled"
        flat hide-details single-line>
        <template v-if="model == ''" v-slot:append-inner>
            <v-menu :close-on-content-click="false">
                <template v-slot:activator="{ props }">
                    <v-btn variant="text" size="small" color="orange-darken-4" icon="mdi-filter-variant" v-bind="props"></v-btn>
                </template>

                <v-list>
                    <v-list-item class="py-0" v-for="(column, i) in columns" :key="i">
                        <v-list-item-title>
                            <v-checkbox :model-value="headers.find((item) => item.key == column.key) != undefined"
                                color="orange-darken-4" density="compact" :hide-details="true"
                                @update:modelValue="(checked) => Utils.toggleColumn(checked, headers, column, id)"
                                :label="column.title"></v-checkbox>
                        </v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </template>
        <template v-else v-slot:append-inner>
            <v-btn @click="model = ''" variant="text" size="small" color="orange-darken-4" icon="mdi-close"></v-btn>
        </template>
    </v-text-field>
</template>
