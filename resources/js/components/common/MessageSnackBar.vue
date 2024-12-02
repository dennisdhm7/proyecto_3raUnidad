<script>
export default {
    props: {
        message: Object,
    },

}
</script>
<template>
    <v-snackbar v-model="message.open" timeout="3000" color="green-darken-4">
        {{ message.text }}

        <template v-slot:actions>
            <v-btn color="grey-lighten-4" variant="text" @click="message.open = false">
                Cerrar
            </v-btn>
        </template>
    </v-snackbar>
</template>
