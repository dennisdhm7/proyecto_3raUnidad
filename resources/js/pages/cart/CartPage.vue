<script setup>
import MenuLayout from "../../components/layout/MenuLayout.vue"
import CartItem from "./CartItem.vue"
import { ref, onMounted, getCurrentInstance } from "vue"
import axios from "../../request"
import Cart from "../../Cart.js"
import Utils from "../../Utils"

const { appContext } = getCurrentInstance()
const emitter = appContext.config.globalProperties.emitter

const cart = new Cart(emitter)

const selected = ref("Delivery")

const order = ref(cart.order)

const total = ref(0)

const address = ref("")
const name = ref(Utils.user() != null ? Utils.user().name : "")
const phone = ref(Utils.user() != null ? Utils.user().phone : "")

const payment = ref("Card")

const loading = ref(false)

const tables = ref([])

function save(e) {
    loading.value = true
    const data = new FormData(e.target)
    axios
        .post("/api/orders", data)
        .then((response) => {
            localStorage.removeItem("order")
            localStorage.setItem("last", JSON.stringify(response.data))
            loading.value = false
            window.open('/api/voucher/' + response.data.id, '_blank')
            location.href = '/client-orders'
        })
        .catch(() => {
            loading.value = false
        })
}

function select(type) {
    selected.value = type
    payment.value = 'Card'
}

onMounted(() => {
    emitter.on("cart-updated", (data) => {
        order.value = JSON.parse(JSON.stringify(data))
        total.value = cart.total
    })

    total.value = cart.total

    axios.get("/api/tables").then((response) => {
        tables.value = response.data
    })
})
</script>
<template>
    <main class="bg-gradient-to-tr from-gray-900 to-black">
        <MenuLayout :cart="cart" title="Carrito de compras">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <p class="text-gray-200">Carrito de compras</p>
                    <div class="grid grid-cols-3 gap-3">
                        <CartItem v-for="(item, index) in order.items" @remove="cart.remove(index)" :item="item"
                            :index="index" :cart="cart" />
                    </div>
                </div>
                <div v-if="Utils.token() != null && Utils.user() != null">
                    <form @submit.prevent="save" class="px-2 md:px-6">
                        <input type="hidden" name="client_id" :value="Utils.user().id" />
                        <input type="hidden" name="total" :value="total" />
                        <template v-for="(item, index) in order.items">
                            <input type="hidden" :name="`items[${index}][item_id]`" :value="item.item_id" />
                            <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity" />
                            <input type="hidden" :name="`items[${index}][price]`" :value="item.item.local_price" />
                            <input type="hidden" :name="`items[${index}][notes]`" :value="item.notes || ''" />
                        </template>

                        <div class="flex gap-4 mt-6">
                            <input type="hidden" name="type" :value="selected" />
                            <input type="hidden" name="payment_method" :value="payment" />
                            <v-btn variant="tonal" :color="selected == 'Delivery' ? 'blue' : 'white'" type="button"
                                @click="select('Delivery')"> A domicilio </v-btn>
                            <v-btn variant="tonal" :color="selected == 'Local' ? 'blue' : 'white'" type="button"
                                @click="select('Local')"> En el local </v-btn>
                        </div>
                        <div v-if="selected == 'Delivery'">
                            <p class="mt-4 mb-2 text-center text-gray-100 text-opacity-80">Rellena los datos de entrega</p>
                            <div class="relative w-full">
                                <v-text-field variant="solo-filled" v-model="name" name="client" required
                                    label="Su nombre"></v-text-field>
                                <v-text-field variant="solo-filled" v-model="phone" name="phone"
                                    label="Teléfono de contacto"></v-text-field>
                                <v-textarea variant="solo-filled" v-model="address" name="address"
                                    label="Dirección de entrega" rows="3"></v-textarea>
                            </div>
                        </div>
                        <div v-show="selected == 'Local'" class="py-4">
                            <v-select v-model="address" :items="tables" :rules="[Utils.RULES.required]" :disabled="loading"
                                item-value="name" item-title="name" name="address" variant="solo-filled" label="Su mesa"
                                required></v-select>
                        </div>

                        <p class="text-gray-200 text-opacity-80">Método de pago</p>
                        <div class="flex gap-4 mt-1">
                            <v-btn :disabled="selected == 'Delivery'" variant="tonal"
                                :color="payment == 'Cash' ? 'blue' : 'white'" type="button" @click="payment = 'Cash'">
                                Efectivo </v-btn>
                            <v-btn variant="tonal" :color="payment == 'Card' ? 'blue' : 'white'" type="button"
                                @click="payment = 'Card'"> Tarjeta </v-btn>
                        </div>

                        <v-row class="mt-2" dense>
                            <v-col cols="12">
                                <p class="text-gray-200 text-opacity-70 text-sm text-center" v-if="payment == 'Cash'">Espere
                                    que uno de nuestros empleados le cobre</p>
                                <p class="text-gray-200 text-opacity-70 text-sm text-center" v-else>Rellena los datos de tu
                                    tarjeta</p>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field :disabled="payment == 'Cash'"
                                    :rules="[Utils.RULES.required, Utils.RULES.card]" :hide-details="true"
                                    variant="solo-filled" required label="Número de tarjeta"></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field :disabled="payment == 'Cash'"
                                    :rules="[Utils.RULES.required, Utils.RULES.expire]" :hide-details="true"
                                    variant="solo-filled" required label="F. de vecimiento"></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field :disabled="payment == 'Cash'" :rules="[Utils.RULES.required, Utils.RULES.ccv]"
                                    :hide-details="true" variant="solo-filled" required label="CCV"></v-text-field>
                            </v-col>
                        </v-row>

                        <!-- <hr class="bg-gray-400 h-[2px]" /> -->
                        <div class="mt-4 px-2">
                            <div class="flex justify-between">
                                <p class="font-semibold text-gray-100 text-opacity-80">Subtotal:</p>
                                <p class="font-semibold text-gray-100 text-opacity-80">{{ total }}</p>
                            </div>
                            <div class="flex justify-between mt-1">
                                <p class="font-semibold text-gray-100 text-opacity-80">Costo de entrega:</p>
                                <p class="font-semibold text-gray-100 text-opacity-80">
                                    {{ selected == "Local" ? "No aplicable" : "55" }}
                                </p>
                            </div>
                            <hr class="bg-text-primary opacity-40 mt-4 mb-2" />
                            <div class="flex justify-between mt-1">
                                <p class="font-semibold text-gray-100 text-opacity-80">Total:</p>
                                <p class="font-semibold text-gray-100 text-opacity-80">{{ Number(total) + (selected ==
                                    "Local" ? 0 : 55) }}</p>
                            </div>
                        </div>
                        <v-btn class="mt-4 mx-auto" type="submit" variant="tonal" color="blue" :loading="loading"
                            :disabled="address == '' || (selected == 'Delivery' && (name == '' || phone == '')) || order.items.length == 0 || loading">
                            Realizar pedido </v-btn>
                    </form>
                </div>
                <div v-else>
                    <v-empty-state icon="mdi-login">
                        <template v-slot:media>
                            <v-icon color="surface-variant"></v-icon>
                        </template>

                        <template v-slot:headline>
                            <div class="text-h4">¡Ups!</div>
                        </template>

                        <template v-slot:text>
                            <div class="text-medium-emphasis text-caption">
                                Inicia sesión para realizar el pedido
                                <v-btn to="/login" class="mt-4" variant="tonal" color="blue">Iniciar sesión</v-btn>
                            </div>
                        </template>
                    </v-empty-state>
                </div>
            </div>
        </MenuLayout>
    </main>
</template>
