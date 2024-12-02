<script setup>
import { ref, defineModel, onMounted } from 'vue'
import { Loader } from "@googlemaps/js-api-loader"

const coordinates = defineModel()

/**
 * Referencia del mapa
 */
const map = ref(null)


/**
* Referencia al marcador del mapa
*/
const marker = ref(null)

async function init() {
    // Cargar el script de Google Maps
    const loader = new Loader({
        apiKey: "AIzaSyDGa5ypXzQ7UTqsfbGCQRL8gSSDHxaI9Po",
        version: "weekly",
    });

    try {
        await loader.importLibrary('maps');


        // Inicializar el mapa
        map.value = new google.maps.Map(document.querySelector('#map'), {
            center: { lat: coordinates.value.lat, lng: coordinates.value.lng },  // Coordenadas iniciales
            zoom: 15,  // Nivel de zoom
        });

        // Agregar listener para clics en el mapa
        map.value.addListener("click", (event) => {
            placeMarker(event.latLng);
        });

        placeMarker(coordinates.value)

    } catch (error) {
        console.error("Error al cargar Google Maps: ", error);
    }
}

function placeMarker(location) {
    // Si ya hay un marcador, lo movemos
    if (marker.value) {
        marker.value.setPosition(location);
    } else {
        // Crear el marcador y hacerlo arrastrable
        marker.value = new google.maps.Marker({
            position: location,
            map: map.value,
            draggable: true,  // Permitir arrastrar el marcador
        });

        // Evento cuando el usuario suelte el marcador después de arrastrarlo
        marker.value.addListener("dragend", () => {
            updateMarkerPosition(marker.value.getPosition());
        });
    }

    // Actualizar la posición del marcador
    updateMarkerPosition(location);
}

function updateMarkerPosition(position) {
    // Actualizar la variable en Vue con la nueva posición del marcador
    coordinates.value.lat = position.lat();
    coordinates.value.lng = position.lng();
    console.log("Ubicación actual del marcador:", coordinates.value);
}

onMounted(() => {
    init()
})
</script>
<template>
    <div id="map" ref="map" class="mt-1 rounded-md" style="height: 300px;"></div>
</template>
