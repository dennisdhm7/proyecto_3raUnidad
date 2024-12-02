<script setup>
import { ref, defineModel, watch } from 'vue'
import NotificationsList from './NotificationsList.vue';
import ChatBot from './ChatBot.vue';

const collapse = defineModel('collapse')
const user = defineModel('user')

defineProps({
    title: String,
})

const edit = ref(false)
const profile = ref(false)
const chatbot = ref(false)
const role = ref('')
const loading = ref(false)

watch(role, (newRole) => {
    changeRole(newRole)
})


function editProfile(data) {
    //Mostrar identificación visual de petición al backend
    loading.value = true
    //Establecer el método de la petición PUT para actualizar o POST para agregar (Ver documentación de Laravel para más información)
    data.append('_method', 'put')
    //Realizar la petición
    axios.post(`api/users/${user.id}`, data).then((response) => {
        user.value = response.data
        //Actualizar datos locales del usuario
        localStorage.setItem('user', JSON.stringify(response.data))
        //Ocultar identificación visual de petición al backend
        loading.value = false
        //Ocultar formulario
        edit.value = false
    }).catch((error) => {
        //Ocultar identificación visual de petición al backend
        loading.value = false
    })
}

</script>
<template>
    <v-app-bar class="bg-[#2e2e2ec9] rounded-lg top-1 shadow-none"
        :class="{ 'left-[265px]': collapse, 'w-[calc(100%-271px)]': collapse, 'left-1': !collapse, 'w-[calc(100%-8px)]': !collapse }">
        <v-btn @click="collapse = !collapse" class="block xl:hidden" icon="mdi-menu" slim></v-btn>
        <v-list-item color="blue" class="my-1" :title="title" subtitle="Restaurante Capitán Chino"></v-list-item>
        <v-spacer></v-spacer>

        <!-- <NotificationsList /> -->
        <v-menu v-model="profile" :close-on-content-click="false">
            <template v-slot:activator="{ props }">
                <v-btn v-bind="props" slim icon="mdi-account-circle">
                </v-btn>
            </template>
            <v-card min-width="300">
                <v-list>
                    <v-list-item :prepend-avatar="user.image == null ? 'src/image_placeholder.jpg' : user.image"
                        :subtitle="user.email" :title="user.name">
                        <template v-slot:append>
                            <v-btn @click="edit = true" class="text-orange-darken-4" icon="mdi-pencil"
                                variant="text"></v-btn>
                        </template>
                    </v-list-item>
                </v-list>

                <!-- <v-divider></v-divider> -->
            </v-card>
        </v-menu>
        <v-menu v-model="chatbot" :close-on-content-click="false">
            <template v-slot:activator="{ props }">
                <v-btn v-bind="props" slim icon="mdi-robot">
                </v-btn>
            </template>
            <v-card min-width="300">
                <ChatBot />
            </v-card>
        </v-menu>

        <!-- Formulario de agregar/editar item -->
        <v-dialog width="auto" scrollable v-model="edit">
            <template v-slot:default="{ isActive }">
                <v-card :loading="loading" title="Editar perfil"
                    subtitle="Completa el formulario para editar los datos de su perfil">
                    <v-divider></v-divider>
                    <v-card-text class="px-4" style="height: 300px;">
                        <!-- <UserForm @submit="editProfile" :item="user" :loading="loading" /> -->
                    </v-card-text>
                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-btn text="Cerrar" prepend-icon="mdi-close" color="red" @click="isActive.value = false"></v-btn>

                        <v-spacer></v-spacer>

                        <v-btn color="blue" prepend-icon="mdi-content-save-edit" type="submit" text="Actualizar"
                            form="user-form"></v-btn>
                    </v-card-actions>
                </v-card>
            </template>
        </v-dialog>
    </v-app-bar>
</template>
