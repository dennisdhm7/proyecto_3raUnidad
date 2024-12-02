<script setup>
import { ref, onMounted } from 'vue'
import Utils from '../../Utils'
import axios from '../../request'

import LayoutComponent from '../../components/layout/LayoutComponent.vue';
import SalesChart from './SalesChart.vue';
import OrdersChart from './OrdersChart.vue';
import ReservationsChart from './ReservationsChart.vue';

const assistant = ref({
    open: false,
    page: 0,
})


onMounted(() => {
    axios.get('/api/user').then((response) => {
        localStorage.setItem('user', JSON.stringify(response.data))
    })
})
</script>
<template>
    <section class="bg-cover">
        <!-- Layout principal -->
        <LayoutComponent title="Dashboard">
            <div class="w-full min-h-[calc(100vh-100px)]  rounded-md">
                <p class="text-gray-200 text-center mb-2">Productos más vendidos</p>
                <SalesChart />
                <p class="text-gray-200 text-center mb-2 mt-8">Historial de pedidos</p>
                <OrdersChart />
                <p class="text-gray-200 text-center mb-2 mt-8">Historial de reservaciones</p>
                <ReservationsChart />
            </div>
        </LayoutComponent>
    </section>
</template>
<style scoped>
section {
    background: url(../../assets/login-bg.svg);
}
</style>
