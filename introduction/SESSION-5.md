# Session 5: Top-Up Form, Account Lookup & Checkout UI

## Overview
Implement the core user interaction flow where players select a game, input their Game ID / Zone ID, verify their account name, and choose a package before checkout.

## Objectives
- Build the interactive Game Detail page.
- Create input fields for **Game ID** and **Zone ID** with a **Check Name** button.
- Build the package selection grid showing diamond/UC amounts and pricing.

---

## Step 1 — Build the Game Detail page

`frontend/src/pages/GameDetail.vue`:
```vue
<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import api from "../axios";

const route = useRoute();
const game = ref(null);
const userId = ref("");
const zoneId = ref("");
const username = ref(null);
const checking = ref(false);
const selectedPackage = ref(null);

onMounted(async () => {
  const res = await api.get(`/games/${route.params.slug}`);
  game.value = res.data;
});

async function checkName() {
  checking.value = true;
  username.value = null;
  try {
    const res = await api.post("/check-account", {
      game_id: route.params.slug,
      user_id: userId.value,
      zone_id: zoneId.value,
    });
    username.value = res.data.username;
  } catch (e) {
    username.value = "Not found";
  } finally {
    checking.value = false;
  }
}
</script>

<template>
  <div v-if="game">
    <h2>{{ game.name }}</h2>

    <!-- Step 1: Enter Game ID / Zone ID -->
    <input v-model="userId" placeholder="Game ID" />
    <input v-if="game.requires_zone_id" v-model="zoneId" placeholder="Zone ID" />
    <button @click="checkName" :disabled="checking">
      {{ checking ? "Checking..." : "Check Name" }}
    </button>
    <p v-if="username">Account: <strong>{{ username }}</strong></p>

    <!-- Step 2: Select a package -->
    <div class="packages">
      <div
        v-for="pkg in game.packages"
        :key="pkg.id"
        class="package-card"
        :class="{ active: selectedPackage?.id === pkg.id }"
        @click="selectedPackage = pkg"
      >
        <p>{{ pkg.name }}</p>
        <p>${{ pkg.price_usd }}</p>
      </div>
    </div>

    <!-- Step 3: Proceed to checkout (Session 6) -->
    <button :disabled="!username || !selectedPackage">
      Proceed to Payment
    </button>
  </div>
</template>
```

## Step 2 — Validate the flow

The UI now enforces a clear order:
1. User enters **Game ID** (and **Zone ID** if the game requires it).
2. User taps **Check Name** → calls `/api/check-account` from Session 3.
3. Once a username is returned, the user selects a top-up package.
4. Only then does the **Proceed to Payment** button become active.

## Step 3 — Style the package grid

```vue
<style scoped>
.packages {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 12px;
  margin: 16px 0;
}
.package-card {
  border: 1px solid #333;
  border-radius: 8px;
  padding: 12px;
  text-align: center;
  cursor: pointer;
}
.package-card.active {
  border-color: #22c55e;
}
</style>
```

## ✅ Result
Users can now fully browse a game, enter their account details, verify their in-game name, and pick a top-up package — ready to be connected to the KHQR payment flow in the next session.
