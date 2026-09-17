<script setup>
import { ref, nextTick } from "vue";
import axios from "axios";

// បញ្ជីហ្គេមផ្លូវការ រូបភាព Logo ផ្ទាល់ និង In-Game Currency ដាច់ដោយឡែក
const gamesList = ref([
  {
    id: "mlbb",
    name: "Mobile Legends",
    currency: "Diamonds",
    currencyIcon: "💎",
    badge: "ពេញនិយម",
    themeColor: "#2563eb",
    logo: "https://images.seeklogo.com/logo-png/38/2/mobile-legends-bang-bang-logo-png_seeklogo-385558.png",
    needsZone: true,
    categories: [
      {
        title: "🎟️ កញ្ចប់ Full Ticket & Pass",
        packages: [
          {
            id: "ml-ft1",
            name: "Full Ticket + Weekly x2",
            sub: "55 💎 + Weekly x2",
            bonus: "+5 ពិ",
            price: 3.83,
          },
          {
            id: "ml-ft2",
            name: "Full Ticket + Weekly x1",
            sub: "165 💎 + Weekly x1",
            bonus: "+5 ពិ",
            price: 3.94,
          },
          {
            id: "ml-ft3",
            name: "Weekly Pass x3",
            sub: "បាន 240 💎 ភ្លាមៗ",
            bonus: "+3 ពិ",
            price: 4.47,
          },
          {
            id: "ml-ft4",
            name: "Full Ticket + 250 💎",
            sub: "250 💎 + 25 Bonus",
            bonus: "+5 ពិ",
            price: 3.75,
          },
          {
            id: "ml-wp1",
            name: "Weekly Pass x1",
            sub: "បាន 80 💎 ភ្លាមៗ",
            bonus: "+1 ពិ",
            price: 1.49,
          },
          {
            id: "ml-wp2",
            name: "Weekly Pass x2",
            sub: "បាន 160 💎 ភ្លាមៗ",
            bonus: "+2 ពិ",
            price: 2.98,
          },
        ],
      },
      {
        title: "💎 កញ្ចប់ពេជ្រទូទៅ (Diamonds)",
        packages: [
          {
            id: "ml-d1",
            name: "55 Diamonds",
            sub: "50 + 5 💎",
            bonus: "+3 ពិ",
            price: 0.85,
          },
          {
            id: "ml-d2",
            name: "86 Diamonds",
            sub: "78 + 8 💎",
            bonus: "+4 ពិ",
            price: 1.29,
          },
          {
            id: "ml-d3",
            name: "172 Diamonds",
            sub: "156 + 16 💎",
            bonus: "+5 ពិ",
            price: 2.55,
          },
          {
            id: "ml-d4",
            name: "257 Diamonds",
            sub: "234 + 23 💎",
            bonus: "+6 ពិ",
            price: 3.75,
          },
          {
            id: "ml-d5",
            name: "706 Diamonds",
            sub: "625 + 81 💎",
            bonus: "+10 ពិ",
            price: 9.99,
          },
          {
            id: "ml-d6",
            name: "2195 Diamonds",
            sub: "1860 + 335 💎",
            bonus: "+25 ពិ",
            price: 30.5,
          },
        ],
      },
    ],
  },
  {
    id: "ff",
    name: "Free Fire",
    currency: "Diamonds",
    currencyIcon: "💎",
    badge: "លក់ដាច់",
    themeColor: "#f59e0b",
    logo: "https://images.seeklogo.com/logo-png/34/1/free-fire-logo-png_seeklogo-343516.png",
    needsZone: false,
    categories: [
      {
        title: "💎 កញ្ចប់ Free Fire Diamonds",
        packages: [
          {
            id: "ff-1",
            name: "150 Diamonds",
            sub: "150 💎",
            bonus: "+2 ពិ",
            price: 1.29,
          },
          {
            id: "ff-2",
            name: "365 Diamonds",
            sub: "365 💎",
            bonus: "+5 ពិ",
            price: 2.99,
          },
          {
            id: "ff-3",
            name: "780 Diamonds",
            sub: "780 💎",
            bonus: "+10 ពិ",
            price: 4.99,
          },
          {
            id: "ff-4",
            name: "1590 Diamonds",
            sub: "1590 💎",
            bonus: "+10 ពិ",
            price: 9.99,
          },
          {
            id: "ff-5",
            name: "3270 Diamonds",
            sub: "3270 💎",
            bonus: "+30 ពិ",
            price: 19.99,
          },
          {
            id: "ff-6",
            name: "8400 Diamonds",
            sub: "8400 💎",
            bonus: "+100 ពិ",
            price: 49.99,
          },
        ],
      },
    ],
  },
  {
    id: "pubg",
    name: "PUBG Mobile",
    currency: "UC",
    currencyIcon: "💵",
    badge: "ពេញនិយម",
    themeColor: "#eab308",
    logo: "https://images.seeklogo.com/logo-png/38/2/pubg-mobile-logo-png_seeklogo-385559.png",
    needsZone: false,
    categories: [
      {
        title: "💵 កញ្ចប់ Unknown Cash (UC)",
        packages: [
          {
            id: "pubg-1",
            name: "60 UC",
            sub: "60 Unknown Cash",
            bonus: "+2 ពិ",
            price: 0.99,
          },
          {
            id: "pubg-2",
            name: "120 UC",
            sub: "120 Unknown Cash",
            bonus: "+2 ពិ",
            price: 1.98,
          },
          {
            id: "pubg-3",
            name: "325 UC",
            sub: "325 Unknown Cash",
            bonus: "+3 ពិ",
            price: 4.79,
          },
          {
            id: "pubg-4",
            name: "660 UC",
            sub: "660 Unknown Cash",
            bonus: "+5 ពិ",
            price: 9.59,
          },
          {
            id: "pubg-5",
            name: "1800 UC",
            sub: "1800 Unknown Cash",
            bonus: "+20 ពិ",
            price: 22.99,
          },
          {
            id: "pubg-6",
            name: "3850 UC",
            sub: "3850 Unknown Cash",
            bonus: "+20 ពិ",
            price: 47.0,
          },
          {
            id: "pubg-7",
            name: "8100 UC",
            sub: "8100 Unknown Cash",
            bonus: "+50 ពិ",
            price: 93.0,
          },
        ],
      },
    ],
  },
  {
    id: "hok",
    name: "Honor of Kings",
    currency: "Tokens",
    currencyIcon: "🪙",
    badge: "ថ្មី",
    themeColor: "#ec4899",
    logo: "https://images.seeklogo.com/logo-png/52/1/honor-of-kings-logo-png_seeklogo-526462.png",
    needsZone: false,
    categories: [
      {
        title: "🎟️ កញ្ចប់ Weekly Card / Plus",
        packages: [
          {
            id: "hok-w1",
            name: "Weekly Card",
            sub: "សំបុត្រប្រចាំសប្តាហ៍",
            bonus: "+2 ពិ",
            price: 1.19,
          },
          {
            id: "hok-w2",
            name: "Weekly Plus",
            sub: "សំបុត្រពិសេស Plus",
            bonus: "+3 ពិ",
            price: 3.19,
          },
        ],
      },
      {
        title: "🪙 កញ្ចប់ Honor of Kings Tokens",
        packages: [
          {
            id: "hok-t1",
            name: "16 Tokens",
            sub: "16 Tokens",
            bonus: "+1 ពិ",
            price: 0.29,
          },
          {
            id: "hok-t2",
            name: "80 Tokens",
            sub: "80 Tokens",
            bonus: "+2 ពិ",
            price: 0.99,
          },
          {
            id: "hok-t3",
            name: "160 Tokens",
            sub: "160 Tokens",
            bonus: "+2 ពិ",
            price: 1.98,
          },
          {
            id: "hok-t4",
            name: "240 Tokens",
            sub: "240 Tokens",
            bonus: "+2 ពិ",
            price: 2.79,
          },
          {
            id: "hok-t5",
            name: "400 Tokens",
            sub: "400 Tokens",
            bonus: "+3 ពិ",
            price: 4.59,
          },
          {
            id: "hok-t6",
            name: "560 Tokens",
            sub: "560 Tokens",
            bonus: "+3 ពិ",
            price: 6.49,
          },
          {
            id: "hok-t7",
            name: "830 Tokens",
            sub: "830 Tokens",
            bonus: "+5 ពិ",
            price: 9.49,
          },
          {
            id: "hok-t8",
            name: "1245 Tokens",
            sub: "1245 Tokens",
            bonus: "+10 ពិ",
            price: 13.99,
          },
        ],
      },
    ],
  },
  {
    id: "bs",
    name: "Blood Strike",
    currency: "Gold",
    currencyIcon: "🪙",
    badge: "HOT",
    themeColor: "#ef4444",
    logo: "https://images.seeklogo.com/logo-png/52/1/blood-strike-logo-png_seeklogo-526463.png",
    needsZone: false,
    categories: [
      {
        title: "🪙 កញ្ចប់ Blood Strike Gold",
        packages: [
          {
            id: "bs-1",
            name: "51 Gold",
            sub: "51 Gold",
            bonus: "+1 ពិ",
            price: 0.49,
          },
          {
            id: "bs-2",
            name: "105 Gold",
            sub: "105 Gold",
            bonus: "+2 ពិ",
            price: 0.89,
          },
          {
            id: "bs-3",
            name: "320 Gold",
            sub: "320 Gold",
            bonus: "+3 ពិ",
            price: 2.45,
          },
          {
            id: "bs-4",
            name: "540 Gold",
            sub: "540 Gold",
            bonus: "+5 ពិ",
            price: 3.99,
          },
          {
            id: "bs-5",
            name: "1100 Gold",
            sub: "1100 Gold",
            bonus: "+10 ពិ",
            price: 7.99,
          },
          {
            id: "bs-6",
            name: "2260 Gold",
            sub: "2260 Gold",
            bonus: "+25 ពិ",
            price: 15.9,
          },
          {
            id: "bs-7",
            name: "5800 Gold",
            sub: "5800 Gold",
            bonus: "+50 ពិ",
            price: 39.99,
          },
        ],
      },
    ],
  },
  {
    id: "mcgg",
    name: "Magic Chess Go Go",
    currency: "Go Go Diamonds",
    currencyIcon: "💎",
    badge: "ថ្មី",
    themeColor: "#8b5cf6",
    logo: "https://images.seeklogo.com/logo-png/38/2/mobile-legends-bang-bang-logo-png_seeklogo-385558.png",
    needsZone: true,
    categories: [
      {
        title: "♟️ កញ្ចប់ Go Go Diamonds & Pass",
        packages: [
          {
            id: "mc-1",
            name: "60 Go Go Diamonds",
            sub: "60 💎",
            bonus: "+2 ពិ",
            price: 0.99,
          },
          {
            id: "mc-2",
            name: "330 Go Go Diamonds",
            sub: "300 + 30 💎",
            bonus: "+5 ពិ",
            price: 4.89,
          },
          {
            id: "mc-3",
            name: "710 Go Go Diamonds",
            sub: "625 + 85 💎",
            bonus: "+10 ពិ",
            price: 9.89,
          },
          {
            id: "mc-4",
            name: "Season Chess Pass",
            sub: "សំបុត្រ Chess Pass",
            bonus: "+8 ពិ",
            price: 3.99,
          },
        ],
      },
    ],
  },
]);

