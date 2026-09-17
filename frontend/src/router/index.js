import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import GameDetail from "../views/GameDetail.vue";

const routes = [
  { path: "/", name: "Home", component: Home },
  { path: "/game/:slug", name: "GameDetail", component: GameDetail },
];

export default createRouter({
  history: createWebHistory(),
  routes,
});
