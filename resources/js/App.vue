<script setup>
import { ref, getCurrentInstance, onMounted } from 'vue';
const { appContext } = getCurrentInstance();
const emitter = appContext.config.globalProperties.emitter;

const loading = ref(true)

onMounted(() => {
    emitter.on('loading', (value) => {
        loading.value = value;
    })
})
</script>

<template>
    <router-view v-if="!loading"></router-view>
    <!-- loader section -->
    <div v-else class="container-fluid loader-wrap h-full flex items-center justify-center">
       
    </div>
    <!-- loader section ends -->
</template>
<style scoped>

</style>
