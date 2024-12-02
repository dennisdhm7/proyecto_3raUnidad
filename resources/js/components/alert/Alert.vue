<script>
import Mixin from "./AlertMixin.js"

export default {
    /**
     * @mixin: ./AlertMixin.js
     */
    mixins: [Mixin],
}
</script>

<template>
    <div class="toast items-center border-0 z-[2500]" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="flex relative bg-gray-900 rounded-md shadow-md shadow-[#00000067] z-[2500]">
            <div class="toast-body flex items-center p-3">
                <div class="mx-2 flex items-center justify-center">
                    <i class="text-6xl" :class="config.icon" :style="{ color: config.color }"></i>
                </div>
                <div class="border-l border-l-gray-300 py-2 px-3">
                    <h6 class="mb-1 text-white font-bold">{{ config.title }}</h6>
                    <p class="mb-0 text-gray-300">{{ config.text }}</p>
                </div>
            </div>
            <div :style="{ background: config.color }" class="toast-progress"></div>
            <button @click="close" type="button" class="btn-close me-2 mt-2 ms-auto text-white" data-bs-dismiss="toast"
                aria-label="Close">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
    </div>
</template>

<style scoped>
@tailwind base;
@tailwind components;
@tailwind utilities;

.toast-progress {
    position: absolute;
    width: 100%;
    height: 5px;
    bottom: 0;
    border-bottom-left-radius: var(--bs-toast-border-radius);
    border-bottom-right-radius: var(--bs-toast-border-radius);
    transition: width ease-in-out 0.1s;
}

.toast:not(.show) {
    display: none;
}

.toast {
    --bs-toast-zindex: 1090;
    --bs-toast-padding-x: 0.75rem;
    --bs-toast-padding-y: 0.5rem;
    --bs-toast-spacing: 1.5rem;
    --bs-toast-max-width: 350px;
    --bs-toast-font-size: 0.875rem;
    --bs-toast-color: ;
    --bs-toast-bg: rgba(var(--bs-body-bg-rgb), 0.85);
    --bs-toast-border-width: var(--bs-border-width);
    --bs-toast-border-color: var(--bs-border-color-translucent);
    --bs-toast-border-radius: var(--bs-border-radius);
    --bs-toast-box-shadow: var(--bs-box-shadow);
    --bs-toast-header-color: var(--bs-secondary-color);
    --bs-toast-header-bg: rgba(var(--bs-body-bg-rgb), 0.85);
    --bs-toast-header-border-color: var(--bs-border-color-translucent);
    width: var(--bs-toast-max-width);
    max-width: 100%;
    font-size: var(--bs-toast-font-size);
    color: var(--bs-toast-color);
    pointer-events: auto;
    background-color: var(--bs-toast-bg);
    background-clip: padding-box;
    border: var(--bs-toast-border-width) solid var(--bs-toast-border-color);
    box-shadow: var(--bs-toast-box-shadow);
    border-radius: var(--bs-toast-border-radius);
}

.toast.showing {
    opacity: 0;
}

.toast:not(.show) {
    display: none;
}

.toast-body {
    padding: var(--bs-toast-padding-x);
    word-wrap: break-word;
}

.btn-close {
    --bs-btn-close-color: #000;
    --bs-btn-close-bg: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e");
    --bs-btn-close-opacity: 0.5;
    --bs-btn-close-hover-opacity: 0.75;
    --bs-btn-close-focus-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    --bs-btn-close-focus-opacity: 1;
    --bs-btn-close-disabled-opacity: 0.25;
    --bs-btn-close-white-filter: invert(1) grayscale(100%) brightness(200%);
    box-sizing: content-box;
    width: 1em;
    height: 1em;
    padding: 0.25em 0.25em;
    color: var(--bs-btn-close-color);
    background: transparent var(--bs-btn-close-bg) center/1em auto no-repeat;
    border: 0;
    border-radius: 0.375rem;
    opacity: var(--bs-btn-close-opacity);
}

.btn-close:hover {
    color: var(--bs-btn-close-color);
    text-decoration: none;
    opacity: var(--bs-btn-close-hover-opacity);
}

.btn-close:focus {
    outline: 0;
    box-shadow: var(--bs-btn-close-focus-shadow);
    opacity: var(--bs-btn-close-focus-opacity);
}</style>
