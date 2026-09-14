# Session 4: Frontend UI Setup & Game Catalog

## Overview
Initialize the Vue.js 3 frontend application using Vite and build the primary game browsing catalog.

## Objectives
- Configure Axios for API communication with the Laravel backend.
- Build the Home Page UI with responsive game cards.
- Set up Vue Router for navigation between pages.

---

## Step 1 — Scaffold the Vue.js project

```bash
docker-compose exec frontend npm create vite@latest . -- --template vue
docker-compose exec frontend npm install
docker-compose exec frontend npm install axios vue-router@4
```

## Step 2 — Configure Axios

`frontend/src/axios.js`:
```javascript
import axios from "axios";

const api = axios.create({
  baseURL: "http://localhost:8000/api",
});

export default api;
```

## Step 3 — Set up Vue Router

`frontend/src/router.js`:
```javascript
import { createRouter, createWebHistory } from "vue-router";
import Home from "./pages/Home.vue";
import GameDetail from "./pages/GameDetail.vue";

const routes = [
  { path: "/", component: Home },
  { path: "/game/:slug", component: GameDetail },
];

export default createRouter({
  history: createWebHistory(),
  routes,
});
```

`frontend/src/main.js`:
```javascript
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

createApp(App).use(router).mount("#app");
```

## Step 4 — Build the Home Page with game catalog

`frontend/src/pages/Home.vue`:
```vue
<script setup>
import { ref, onMounted } from "vue";
import api from "../axios";

const games = ref([]);

onMounted(async () => {
  const res = await api.get("/games");
  games.value = res.data;
});
</script>

<template>
  <div class="grid">
    <router-link
      v-for="game in games"
      :key="game.id"
      :to="`/game/${game.slug}`"
      class="game-card"
    >
      <img :src="game.thumbnail" :alt="game.name" />
      <h3>{{ game.name }}</h3>
    </router-link>
  </div>
</template>

<style scoped>
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 16px;
}
.game-card {
  border-radius: 8px;
  overflow: hidden;
  text-align: center;
  text-decoration: none;
  color: inherit;
}
</style>
```

## Step 5 — Run and test the frontend

```bash
docker-compose exec frontend npm run dev -- --host
```
Visit `http://localhost:5173` — the game catalog should load from the Laravel API built in Session 3.

## ✅ Result
The frontend now displays a browsable list of games fetched live from the backend API, with routing ready for the Game Detail page in the next session.
