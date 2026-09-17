import { createApp } from "vue";
import "./style.css";
import App from "./App.vue";
import router from "./router";
import axios from "axios";

// note bash to laravel backen
axios.defaults.baseURL = "http://localhost/api";

const app = createApp(App);
app.use(router);
app.mount("#app");
