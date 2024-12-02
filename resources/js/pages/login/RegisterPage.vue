<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter, RouterLink } from 'vue-router'

const error = ref('')
const loading = ref(false)
const router = useRouter()

function login(e) {
    loading.value = true
    error.value = ''
    axios.post('api/register', new FormData(e.target)).then((response) => {
        localStorage.setItem('jwt', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))
        localStorage.setItem('user_role', response.data.type)

        loading.value = false

        router.push({
            path: '/'
        });

    }).catch((e) => {
        // this.error = error.response.data.message
        loading.value = false
        console.log(e)
        if (e.response.status == 422) {
            const data = e.response.data
            error.value = data.message
        }
    })
}
</script>
<template>
    <section class="h-screen w-screen bg-cover flex justify-center items-center overflow-auto">
        <div class="rounded-xl  bg-opacity-20 backdrop-blur-sm p-6 m-auto shadow-lg shadow-[#00000080]">
            <img src="../../assets/menu-digital-logo-transparent.png" class="h-24 w-24 mx-auto mb-4" />
            <h1 class="text-center text-3xl font-bold text-logo">Capitán Chino</h1>
            <p class="text-center text-gray-200">Iniciar sesión</p>

            <v-form @submit.prevent="login" class="mt-4 min-w-64" theme="dark">
                <div class="mb-4">
                    <v-text-field theme="dark" name="name" color="blue-grey-lighten-2" :disabled="loading"
                        label="Nombre del negocio" prepend-inner-icon="mdi-email" variant="solo-filled"
                        :hide-details="true"></v-text-field>
                    <v-text-field class="mt-4" theme="dark" name="email" color="blue-grey-lighten-2" :disabled="loading"
                        label="Correo electrónico" prepend-inner-icon="mdi-email" variant="solo-filled"
                        :hide-details="true"></v-text-field>
                    <v-text-field theme="dark" class="mt-4" :error="error != ''" :error-messages="error" name="password"
                        color="blue-grey-lighten-2" :disabled="loading" label="Contraseña" prepend-inner-icon="mdi-key"
                        type="password" variant="solo-filled"></v-text-field>
                </div>
                <v-btn type="submit" :loading="loading" class="w-full" color="orange-darken-4"
                    variant="tonal">Registrarse</v-btn>
                <p class="mt-6 text-center text-gray-200 text-sm">¿Tienes una cuenta?</p>
                <p class="text-center text-sm">
                    <RouterLink to="/login" class="text-sky-600">¡Inicia sesión!</RouterLink>
                </p>
            </v-form>
        </div>
    </section>
</template>
<style scoped>
section {
    background: url(../../assets/login-bg.svg);
}
</style>
