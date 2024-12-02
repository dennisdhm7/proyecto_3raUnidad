<script setup>
    import { ref, defineProps, onMounted } from "vue"
    import { useRoute } from "vue-router"

    import axios from "axios"
    import Utils from "../../Utils"
    import AppNavbar from "./MenuNavbar.vue"
    import Sidebar from "./MenuSidebar.vue"

    defineProps({
        title: String,
        cart: Object
    })

    const theme = ref("dark")

    const active = ref("home")

    const collapse = ref(true)

    const dialog = ref(false)

    const user = Utils.user()

    const route = useRoute()

    function logout() {
        dialog.value = false
        axios
            .post(
                "api/logout",
                {},
                {
                    headers: {
                        Authorization: `Bearer ${Utils.token()}`,
                    },
                }
            )
            .then(() => {
                localStorage.removeItem("jwt")
                location.href = "/login"
            })
    }

    onMounted(() => {
        active.value = route.path.substring(1)
    })
</script>
<template>
    <v-responsive theme="dark" class="border">
        <v-app theme="dark" class="bg-transparent">
            <v-navigation-drawer v-model="collapse" class="thin-scroll drawer scroll-start bg-[#2e2e2ec5] rounded-lg m-1 h-[calc(100vh-0.5rem)] border-0">
                <div class="text-center">
                    <img class="h-[160px] mx-auto rounded-md mt-2" src="../../assets/menu-digital-logo-transparent.png" />

                    <div class="mb-3">
                        <template v-if="Utils.user() == null">
                            <v-btn class="mt-2" to="/login" variant="tonal" color="blue">Iniciar sesión</v-btn>
                        </template>
                        <template v-else>
                            <h3 class="text-gray-200 text-center">{{ Utils.user().name }}</h3>
                            <p class="text-sm text-gray-300 text-center">
                                {{ Utils.user().email }}
                            </p>
                        </template>
                    </div>
                </div>
                <v-divider class="mb-2"></v-divider>

                <div class="px-2">
                    <Sidebar :cart="cart" :active="active" />
                </div>

                <template v-slot:append>
                    <div class="pa-2">
                        <v-dialog v-model="dialog" max-width="400" persistent>
                            <template v-slot:activator="{ props: activatorProps }">
                                <v-btn v-bind="activatorProps" prepend-icon="mdi mdi-logout" variant="tonal" color="orange-darken-4" block> Cerrar sesión </v-btn>
                            </template>

                            <v-card prepend-icon="mdi-logout" text="¿Está seguro que desea cerrar su sesión?" title="Confirmar">
                                <template v-slot:actions>
                                    <v-spacer></v-spacer>

                                    <v-btn color="blue" @click="dialog = false"> Cancelar </v-btn>

                                    <v-btn color="red" @click="logout"> Cerrar sesión </v-btn>
                                </template>
                            </v-card>
                        </v-dialog>
                    </div>
                </template>
            </v-navigation-drawer>

            <AppNavbar :title="title" v-model:user="user" v-model:collapse="collapse" v-model:theme="theme" />

            <v-main>
                <v-container>
                    <slot></slot>
                </v-container>
            </v-main>
        </v-app>
    </v-responsive>
</template>
