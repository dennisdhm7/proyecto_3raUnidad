<script>
export default {
    emits: ['submit'],
    props: {
        item: Object,
        loading: Boolean,
    },
    data() {
        return {
            permissions: [],
            selected: [],
            form: false,
            password: '',
            ROLES: [
                { 'id': 'Administrator', 'name': 'Administrador' },
                { 'id': 'Manager', 'name': 'Gestor' },
                { 'id': 'Waiter', 'name': 'Camarero/Dependiente' },
                { 'id': 'Grocer', 'name': 'Almacenero' },
                { 'id': 'Other', 'name': 'Otro' },
            ]
        }
    },
    methods: {
        send(e) {
            if (!this.form) return
            const data = new FormData(e.target)
            //Manejar error de Vuetify (si no se selecciona un rol o un tipo de documento en el formulario)
            if (data.get('role') == '') {
                data.set('role', this.item.role)
            }
            this.$emit('submit', data)
        },
        update(role) {
            console.log(role)
            switch (role) {
                case 'Administrator':
                    this.selected = this.permissions.map((permission) => permission.id)
                    break
                case 'Manager':
                    this.selected = [1, 2, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27]
                    break
                case 'Waiter':
                    this.selected = [7, 10, 16, 17, 18, 19,]
                    break
                case 'Grocer':
                    this.selected = [7, 10, 24, 25, 26]
                    break
                case 'Other':
                    this.selected = [7, 10, 24, 25, 26]
                    break
            }
        }
    },
    created() {
        axios.get('/api/permissions').then((response) => {
            this.permissions = response.data
        })
    },
    mounted() {
        if (this.item.permissions != undefined) {
            for (const permission of this.item.permissions) {
                this.selected.push(permission.id)
            }
        }
    }
}
</script>
<template>
    <v-form v-model="form" @submit.prevent="send" id="user-form">
        <v-row dense>
            <v-col cols="12" md="6" sm="6">
                <v-text-field :rules="[Utils.RULES.required, Utils.RULES.name]" :disabled="loading" v-model="item.name"
                    name="name" variant="solo-filled" label="Nombre(s) y apellidos" required></v-text-field>
            </v-col>

            <v-col cols="12" md="6" sm="6">
                <v-text-field :rules="[Utils.RULES.required, Utils.RULES.phone]" :disabled="loading"
                    v-model="item.phone" name="phone" variant="solo-filled" label="Número de teléfono" type="tel"
                    required></v-text-field>
            </v-col>

            <v-col cols="12" md="6" sm="6">
                <v-text-field :rules="[Utils.RULES.required, Utils.RULES.email]" :disabled="loading"
                    v-model="item.email" name="email" variant="solo-filled" label="Correo electrónico" type="email"
                    required></v-text-field>
            </v-col>
            <v-col v-if="item.id == undefined" cols="12" md="6" sm="6">
                <v-text-field autocomplete="new-password" v-if="password != ''"
                    :rules="[Utils.RULES.required, Utils.RULES.password]" :disabled="loading" v-model="password"
                    name="password" variant="solo-filled" label="Contraseña" type="password" required></v-text-field>
                <v-text-field autocomplete="new-password" v-else :disabled="loading" v-model="password" name="password"
                    variant="solo-filled" label="Contraseña" type="password" required></v-text-field>
            </v-col>

            <v-col cols="12">
                <v-select @update:model-value="update" :items="ROLES" item-value="id" item-title="name"
                    :rules="[Utils.RULES.required]" :disabled="loading" v-model="item.role" name="role"
                    variant="solo-filled" label="Rol o puesto" required></v-select>
            </v-col>

            <!-- <v-col cols="12">
                <p class="text-orange-500 text-center mb-2">Permisos del usuario</p>
                <input v-for="(id, index) in selected" type="hidden" :name="`permissions[${index}]`" :value="id" />
                <v-row dense>
                    <v-col v-for="permission in permissions" cols="12" md="6" sm="6">
                        <v-checkbox v-model="selected" density="compact" :hide-details="true" color="orange-darken-4"
                            :value="permission.id" :label="permission.name"></v-checkbox>
                    </v-col>
                </v-row>
            </v-col> -->
        </v-row>
    </v-form>
</template>