const activeGame = ref(gamesList.value[0]);
const playerId = ref("");
const serverId = ref("");
const isChecking = ref(false);
const verifiedPlayer = ref(null);
const checkError = ref("");
const selectedPackage = ref(null);

const topupSectionRef = ref(null);
const packageSectionRef = ref(null);

const selectGame = (game) => {
  activeGame.value = game;
  verifiedPlayer.value = null;
  selectedPackage.value = null;
  checkError.value = "";
  if (topupSectionRef.value) {
    topupSectionRef.value.scrollIntoView({ behavior: "smooth" });
  }
};

const handleCheckId = async () => {
  checkError.value = "";
  verifiedPlayer.value = null;

  if (!playerId.value.trim()) {
    checkError.value = "សូមបញ្ចូល Player ID!";
    return;
  }
  if (activeGame.value.needsZone && !serverId.value.trim()) {
    checkError.value = "ហ្គេមនេះតម្រូវឱ្យបញ្ចូល Zone / Server ID!";
    return;
  }

  isChecking.value = true;
  try {
    const res = await axios.post("http://localhost/api/check-account", {
      player_id: playerId.value.trim(),
      server_id: serverId.value.trim() || "1",
      game_id: activeGame.value.id,
    });

    if (res.data && (res.data.success || res.data.valid)) {
      verifiedPlayer.value = {
        username: res.data.username,
        playerId: playerId.value.trim(),
        server: serverId.value.trim() || "Global",
      };

      await nextTick();
      if (packageSectionRef.value) {
        packageSectionRef.value.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    } else {
      checkError.value = res.data.message || "រកមិនឃើញគណនីនេះទេ!";
    }
  } catch (err) {
    checkError.value =
      err.response?.data?.message || "រកមិនឃើញគណនីនេះទេ! សូមពិនិត្យ ID ឡើងវិញ។";
  } finally {
    isChecking.value = false;
  }
};

const selectPackage = (pkg) => {
  if (!verifiedPlayer.value) {
    checkError.value = "សូមធ្វើការ Check ID ឲ្យជោគជ័យជាមុនសិន!";
    window.scrollTo({ top: 380, behavior: "smooth" });
    return;
  }
  selectedPackage.value = pkg;
};

const openTelegram = () => {
  window.open("https://t.me/Ul_xnma", "_blank");
};
</script>

<template>
  <div class="sai-app">
    <!-- Navbar -->
    <header class="navbar">
      <div class="nav-content">
        <div class="brand">
          <div class="brand-badge">⚡</div>
          <div class="brand-text">
            <h2>SAI TOP-UP</h2>
            <small>សេវាបញ្ចូលហ្គេមរហ័សទាន់ចិត្ត</small>
          </div>
        </div>
        <div class="nav-actions">
          <button class="btn-support-telegram" @click="openTelegram">
            ✈️ ទាក់ទង Telegram (@Ul_xnma)
          </button>
        </div>
      </div>
    </header>

    <!-- Multi-Game Showcase Grid -->
    <section class="game-catalog-section">
      <div class="section-badge-bar">
        <span>🎮 ជ្រើសរើសហ្គេមដែលអ្នកចង់បញ្ចូល</span>
      </div>

      <div class="game-cards-grid">
        <div
          v-for="game in gamesList"
          :key="game.id"
          class="catalog-card"
          :class="{ active: activeGame.id === game.id }"
          @click="selectGame(game)"
        >
          <div class="catalog-badge">{{ game.badge }}</div>

          <div class="game-logo-box" :style="{ borderColor: game.themeColor }">
            <img :src="game.logo" :alt="game.name" class="game-real-logo" />
          </div>

          <div class="game-title">{{ game.name }}</div>
          <div class="game-curr-tag" :style="{ color: game.themeColor }">
            {{ game.currency }}
          </div>
          <button class="btn-enter-topup">ចូល ដាក់{{ game.currency }} ➔</button>
        </div>
      </div>
    </section>

    <!-- Top-up Workspace -->
    <main ref="topupSectionRef" class="topup-container">
      <div class="active-game-header">
        <span class="dot-live"></span>
        <span
          >ហ្គេមបច្ចុប្បន្ន៖ <strong>{{ activeGame.name }}</strong> (រូបិយប័ណ្ណ៖
          <strong>{{ activeGame.currency }}</strong
          >)</span
        >
      </div>

      <div class="layout-2col">
        <!-- ផ្នែកខាងឆ្វេង -->
        <div class="form-side">
          <!-- Step 1: Check ID -->
          <div class="box-card">
            <h3 class="box-card-title">
              ១. បញ្ចូលលេខសម្គាល់គណនី (ENTER PLAYER ID)
            </h3>
            <p class="box-card-sub">
              បញ្ចូលលេខសម្គាល់ហ្គេមរបស់អ្នកដើម្បីឆែកយកឈ្មោះពិត
            </p>

            <div class="input-row">
              <div class="input-field">
                <label>Player ID (លេខសម្គាល់)</label>
                <input
                  v-model="playerId"
                  type="text"
                  placeholder="ឧ. 526036764"
                  class="dark-input"
                  @keyup.enter="handleCheckId"
                />
              </div>

              <div class="input-field" v-if="activeGame.needsZone">
                <label>Zone / Server ID</label>
                <div class="check-action-wrap">
                  <input
                    v-model="serverId"
                    type="text"
                    placeholder="ឧ. 3399"
                    class="dark-input"
                    @keyup.enter="handleCheckId"
                  />
                  <button
                    class="btn-emerald-check"
                    :disabled="isChecking"
                    @click="handleCheckId"
                  >
                    <span v-if="isChecking" class="spinner"></span>
                    <span v-else>🔍 Check ID</span>
                  </button>
                </div>
              </div>

              <div class="input-field" v-else>
                <label>ផ្ទៀងផ្ទាត់ឈ្មោះ</label>
                <button
                  class="btn-emerald-check btn-block"
                  :disabled="isChecking"
                  @click="handleCheckId"
                >
                  <span v-if="isChecking" class="spinner"></span>
                  <span v-else>🔍 ត្រួតពិនិត្យគណនី (Check ID)</span>
                </button>
              </div>
            </div>

            <div v-if="checkError" class="alert-box error">
              ⚠️ {{ checkError }}
            </div>

            <div v-if="verifiedPlayer" class="alert-box success">
              <div class="verified-wrap">
                <span class="check-circle">✓</span>
                <div>
                  <div class="v-status">ផ្ទៀងផ្ទាត់ជោគជ័យពី SERVER</div>
                  <div class="v-name">{{ verifiedPlayer.username }}</div>
                  <div class="v-meta">
                    ID: {{ verifiedPlayer.playerId }} ({{
                      verifiedPlayer.server
                    }})
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Currency Packages List with Animation -->
          <div
            ref="packageSectionRef"
            class="box-card"
            :class="{ 'card-locked': !verifiedPlayer }"
          >
            <div class="package-header-flex">
              <h3 class="box-card-title">
                ២. ជ្រើសរើសកញ្ចប់ {{ activeGame.currency }}
              </h3>
              <div class="sparkle-badge">
                <span class="sparkle-icon">✨</span> ស្វ័យប្រវត្តិ ២៤/៧
              </div>
            </div>

            <div
              v-for="(cat, idx) in activeGame.categories"
              :key="idx"
              class="category-block"
            >
              <div class="category-title-bar">
                <span>{{ cat.title }}</span>
              </div>

              <div class="packages-grid">
                <div
                  v-for="pkg in cat.packages"
                  :key="pkg.id"
                  class="pkg-item anim-hover"
                  :class="{ chosen: selectedPackage?.id === pkg.id }"
                  @click="selectPackage(pkg)"
                >
                  <div class="pkg-top-tag">{{ pkg.bonus }}</div>
                  <div class="pkg-price-tag">${{ pkg.price.toFixed(2) }}</div>
                  <div class="pkg-name-text">{{ pkg.name }}</div>
                  <div class="pkg-sub-text">{{ pkg.sub }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ផ្នែកខាងស្តាំ: Summary -->
        <aside class="summary-side">
          <div class="summary-box">
            <h3 class="sum-title">🧾 សង្ខេបការបញ្ជាទិញ</h3>

            <div class="sum-row">
              <span>ហ្គេម៖</span>
              <strong>{{ activeGame.name }}</strong>
            </div>

            <div class="sum-row">
              <span>រូបិយប័ណ្ណ៖</span>
              <strong class="emerald-text">{{ activeGame.currency }}</strong>
            </div>

            <div class="sum-row">
              <span>Player ID៖</span>
              <span>{{ verifiedPlayer?.playerId || playerId || "—" }}</span>
            </div>

            <div class="sum-row">
              <span>ឈ្មោះគណនី៖</span>
              <strong class="emerald-text">{{
                verifiedPlayer?.username || "—"
              }}</strong>
            </div>

            <div class="sum-row">
              <span>កញ្ចប់ជ្រើសរើស៖</span>
              <span>{{ selectedPackage?.name || "—" }}</span>
            </div>

            <hr class="sum-divider" />

            <div class="sum-total-row">
              <span>ទឹកប្រាក់សរុប៖</span>
              <span class="sum-total-price"
                >${{ (selectedPackage?.price || 0).toFixed(2) }}</span
              >
            </div>

            <button
              class="btn-checkout-emerald"
              :disabled="!verifiedPlayer || !selectedPackage"
            >
              បង់ប្រាក់ឥឡូវនេះ (Pay KHQR) ➔
            </button>

            <!-- Telegram Support Card -->
            <div class="telegram-support-card" @click="openTelegram">
              <div class="tg-icon">✈️</div>
              <div class="tg-info">
                <strong>ជំនួយ និងផ្ទេរប្រាក់៖ @Ul_xnma</strong>
                <p>ចុចទីនេះដើម្បីឆាតផ្ទាល់តាម Telegram</p>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </main>

    <!-- Floating Chat Button -->
    <div class="floating-chat-btn" @click="openTelegram">
      <span class="chat-icon">✈️</span>
      <span class="chat-text">Chat Telegram (@Ul_xnma)</span>
    </div>
  </div>
</template>

<style scoped>
.sai-app {
  background-color: #0b0f15;
  min-height: 100vh;
  color: #e2e8f0;
  font-family: "Kantumruy Pro", sans-serif;
  padding-bottom: 90px;
}

/* Navbar */
.navbar {
  background: #101722;
  border-bottom: 1px solid #1e293b;
  padding: 12px 20px;
}
.nav-content {
  max-width: 1140px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.brand {
  display: flex;
  align-items: center;
  gap: 10px;
}
.brand-badge {
  width: 36px;
  height: 36px;
  background: #10b981;
  color: #062e22;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}
.brand-text h2 {
  font-size: 16px;
  margin: 0;
  color: #ffffff;
}
.brand-text small {
  color: #94a3b8;
  font-size: 11px;
}
.btn-support-telegram {
  background: rgba(16, 185, 129, 0.15);
  border: 1px solid #10b981;
  color: #34d399;
  padding: 8px 16px;
  border-radius: 20px;
  cursor: pointer;
  font-family: inherit;
  font-weight: 700;
  font-size: 13px;
  transition: all 0.2s;
}
.btn-support-telegram:hover {
  background: #10b981;
  color: #042e22;
  box-shadow: 0 0 14px rgba(16, 185, 129, 0.4);
}

/* Game Catalog Grid */
.game-catalog-section {
  max-width: 1140px;
  margin: 24px auto 0;
  padding: 0 16px;
}
.section-badge-bar {
  background: #131b26;
  border-left: 4px solid #10b981;
  padding: 10px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 16px;
}
.game-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 14px;
}
.catalog-card {
  background: #131b26;
  border: 1px solid #1e293b;
  border-radius: 12px;
  padding: 14px 10px;
  text-align: center;
  cursor: pointer;
  position: relative;
  transition: all 0.25s ease;
}
.catalog-card:hover,
.catalog-card.active {
  border-color: #10b981;
  transform: translateY(-4px);
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.25);
}
.catalog-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  font-size: 10px;
  background: rgba(16, 185, 129, 0.2);
  color: #34d399;
  padding: 2px 6px;
  border-radius: 10px;
  font-weight: bold;
}

/* Real Game Logo Box */
.game-logo-box {
  width: 68px;
  height: 68px;
  margin: 6px auto;
  border-radius: 14px;
  overflow: hidden;
  border: 2px solid #233044;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
  background: #090e15;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4px;
}
.game-real-logo {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  display: block;
}

.game-title {
  font-size: 13px;
  font-weight: 700;
  color: #fff;
  margin-top: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.game-curr-tag {
  font-size: 11px;
  font-weight: bold;
  margin-bottom: 10px;
}
.btn-enter-topup {
  width: 100%;
  background: #10b981;
  color: #042e22;
  border: none;
  padding: 6px 0;
  border-radius: 6px;
  font-size: 11px;
  font-weight: bold;
  cursor: pointer;
  font-family: inherit;
}

/* Top-up Area */
.topup-container {
  max-width: 1140px;
  margin: 28px auto 0;
  padding: 0 16px;
}
.active-game-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  font-size: 14px;
  color: #94a3b8;
}
.dot-live {
  width: 8px;
  height: 8px;
  background: #10b981;
  border-radius: 50%;
  box-shadow: 0 0 8px #10b981;
}
.layout-2col {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 24px;
}
@media (max-width: 920px) {
  .layout-2col {
    grid-template-columns: 1fr;
  }
}
.box-card {
  background: #131b26;
  border: 1px solid #1e293b;
  border-radius: 12px;
  padding: 22px;
  margin-bottom: 20px;
}
.card-locked {
  opacity: 0.5;
  pointer-events: none;
}
.box-card-title {
  font-size: 15px;
  font-weight: 700;
  margin-bottom: 4px;
}
.box-card-sub {
  font-size: 12.5px;
  color: #64748b;
  margin-bottom: 16px;
}
.input-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}
@media (max-width: 600px) {
  .input-row {
    grid-template-columns: 1fr;
  }
}
.input-field label {
  display: block;
  font-size: 12.5px;
  margin-bottom: 6px;
  color: #cbd5e1;
}
.dark-input {
  width: 100%;
  background: #090e15;
  border: 1px solid #233044;
  color: #fff;
  padding: 10px 12px;
  border-radius: 7px;
  font-size: 13.5px;
  outline: none;
  font-family: inherit;
}
.dark-input:focus {
  border-color: #10b981;
}
.check-action-wrap {
  display: flex;
  gap: 8px;
}
.btn-emerald-check {
  background: #10b981;
  color: #042e22;
  font-weight: 700;
  border: none;
  padding: 0 14px;
  border-radius: 7px;
  cursor: pointer;
  white-space: nowrap;
  font-size: 12.5px;
  font-family: inherit;
}
.btn-block {
  width: 100%;
  height: 40px;
}

