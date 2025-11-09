import { createApp } from 'vue';
import App from './App.vue';
import router from './router'; 
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css'; // Dùng theme Bootstrap để phù hợp giao diện

import { createPinia } from 'pinia';
const pinia = createPinia(); // Tạo Pinia instance

import './assets/main.css'; 
// 1. Import các thư viện chínhimport { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons'; 
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core'; // <-- Import byPrefixAndName
// import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'; 
// import { faUserSecret } from '@fortawesome/free-solid-svg-icons'; 
// THÊM TOÀN BỘ GÓI SOLID VÀ BRANDS VÀO LIBRARY
// library.add(fas, fab); // Thêm toàn bộ các icon đã import vào library
library.add(fas);

// Đăng ký component Font Awesome
// app.component('FontAwesomeIcon', FontAwesomeIcon);
const app = createApp(App);
app.component('fa', FontAwesomeIcon);
app.use(VueToast, {
    position: 'top-right', // Vị trí hiển thị
    duration: 5000,        // Thời gian hiển thị (5 giây)
    dismissible: true,
    type: 'default'
});
app.use(router).use(pinia).mount('#app'); 
