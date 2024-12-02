<script>
    import CustomAlert from "./Alert.vue"

    export default {
        components: { CustomAlert },
    }
</script>

<template>
    <CustomAlert :config="config" />
</template>