/* Alerts */
.alert-box {
  margin-top: 14px;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13px;
}
.alert-box.error {
  background: rgba(239, 68, 68, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.4);
  color: #fca5a5;
}
.alert-box.success {
  background: rgba(16, 185, 129, 0.12);
  border: 1px solid rgba(16, 185, 129, 0.4);
}
.verified-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
}
.check-circle {
  width: 26px;
  height: 26px;
  background: #10b981;
  color: #042e22;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}
.v-status {
  font-size: 11px;
  color: #34d399;
  font-weight: 700;
}
.v-name {
  font-size: 15px;
  font-weight: 800;
  color: #fff;
}
.v-meta {
  font-size: 11.5px;
  color: #94a3b8;
}

/* Animations */
.package-header-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.sparkle-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(16, 185, 129, 0.12);
  border: 1px solid #10b981;
  color: #34d399;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  animation: pulse-glow 2s infinite ease-in-out;
}
@keyframes pulse-glow {
  0%,
  100% {
    box-shadow: 0 0 4px rgba(16, 185, 129, 0.3);
  }
  50% {
    box-shadow: 0 0 16px rgba(16, 185, 129, 0.7);
  }
}

.category-block {
  margin-bottom: 20px;
}
.category-title-bar {
  background: #090e15;
  border-left: 3px solid #10b981;
  padding: 8px 12px;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 700;
  margin-bottom: 12px;
  color: #f1f5f9;
}
.packages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(135px, 1fr));
  gap: 12px;
}
.pkg-item {
  background: #090e15;
  border: 1px solid #1e293b;
  border-radius: 10px;
  padding: 12px 8px;
  text-align: center;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}
