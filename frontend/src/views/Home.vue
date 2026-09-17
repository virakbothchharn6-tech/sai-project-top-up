<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const games = ref([]);
const loading = ref(true);
const errorMessage = ref("");

const fetchGames = async () => {
  try {
    const response = await axios.get("http://localhost/api/games");
    games.value = response.data.data || response.data;
  } catch (error) {
    console.error("Error fetching games:", error);
    errorMessage.value = "មិនអាចទាញយកទិន្នន័យហ្គេមបានទេ";
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchGames();
});
</script>

<template>
  <div class="home-container">
    <h2>🎮 បញ្ជីហ្គេមសម្រាប់បំពេញប្រាក់ (Game Catalog)</h2>

    <div v-if="loading" class="loading">កំពុងទាញយកទិន្នន័យ...</div>
    <div v-if="errorMessage" class="error">{{ errorMessage }}</div>

    <div v-if="!loading && games.length > 0" class="games-grid">
      <div v-for="game in games" :key="game.id" class="game-card">
        <div class="game-banner">
          <span class="game-icon">🎮</span>
          <span class="game-badge">{{ game.name }}</span>
        </div>

        <h3>{{ game.name }}</h3>
        <p>{{ game.description || "សេវាកម្មបញ្ចូលលុយហ្គេមរហ័ស" }}</p>
        <!-- កែប្រែទៅតាមស្តង់ដារ Session 4 ដោយប្រើ game.slug -->
        <router-link :to="`/game/${game.slug}`" class="topup-btn">
          បញ្ចូលលុយ (Top Up)
        </router-link>
      </div>
    </div>

    <div v-if="!loading && games.length === 0 && !errorMessage" class="empty">
      មិនទាន់មានទិន្នន័យហ្គេមនៅឡើយទេ
    </div>
  </div>
</template>

<style scoped>
.home-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 20px;
  font-family:
    system-ui,
    -apple-system,
    sans-serif;
}
.games-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 20px;
  margin-top: 20px;
}
.game-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px;
  text-align: center;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.game-banner {
  width: 100%;
  height: 130px;
  border-radius: 10px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 14px;
  color: white;
}
.game-icon {
  font-size: 40px;
}
.game-badge {
  font-size: 13px;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.2);
  padding: 3px 10px;
  border-radius: 20px;
}
.topup-btn {
  display: inline-block;
  margin-top: 12px;
  padding: 8px 16px;
  background: #4f46e5;
  color: white;
  border-radius: 6px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: background 0.2s;
}
.topup-btn:hover {
  background: #4338ca;
}
.loading,
.error,
.empty {
  text-align: center;
  margin-top: 40px;
  font-size: 16px;
  color: #64748b;
}
</style>
