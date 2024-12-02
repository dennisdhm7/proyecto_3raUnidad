export default [
    {
        path: "/login",
        name: "login",
        component: () => import("../pages/login/LoginPage.vue"),
        meta: {
            title: "Iniciar sesión en Restaurante Capitán Chino",
            loading: false,
        },
    },

    {
        path: "/register",
        name: "register",
        component: () => import("../pages/login/RegisterPage.vue"),
        meta: {
            title: "Registrarse en Restaurante Capitán Chino",
            loading: false,
        },
    },

    {
        path: "/",
        name: "menu",
        component: () => import("../pages/menu/MenuPage.vue"),
        meta: {
            title: "Menú Restaurante Capitán Chino",
            loading: false,
        },
    },

    {
        path: "/home",
        name: "home",
        component: () => import("../pages/home/HomePage.vue"),
        meta: {
            title: "Restaurante Capitán Chino",
            loading: false,
        },
    },

    {
        path: "/cart",
        name: "cart",
        component: () => import("../pages/cart/CartPage.vue"),
        meta: {
            title: "Carrito de compras - Restaurante Capitán Chino",
            loading: false,
        },
    },

    {
        path: "/dashboard",
        name: "dashboard",
        component: () => import("../pages/dashboard/DashboardPage.vue"),
        meta: {
            title: "Dashboard",
            authRequired: true,
            loading: false,
        },
    },

    {
        path: "/categories",
        name: "categories",
        component: () => import("../pages/categories/CategoriesPage.vue"),
        meta: {
            title: "Categorías",
            authRequired: true,
            loading: false,
        },
    },

    {
        path: "/items",
        name: "items",
        component: () => import("../pages/items/ItemsPage.vue"),
        meta: {
            title: "Productos o servicios",
            authRequired: true,
            loading: false,
        },
    },

    {
        path: "/users",
        name: "users",
        component: () => import("../pages/users/UsersPage.vue"),
        meta: {
            title: "Usuarios",
            authRequired: true,
            loading: false,
        },
    },

    {
        path: "/orders",
        name: "orders",
        component: () => import("../pages/orders/OrdersPage.vue"),
        meta: {
            title: "Ventas y Pedidos",
            authRequired: true,
            loading: false,
        },
    },

    {
        path: "/client-orders",
        name: "client-orders",
        component: () => import("../pages/client-orders/OrdersPage.vue"),
        meta: {
            title: "Mis pedidos",
            authRequired: true,
            loading: false,
        },
    },

    {
        path: "/reservations",
        name: "reservations",
        component: () => import("../pages/reservations/ReservationsPage.vue"),
        meta: {
            title: "Reservaciones",
            authRequired: true,
            loading: false,
        },
    },

    {
        path: "/client-reservations",
        name: "client-reservations",
        component: () => import("../pages/client-reservations/ReservationsPage.vue"),
        meta: {
            title: "Reservaciones",
            authRequired: true,
            loading: false,
        },
    },
];