.anim-hover:hover {
  border-color: #10b981;
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 6px 18px rgba(16, 185, 129, 0.2);
}
.pkg-item.chosen {
  border-color: #10b981;
  background: rgba(16, 185, 129, 0.12);
  box-shadow: 0 0 20px rgba(16, 185, 129, 0.35);
}
.pkg-top-tag {
  font-size: 10px;
  color: #34d399;
  font-weight: 700;
  margin-bottom: 2px;
}
.pkg-price-tag {
  font-size: 16px;
  font-weight: 800;
  color: #fff;
  margin-bottom: 4px;
}
.pkg-name-text {
  font-size: 12px;
  font-weight: 600;
  color: #cbd5e1;
}
.pkg-sub-text {
  font-size: 10.5px;
  color: #64748b;
}

/* Right Summary */
.summary-box {
  background: #131b26;
  border: 1px solid #1e293b;
  border-radius: 12px;
  padding: 20px;
  position: sticky;
  top: 20px;
}
.sum-title {
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 16px;
}
.sum-row {
  display: flex;
  justify-content: space-between;
  font-size: 12.5px;
  margin-bottom: 10px;
  color: #94a3b8;
}
.sum-row strong,
.sum-row span:last-child {
  color: #f1f5f9;
}
.emerald-text {
  color: #34d399 !important;
}
.sum-divider {
  border: none;
  border-top: 1px solid #1e293b;
  margin: 14px 0;
}
.sum-total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  font-size: 14px;
}
.sum-total-price {
  font-size: 24px;
  font-weight: 800;
  color: #34d399;
}
.btn-checkout-emerald {
  width: 100%;
  background: #10b981;
  color: #042e22;
  border: none;
  padding: 12px;
  border-radius: 7px;
  font-weight: 800;
  font-size: 13.5px;
  cursor: pointer;
  font-family: inherit;
  transition: opacity 0.2s;
}
.btn-checkout-emerald:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* Telegram Support Card */
.telegram-support-card {
  margin-top: 16px;
  background: rgba(16, 185, 129, 0.08);
  border: 1px dashed #10b981;
  border-radius: 8px;
  padding: 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  transition: all 0.2s;
}
.telegram-support-card:hover {
  background: rgba(16, 185, 129, 0.16);
  transform: translateY(-2px);
}
.tg-icon {
  font-size: 22px;
}
.tg-info strong {
  font-size: 12px;
  display: block;
  color: #34d399;
}
.tg-info p {
  font-size: 11px;
  color: #94a3b8;
  margin: 0;
}

/* Floating Chat */
.floating-chat-btn {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #10b981;
  color: #042e22;
  padding: 10px 18px;
  border-radius: 30px;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  box-shadow: 0 4px 20px rgba(16, 185, 129, 0.45);
  font-weight: 800;
  font-size: 13px;
  z-index: 1000;
  transition: transform 0.2s;
}
.floating-chat-btn:hover {
  transform: scale(1.06);
}

.spinner {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(0, 0, 0, 0.2);
  border-top-color: #000;
  border-radius: 50%;
  display: inline-block;
  animation: spin 0.8s linear infinite;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
