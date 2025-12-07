// main.js
import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router";
import axios from "./plugins/axios";

// Toast
import VueToast from "vue-toast-notification";
import "vue-toast-notification/dist/theme-bootstrap.css";

// FontAwesome
import { library } from "@fortawesome/fontawesome-svg-core";
import { fas } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
library.add(fas);

import "./assets/main.css";

// --------------------------------
//     KHỞI TẠO APP
// --------------------------------
const app = createApp(App);
const pinia = createPinia();
app.use(pinia);

// Sau khi gắn pinia → mới dùng authStore
import { useAuthStore } from "./stores/auth";
const authStore = useAuthStore();

// Khôi phục token từ localStorage
authStore.initializeStore();

// Setup token cho axios (nếu có)
if (authStore.token) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${authStore.token}`;
}

// Toast
app.use(VueToast, {
    position: "top-right",
    duration: 5000,
    dismissible: true,
});

// FontAwesome
app.component("fa", FontAwesomeIcon);

// Router
app.use(router);

// Start App
app.mount("#app");
