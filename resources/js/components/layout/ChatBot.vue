<script setup>
    import { ref, onMounted } from "vue"

    import bot from "../../chat-messages.js"

    const loading = ref(false)

    const suggestions = ref([bot[0]])

    const messages = ref([])

    function select(suggestion) {
        loading.value = true
        messages.value.push({
            type: suggestion.answer == 0 ? "bot" : "user",
            text: suggestion.message,
        })

        setTimeout(() => {
            if (suggestion.answer != 0) {
                messages.value.push({
                    type: "bot",
                    text: bot[suggestion.answer - 1].message,
                })
                suggestions.value = []
                for (let id of bot[suggestion.answer - 1].suggestions) {
                    suggestions.value.push(bot[id - 1])
                }
            }
            loading.value = false
            localStorage.setItem("messages", JSON.stringify(messages.value))
        }, 300)
    }

    onMounted(() => {
        const local = localStorage.getItem("messages")
        if (local != null) {
            messages.value = JSON.parse(local)
        }
    })
</script>
<template>
    <div>
        <div class="max-w-[300px] p-4 max-h-[400px] overflow-auto">
            <template v-for="message in messages">
                <div v-if="message.type == 'user'" class="ps-20 py-2 text-sm">
                    <div class="bg-blue-500 p-2 rounded">{{ message.text }}</div>
                </div>
                <div v-else class="pe-20 py-2 text-sm">
                    <div class="p-2 rounded bg-gray-600">{{ message.text }}</div>
                </div>
            </template>

            <div class="mt-4">
                <v-chip class="my-0.5" @click="select(suggestion)" size="small" color="blue" v-for="suggestion in suggestions">{{ suggestion.message }}</v-chip>
            </div>
        </div>
    </div>
</template>
