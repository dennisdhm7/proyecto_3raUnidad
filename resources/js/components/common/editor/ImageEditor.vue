<script setup>
import { defineModel, defineProps, onMounted } from 'vue'
import Cropper from 'cropperjs';

const props = defineProps({
    image: String,
    aspectRatio: Number
})

const cropper = defineModel()

onMounted(() => {
    const image = document.getElementById('image');
    cropper.value = new Cropper(image, {
        aspectRatio: props.aspectRatio,
        autoCropArea: 1,
        viewMode: 3,
        dragMode: 'move',
        crop(event) {
            // data.value = cropper.getCroppedCanvas().toDataURL()
        },
    });
})
</script>
<template>
    <div class="max-w-[300px] max-h-[350px] mx-auto">
        <img id="image" :src="image">
    </div>
</template>
