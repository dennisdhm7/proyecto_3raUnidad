<script>
import axios from "../../request";
import logo from '../../assets/menu-digital-logo-icon.png'
export default {
    data() {
        const readed = localStorage.getItem('notifications-readed-at');
        let readedAt = null
        if (readed != null) {
            readedAt = new Date(readed)
        }
        return {
            notifications: [],
            page: 1,
            unread: 0,
            logo: logo,
            open: false,
            readedAt: readedAt
        }
    },
    watch: {
        open(value) {
            if (value) {
                localStorage.setItem('notifications-readed-at', (new Date()).toString());
                this.unread = 0
            }
        }
    },
    methods: {
        load() {
            axios.get(`api/notifications?page=${this.page}&per_page=100`).then((response) => {
                // Establecer los items de la página
                const id = this.Utils.user().id
                this.notifications = response.data.data
                for (let notification of this.notifications) {
                    if (notification.content.indexOf('Ha') == 0 && notification.user_id == id) {
                        notification.content = notification.content.replace('Ha', 'Has')
                    }
                    const date = new Date(notification.created_at)
                    if (this.readedAt == null || date.valueOf() > this.readedAt.valueOf()) {
                        this.unread++
                    }
                }
            })
        },
    },
    mounted() {
        this.emitter.on('notifications-update', () => {
            this.load()
        })
        this.load()
    }
}
</script>
<template>
    <v-menu v-model="open">
        <template v-slot:activator="{ props }">
            <v-btn v-bind="props" icon="mdi-bell-outline">
                <v-badge color="error" :content="unread" floating>
                    <v-icon>mdi-bell-outline</v-icon>
                </v-badge>
            </v-btn>
        </template>
        <v-card min-width="300">
            <v-list class="px-2">
                <template v-for="(notification, index) in notifications">
                    <v-list-item class="py-3" v-if="notification.user != null"
                        :prepend-avatar="notification.user.image == null ? 'src/user_placeholder.jpg' : notification.user.image"
                        :subtitle="notification.content">
                        <template v-slot:title>
                            <small class="float-right text-gray-500">{{ Utils.datetime(notification.created_at,
                                Utils.FULL_DATE_TIME)
                            }}</small>
                            <v-list-item-title>
                                {{ notification.user.name }}
                            </v-list-item-title>
                        </template>
                    </v-list-item>
                    <v-list-item class="py-3" v-else :prepend-avatar="logo" :subtitle="notification.content">
                        <template v-slot:title>
                            <small class="float-right text-gray-500">{{ Utils.datetime(notification.created_at,
                                Utils.FULL_DATE_TIME)
                            }}</small>
                            <v-list-item-title>
                                Sistema
                            </v-list-item-title>
                        </template>
                    </v-list-item>
                    <v-divider v-if="index != notifications.length - 1"></v-divider>
                </template>
            </v-list>

        </v-card>
    </v-menu>
</template>
