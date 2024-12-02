<script setup>
import { defineProps, defineEmits, ref } from 'vue'
import { VDateInput } from 'vuetify/labs/VDateInput'
import axios from '../../../request.js'
import Utils from '../../../Utils'

const CAPACITIES = []
for (let i = 1; i <= 100; i++) {
    CAPACITIES.push({
        id: i,
        name: `${i} ${i == 1 ? 'persona' : 'personas'}`
    })
}

const HOURS = []
for (let i = 0; i <= 24; i++) {
    HOURS.push({
        id: i,
        name: `${i} ${i == 1 ? 'hora' : 'horas'}`
    })
}

const MINUTES = []
for (let i = 0; i <= 60; i++) {
    MINUTES.push({
        id: i,
        name: `${i} ${i == 1 ? 'minuto' : 'minutos'}`
    })
}

/**
 * Definir el evento de envío del form
 */
const emit = defineEmits(['submit'])

defineProps({
    /**
     * Datos del item a actualizar
     */
    item: Object,
    /**
     * True si se está realizando alguna petición al backend
     */
    loading: Boolean,
    /**
     * Tablas disponibles
     */
    tables: Array,
})

/**
 * Estado de verificación del formulario
 */
const form = ref(false)

/**
 * Fecha de la reservación
 */
const date = ref(new Date())

/**
 * Hora de la reservación
 */
const time = ref('')

/**
 * Cantidad de horas de reservación
 */
const hours = ref(0)

/**
 * Cantidad de minutos de reservación
 */
const minutes = ref(45)

/**
 * Mesas disponibles
 */
const available = ref([])

/**
 * Mesas seleccionadas
 */
const selected = ref([])

function send(e) {
    if (!form.value) return
    //Obtener datos del formulario
    const data = new FormData(e.target)

    const datetime = `${date.value.getFullYear()}-${date.value.getMonth() + 1}-${date.value.getDate()} ${time.value}:00`
    const temp = new Date(datetime)
    if (temp.valueOf() - new Date().valueOf() < 7246262) {
        Utils.notify('Error', 'Debes reservar con 2h de antelación', 'mdi mdi-alert-octagon-outline', '#f55555')
        return
    }

    if (!isRestaurantOpen(temp)) {
        Utils.notify('Error', 'Debes reservar solo en el horario de atención del restaurant', 'mdi mdi-alert-octagon-outline', '#f55555')
        return
    }

    const rtime = Number(hours.value) * 60 + Number(minutes.value)

    data.append('reservation_datetime', datetime)
    data.append('reservation_time', rtime)

    emit('submit', data)
}

function isRestaurantOpen(date) {
    // Obtener el día de la semana (0: Domingo, 1: Lunes, ..., 6: Sábado)
    const day = date.getDay();
    // Obtener las horas y minutos
    const hours = date.getHours();
    const minutes = date.getMinutes();

    // Definir horarios en minutos desde las 00:00
    const openTime = 8 * 60; // 08:00AM en minutos
    const closeTimeMondayToSaturday = 23 * 60 + 30; // 11:30PM en minutos
    const closeTimeTuesday = 14 * 60; // 02:00PM en minutos

    // Convertir la hora actual a minutos desde las 00:00
    const currentTime = hours * 60 + minutes;

    // Verificar horarios
    if (day === 2) { // Martes
        return currentTime >= openTime && currentTime <= closeTimeTuesday;
    } else if (day >= 1 && day <= 6 || day === 0) { // Miércoles a Lunes
        return currentTime >= openTime && currentTime <= closeTimeMondayToSaturday;
    }

    return false; // No debería llegar aquí
}

function update() {
    if (time.value != '') {
        const datetime = `${date.value.getFullYear()}-${date.value.getMonth() + 1}-${date.value.getDate()}_${time.value}`
        const rtime = Number(hours.value) * 60 + Number(minutes.value)
        axios.get(`/api/reservations/tables-check/${datetime}/${rtime}`).then((response) => {
            available.value = response.data
        })
    }
}

function add(id) {
    const i = selected.value.indexOf(id)

    if (i == -1) {
        selected.value.push(id)
    }
}

function remove(id) {
    const i = selected.value.indexOf(id)

    if (i !== -1) {
        selected.value.splice(i, 1)
    }
}
</script>
<template>
    <div class="max-w-[400px]">
        <v-form v-model="form" @submit.prevent="send" id="reservation-form">
            <input v-if="item.client_id != undefined" type="hidden" name="client_id" :value="item.client_id" />
            <v-row dense>
                <v-col cols="12" md="6" sm="6">
                    <v-text-field :rules="[Utils.RULES.required]" :disabled="loading" v-model="item.client" name="client"
                        variant="solo-filled" label="Nombre del cliente" required></v-text-field>
                </v-col>

                <v-col cols="12" md="6" sm="6">
                    <v-select :rules="[Utils.RULES.required]" v-model="item.client_quantity" item-value="id"
                        item-title="name" :items="CAPACITIES" name="client_quantity" variant="solo-filled"
                        label="Capacidad"></v-select>
                </v-col>


                <v-col cols="12" md="6" sm="6">
                    <v-date-input @change="update" :prepend-icon="null" :rules="[Utils.RULES.required]" :disabled="loading"
                        v-model="date" variant="solo-filled" label="Fecha de la reservación" required></v-date-input>
                </v-col>

                <v-col cols="12" md="6" sm="6">
                    <v-text-field @change="update" :rules="[Utils.RULES.required]" type="time" :disabled="loading"
                        v-model="time" variant="solo-filled" label="Hora de la reservación" required></v-text-field>
                </v-col>

                <v-col cols="12" md="6" sm="6">
                    <v-select @change="update" v-model="hours" item-value="id" item-title="name" :items="HOURS"
                        variant="solo-filled" label="Cantidad de horas"></v-select>
                </v-col>

                <v-col cols="12" md="6" sm="6">
                    <v-select @change="update" v-model="minutes" item-value="id" item-title="name" :items="MINUTES"
                        variant="solo-filled" label="Cantidad de minutos"></v-select>
                </v-col>

                <input v-for="(table, index) in selected" type="hidden" :name="`tables[${index}]`" :value="table" />

                <v-col cols="12">
                    <v-list-item class="px-0" v-for="table in tables" :title="table.name"
                        :subtitle="`${table.capacity} ${table.capacity ? 'persona' : 'personas'}`">
                        <template v-slot:prepend>
                            <v-checkbox @change="(value) => value ? add(table.id) : remove(table.id)"
                                :disabled="!available.includes(table.id)" :hide-details="true"
                                color="orange-darken-4"></v-checkbox>
                        </template>
                    </v-list-item>
                </v-col>
            </v-row>
        </v-form>
    </div>
</template>
