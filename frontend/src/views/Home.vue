<script setup>
import { ref, computed, nextTick, onBeforeUnmount, onMounted } from "vue";
import QRCode from "qrcode";
import axios from "axios";

// ===== 1. Dark / Light Mode System =====
const isDark = ref(true);

const toggleDarkMode = () => {
  isDark.value = !isDark.value;
  localStorage.setItem("theme_mode", isDark.value ? "dark" : "light");
};

// ===== 2. 7-Color Theme System =====
const themes = [
  { id: "emerald", name: "Emerald", primary: "#10b981", light: "#34d399", glow: "rgba(16, 185, 129, 0.45)" },
  { id: "cyan",    name: "Cyan",    primary: "#06b6d4", light: "#22d3ee", glow: "rgba(6, 182, 212, 0.45)" },
  { id: "blue",    name: "Blue",    primary: "#2563eb", light: "#60a5fa", glow: "rgba(37, 99, 235, 0.45)" },
  { id: "violet",  name: "Violet",  primary: "#8b5cf6", light: "#a78bfa", glow: "rgba(139, 92, 246, 0.45)" },
  { id: "pink",    name: "Pink",    primary: "#ec4899", light: "#f472b6", glow: "rgba(236, 72, 153, 0.45)" },
  { id: "rose",    name: "Rose",    primary: "#f43f5e", light: "#fb7185", glow: "rgba(244, 63, 94, 0.45)" },
  { id: "amber",   name: "Amber",   primary: "#f59e0b", light: "#fbbf24", glow: "rgba(245, 158, 11, 0.45)" },
];

const currentTheme = ref(themes[0]);
const showThemePicker = ref(false);

const applyTheme = (theme) => {
  currentTheme.value = theme;
  document.documentElement.style.setProperty("--primary-color", theme.primary);
  document.documentElement.style.setProperty("--primary-light", theme.light);
  document.documentElement.style.setProperty("--primary-glow", theme.glow);
  localStorage.setItem("user_theme", theme.id);
};

onMounted(() => {
  const savedMode = localStorage.getItem("theme_mode");
  if (savedMode) {
    isDark.value = savedMode === "dark";
  } else if (window.matchMedia && window.matchMedia("(prefers-color-scheme: light)").matches) {
    isDark.value = false;
  }

  const savedThemeId = localStorage.getItem("user_theme");
  if (savedThemeId) {
    const found = themes.find((t) => t.id === savedThemeId);
    if (found) applyTheme(found);
  } else {
    applyTheme(themes[0]);
  }
});

// Games list
const gamesList = ref([
  {
    id: "mlbb",
    name: "Mobile Legends",
    currency: "Diamonds",
    currencyIcon: "💎",
    badge: "ពេញនិយម",
    themeColor: "#2563eb",
    logo: "/images/games/mlbb.png",
    needsZone: true,
    categories: [
      {
        title: "🎟️ កញ្ចប់ Full Ticket & Pass",
        packages: [
          { id: "ml-ft1", name: "Full Ticket + Weekly x2", sub: "55 💎 + Weekly x2", bonus: "+5 ពិ", price: 3.83 },
          { id: "ml-ft2", name: "Full Ticket + Weekly x1", sub: "165 💎 + Weekly x1", bonus: "+5 ពិ", price: 3.94 },
          { id: "ml-ft3", name: "Weekly Pass x3", sub: "បាន 240 💎 ភ្លាមៗ", bonus: "+3 ពិ", price: 4.47 },
          { id: "ml-ft4", name: "Full Ticket + 250 💎", sub: "250 💎 + 25 Bonus", bonus: "+5 ពិ", price: 3.75 },
          { id: "ml-wp1", name: "Weekly Pass x1", sub: "បាន 80 💎 ភ្លាមៗ", bonus: "+1 ពិ", price: 1.49 },
          { id: "ml-wp2", name: "Weekly Pass x2", sub: "បាន 160 💎 ភ្លាមៗ", bonus: "+2 ពិ", price: 2.98 },
        ],
      },
      {
        title: "💎 កញ្ចប់ពេជ្រទូទៅ (Diamonds)",
        packages: [
          { id: "ml-d1", name: "55 Diamonds", sub: "50 + 5 💎", bonus: "+3 ពិ", price: 0.85 },
          { id: "ml-d2", name: "86 Diamonds", sub: "78 + 8 💎", bonus: "+4 ពិ", price: 1.29 },
          { id: "ml-d3", name: "172 Diamonds", sub: "156 + 16 💎", bonus: "+5 ពិ", price: 2.55 },
          { id: "ml-d4", name: "257 Diamonds", sub: "234 + 23 💎", bonus: "+6 ពិ", price: 3.75 },
          { id: "ml-d5", name: "706 Diamonds", sub: "625 + 81 💎", bonus: "+10 ពិ", price: 9.99 },
          { id: "ml-d6", name: "2195 Diamonds", sub: "1860 + 335 💎", bonus: "+25 ពិ", price: 30.5 },
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
    logo: "/images/games/freefire.png",
    needsZone: false,
    categories: [
      {
        title: "💎 កញ្ចប់ Free Fire Diamonds",
        packages: [
          { id: "ff-1", name: "150 Diamonds", sub: "150 💎", bonus: "+2 ពិ", price: 1.29 },
          { id: "ff-2", name: "365 Diamonds", sub: "365 💎", bonus: "+5 ពិ", price: 2.99 },
          { id: "ff-3", name: "780 Diamonds", sub: "780 💎", bonus: "+10 ពិ", price: 4.99 },
          { id: "ff-4", name: "1590 Diamonds", sub: "1590 💎", bonus: "+10 ពិ", price: 9.99 },
          { id: "ff-5", name: "3270 Diamonds", sub: "3270 💎", bonus: "+30 ពិ", price: 19.99 },
          { id: "ff-6", name: "8400 Diamonds", sub: "8400 💎", bonus: "+100 ពិ", price: 49.99 },
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
    logo: "/images/games/hok.png",
    needsZone: false,
    categories: [
      {
        title: "🎟️ កញ្ចប់ Weekly Card / Plus",
        packages: [
          { id: "hok-w1", name: "Weekly Card", sub: "សំបុត្រប្រចាំសប្តាហ៍", bonus: "+2 ពិ", price: 1.19 },
          { id: "hok-w2", name: "Weekly Plus", sub: "សំបុត្រពិសេស Plus", bonus: "+3 ពិ", price: 3.19 },
        ],
      },
      {
        title: "🪙 កញ្ចប់ Honor of Kings Tokens",
        packages: [
          { id: "hok-t1", name: "16 Tokens", sub: "16 Tokens", bonus: "+1 ពិ", price: 0.29 },
          { id: "hok-t2", name: "80 Tokens", sub: "80 Tokens", bonus: "+2 ពិ", price: 0.99 },
          { id: "hok-t3", name: "160 Tokens", sub: "160 Tokens", bonus: "+2 ពិ", price: 1.98 },
          { id: "hok-t4", name: "240 Tokens", sub: "240 Tokens", bonus: "+2 ពិ", price: 2.79 },
          { id: "hok-t5", name: "400 Tokens", sub: "400 Tokens", bonus: "+3 ពិ", price: 4.59 },
          { id: "hok-t6", name: "560 Tokens", sub: "560 Tokens", bonus: "+3 ពិ", price: 6.49 },
          { id: "hok-t7", name: "830 Tokens", sub: "830 Tokens", bonus: "+5 ពិ", price: 9.49 },
          { id: "hok-t8", name: "1245 Tokens", sub: "1245 Tokens", bonus: "+10 ពិ", price: 13.99 },
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

// ===== Payment (Bakong KHQR) =====
const isOrdering = ref(false);
const showPayModal = ref(false);
const currentOrder = ref(null);
const qrImage = ref("");
const payStatus = ref("pending");
const secondsLeft = ref(0);
const orderError = ref("");
const copied = ref(false);
let pollTimer = null;
let tickTimer = null;

const timeLeft = computed(() => {
  const m = String(Math.floor(secondsLeft.value / 60)).padStart(2, "0");
  const s = String(secondsLeft.value % 60).padStart(2, "0");
  return `${m}:${s}`;
});

const stopTimers = () => {
  clearInterval(pollTimer);
  clearInterval(tickTimer);
  pollTimer = tickTimer = null;
};

const closePayModal = () => {
  stopTimers();
  showPayModal.value = false;
};

const pollStatus = async () => {
  if (!currentOrder.value) return;
  try {
    const res = await axios.get(
      `http://localhost/api/orders/${currentOrder.value.order_code}/status`
    );
    const st = res.data.status;
    if (st === "paid" || st === "completed") {
      payStatus.value = "paid";
      stopTimers();
    } else if (st === "expired" || st === "cancelled") {
      payStatus.value = "expired";
      stopTimers();
    }
  } catch (e) {}
};

const handlePay = async () => {
  if (!verifiedPlayer.value || !selectedPackage.value) return;
  orderError.value = "";
  isOrdering.value = true;
  try {
    const res = await axios.post("http://localhost/api/orders", {
      game_id: activeGame.value.id,
      player_id: verifiedPlayer.value.playerId,
      server_id: serverId.value.trim() || null,
      username: verifiedPlayer.value.username,
      package_name: selectedPackage.value.name,
      price: selectedPackage.value.price,
    });
    const o = res.data.data;
    currentOrder.value = o;
    const svg = await QRCode.toString(o.qr, { type: "svg", margin: 4, errorCorrectionLevel: "M" });
    qrImage.value = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(svg);
    payStatus.value = "pending";
    secondsLeft.value = o.expires_in;
    showPayModal.value = true;

    stopTimers();
    pollTimer = setInterval(pollStatus, 3000);
    tickTimer = setInterval(() => {
      if (secondsLeft.value > 0) secondsLeft.value--;
      if (secondsLeft.value === 0 && payStatus.value === "pending") {
        pollStatus();
        stopTimers();
        setTimeout(() => {
          if (payStatus.value === "pending") payStatus.value = "expired";
        }, 2500);
      }
    }, 1000);
  } catch (err) {
    orderError.value =
      err.response?.data?.message || "បង្កើតការបញ្ជាទិញមិនបាន! សូមព្យាយាមម្តងទៀត។";
  } finally {
    isOrdering.value = false;
  }
};

const copyOrderCode = async () => {
  try {
    await navigator.clipboard.writeText(currentOrder.value.order_code);
    copied.value = true;
    setTimeout(() => (copied.value = false), 1500);
  } catch (e) {}
};

const sendOrderTelegram = () => {
  const o = currentOrder.value;
  const msg =
    `Order: ${o.order_code}\nហ្គេម: ${activeGame.value.name}\n` +
    `Player ID: ${o.player_id}${o.server_id ? " (" + o.server_id + ")" : ""}\n` +
    `កញ្ចប់: ${o.package_name}\nតម្លៃ: $${Number(o.price).toFixed(2)}`;
  window.open("https://t.me/Ul_xnma?text=" + encodeURIComponent(msg), "_blank");
};

onBeforeUnmount(stopTimers);
</script>

<template>
  <div class="sai-app" :class="{ 'light-theme': !isDark }">
    <!-- Navbar -->
    <header class="navbar">
      <div class="nav-content">
        <div class="brand">
          <img src="/images/ml/logo.png" alt="Logo" class="brand-logo" />
          <div class="brand-text">
            <h2>SAI TOP-UP</h2>
            <small>សេវាបញ្ចូលហ្គេមរហ័សទាន់ចិត្ត</small>
          </div>
        </div>

        <div class="nav-actions">
          <!-- Dark / Light Mode Toggle Button -->
          <div
            class="theme-toggle-switch"
            :class="{ 'is-dark': isDark }"
            @click="toggleDarkMode"
            title="ប្ដូរ Dark/Light Mode"
          >
            <span class="switch-icon sun">☀️</span>
            <span class="switch-icon moon">🌙</span>
            <div class="switch-slider"></div>
          </div>

          <!-- 7-Color Theme Bar -->
          <div class="theme-switch-container">
            <button class="theme-toggle-btn" @click="showThemePicker = !showThemePicker" title="ប្ដូរពណ៌ Theme">
              🎨 <span class="theme-label-desktop">Theme</span>
            </button>
            <div v-if="showThemePicker" class="theme-palette-dropdown">
              <span
                v-for="t in themes"
                :key="t.id"
                class="theme-dot"
                :style="{ backgroundColor: t.primary }"
                :class="{ active: currentTheme.id === t.id }"
                :title="t.name"
                @click="applyTheme(t)"
              ></span>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Hero Banner -->
    <section class="hero-wrap">
      <div class="hero-banner">
        <video
          class="hero-video"
          src="/videos/ml-hero.mp4"
          autoplay
          muted
          loop
          playsinline
          preload="metadata"
        ></video>
        <div class="hero-overlay"></div>

        <div class="hero-content">
          <div class="hero-logo-wrap">
            <div class="ring ring-1"></div>
            <div class="ring ring-2"></div>
            <img src="/images/ml/logo.png" alt="Mobile Legends" class="hero-logo" />
          </div>

          <div class="hero-text">
            <small class="hero-tag">OFFICIAL MLBB DIRECT TOP-UP</small>
            <h1>MOBILE LEGENDS <span>DIAMONDS</span></h1>
            <p>បញ្ចូល Diamonds ភ្លាមៗ ២៤/៧ ទទួលបានប្រាក់រង្វាន់បន្ថែម</p>
            <button class="btn-hero" @click="selectGame(gamesList[0])">
              💎 បញ្ចូលឥឡូវនេះ ➔
            </button>
          </div>
        </div>
      </div>
    </section>

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
        <span>ហ្គេមបច្ចុប្បន្ន៖ <strong>{{ activeGame.name }}</strong> ({{ activeGame.currency }})</span>
      </div>

      <div class="layout-2col">
        <!-- Left column -->
        <div class="form-side">
          <!-- Step 1: Check ID -->
          <div class="box-card">
            <h3 class="box-card-title">១. បញ្ចូលលេខសម្គាល់គណនី (ENTER PLAYER ID)</h3>
            <p class="box-card-sub">បញ្ចូលលេខសម្គាល់ហ្គេមរបស់អ្នកដើម្បីឆែកយកឈ្មោះពិត</p>

            <div class="input-row">
              <div class="input-field">
                <label>Player ID (លេខសម្គាល់)</label>
                <input
                  v-model="playerId"
                  type="text"
                  placeholder="ឧ. 626036764"
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
                    placeholder="ឧ. 3459"
                    class="dark-input"
                    @keyup.enter="handleCheckId"
                  />
                  <button class="btn-primary-action" :disabled="isChecking" @click="handleCheckId">
                    <span v-if="isChecking" class="spinner"></span>
                    <span v-else>🔍 Check</span>
                  </button>
                </div>
              </div>

              <div class="input-field full-btn-wrap" v-else>
                <label class="hide-mobile">ផ្ទៀងផ្ទាត់ឈ្មោះ</label>
                <button class="btn-primary-action btn-block" :disabled="isChecking" @click="handleCheckId">
                  <span v-if="isChecking" class="spinner"></span>
                  <span v-else>🔍 ត្រួតពិនិត្យគណនី (Check ID)</span>
                </button>
              </div>
            </div>

            <div v-if="checkError" class="alert-box error">⚠️ {{ checkError }}</div>

            <div v-if="verifiedPlayer" class="alert-box success">
              <div class="verified-wrap">
                <span class="check-circle">✓</span>
                <div>
                  <div class="v-status">ផ្ទៀងផ្ទាត់ជោគជ័យពី SERVER</div>
                  <div class="v-name">{{ verifiedPlayer.username }}</div>
                  <div class="v-meta">ID: {{ verifiedPlayer.playerId }} ({{ verifiedPlayer.server }})</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Currency Packages List -->
          <div
            ref="packageSectionRef"
            class="box-card"
            :class="{ 'card-locked': !verifiedPlayer }"
          >
            <div class="package-header-flex">
              <h3 class="box-card-title">២. ជ្រើសរើសកញ្ចប់ {{ activeGame.currency }}</h3>
              <div class="sparkle-badge"><span class="sparkle-icon">✨</span> ២៤/៧</div>
            </div>

            <div v-for="(cat, idx) in activeGame.categories" :key="idx" class="category-block">
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

        <!-- Right column (Desktop Summary) -->
        <aside class="summary-side">
          <div class="summary-box">
            <h3 class="sum-title">🧾 សង្ខេបការបញ្ជាទិញ</h3>
            <div class="sum-row">
              <span>ហ្គេម៖</span>
              <strong>{{ activeGame.name }}</strong>
            </div>
            <div class="sum-row">
              <span>រូបិយប័ណ្ណ៖</span>
              <strong class="primary-text">{{ activeGame.currency }}</strong>
            </div>
            <div class="sum-row">
              <span>Player ID៖</span>
              <span>{{ verifiedPlayer?.playerId || playerId || "—" }}</span>
            </div>
            <div class="sum-row">
              <span>ឈ្មោះគណនី៖</span>
              <strong class="primary-text">{{ verifiedPlayer?.username || "—" }}</strong>
            </div>
            <div class="sum-row">
              <span>កញ្ចប់ជ្រើសរើស៖</span>
              <span>{{ selectedPackage?.name || "—" }}</span>
            </div>

            <hr class="sum-divider" />

            <div class="sum-total-row">
              <span>ទឹកប្រាក់សរុប៖</span>
              <span class="sum-total-price">${{ (selectedPackage?.price || 0).toFixed(2) }}</span>
            </div>

            <button
              class="btn-checkout-primary"
              :disabled="!verifiedPlayer || !selectedPackage || isOrdering"
              @click="handlePay"
            >
              <span v-if="isOrdering" class="spinner"></span>
              <span v-else>បង់ប្រាក់ឥឡូវនេះ (Pay KHQR) ➔</span>
            </button>
            <div v-if="orderError" class="alert-box error">⚠️ {{ orderError }}</div>

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

    <!-- Mobile Sticky Checkout Bottom Bar -->
    <div class="mobile-sticky-bar" v-if="selectedPackage">
      <div class="mobile-bar-info">
        <span class="m-pkg-name">{{ selectedPackage.name }}</span>
        <span class="m-pkg-price">${{ selectedPackage.price.toFixed(2) }}</span>
      </div>
      <button
        class="btn-mobile-pay"
        :disabled="!verifiedPlayer || isOrdering"
        @click="handlePay"
      >
        <span v-if="isOrdering" class="spinner"></span>
        <span v-else>បង់ KHQR ➔</span>
      </button>
    </div>

    <!-- Payment Modal (Bakong KHQR) -->
    <div v-if="showPayModal && currentOrder" class="pay-overlay" @click.self="closePayModal">
      <div class="pay-modal">
        <button class="pay-close" @click="closePayModal">✕</button>

        <template v-if="payStatus === 'paid'">
          <div class="pay-big-icon">✅</div>
          <h3 class="pay-title">បង់ប្រាក់ជោគជ័យ!</h3>
          <p class="pay-note-lg">
            យើងបានទទួលប្រាក់ ${{ Number(currentOrder.price).toFixed(2) }} ហើយ។ Diamonds នឹងត្រូវបញ្ចូលក្នុងពេលឆាប់ៗ។
          </p>
          <div class="pay-code" @click="copyOrderCode">
            <small>លេខ Order</small>
            <strong>{{ currentOrder.order_code }}</strong>
            <span>{{ copied ? "✓ បានចម្លង" : "ចុចដើម្បីចម្លង" }}</span>
          </div>
          <button class="btn-checkout-primary pay-gap" @click="sendOrderTelegram">
            ✈️ ទាក់ទង Telegram ប្រសិនបើយឺត
          </button>
        </template>

        <template v-else-if="payStatus === 'expired'">
          <div class="pay-big-icon">⏰</div>
          <h3 class="pay-title">QR ផុតកំណត់ហើយ</h3>
          <p class="pay-note-lg">
            បើអ្នកបានបង់ប្រាក់រួចហើយ សូមទាក់ទង Telegram ជាមួយលេខ Order <b>{{ currentOrder.order_code }}</b>។
          </p>
          <button class="btn-checkout-primary pay-gap" @click="sendOrderTelegram">
            ✈️ ទាក់ទង Telegram
          </button>
        </template>

        <template v-else>
          <h3 class="pay-title">💳 ស្កេន KHQR ដើម្បីបង់ប្រាក់</h3>
          <div class="pay-amount">${{ Number(currentOrder.price).toFixed(2) }}</div>
          <img :src="qrImage" alt="KHQR" class="pay-qr" />
          <div class="pay-timer">⏳ QR ផុតកំណត់ក្នុង {{ timeLeft }}</div>
          <div class="pay-waiting"><span class="spinner pay-spin"></span> កំពុងរង់ចាំការបង់ប្រាក់...</div>
          <p class="pay-note">
            គាំទ្រកម្មវិធីធនាគារ KHQR ទាំងអស់ (ABA, Wing, ACLEDA...)។
          </p>
          <div class="pay-code pay-gap" @click="copyOrderCode">
            <small>លេខ Order</small>
            <strong>{{ currentOrder.order_code }}</strong>
            <span>{{ copied ? "✓ បានចម្លង" : "ចុចដើម្បីចម្លង" }}</span>
          </div>
        </template>
      </div>
    </div>

    <!-- Floating Chat Button -->
    <div class="floating-chat-btn" @click="openTelegram">
      <span class="chat-icon">✈️</span>
      <span class="chat-text">Chat Support</span>
    </div>
  </div>
</template>

<style>
/* CSS Variables សម្រាប់ 7 ពណ៌ */
:root {
  --primary-color: #10b981;
  --primary-light: #34d399;
  --primary-glow: rgba(16, 185, 129, 0.45);
}
</style>

<style scoped>
*, *::before, *::after {
  box-sizing: border-box;
}

/* Base Styles (Dark Mode ជា default) */
.sai-app {
  background-color: #0b0f15;
  min-height: 100vh;
  width: 100%;
  max-width: 100vw;
  overflow-x: hidden;
  color: #e2e8f0;
  font-family: "Kantumruy Pro", sans-serif;
  padding-bottom: 110px;
  transition: background-color 0.3s ease, color 0.3s ease;
}

/* Navbar */
.navbar {
  background: #101722;
  border-bottom: 1px solid #1e293b;
  padding: 10px 16px;
  position: sticky;
  top: 0;
  z-index: 100;
  width: 100%;
  transition: all 0.3s ease;
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
.brand-logo {
  height: 36px;
  width: auto;
  object-fit: contain;
  filter: drop-shadow(0 0 8px var(--primary-glow));
}
.brand-text h2 {
  font-size: 15px;
  margin: 0;
  color: #ffffff;
}
.brand-text small {
  color: #94a3b8;
  font-size: 10.5px;
  display: block;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* ===== Toggle Switch (Dark / Light Mode) ===== */
.theme-toggle-switch {
  position: relative;
  width: 58px;
  height: 28px;
  background: #cbd5e1;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 6px;
  cursor: pointer;
  border: 1px solid #94a3b8;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  user-select: none;
}
.switch-icon {
  font-size: 13px;
  z-index: 2;
  line-height: 1;
}
.switch-slider {
  position: absolute;
  top: 2px;
  left: 3px;
  width: 22px;
  height: 22px;
  background: #ffffff;
  border-radius: 50%;
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.3s ease;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
  z-index: 1;
}
.theme-toggle-switch.is-dark {
  background: #090e15;
  border-color: #233044;
}
.theme-toggle-switch.is-dark .switch-slider {
  transform: translateX(28px);
  background: #1e293b;
  border: 1px solid var(--primary-color);
}

/* 7 Colors Palette */
.theme-switch-container {
  position: relative;
}
.theme-toggle-btn {
  background: #1e293b;
  border: 1px solid var(--primary-color);
  color: #fff;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
  font-family: inherit;
}
.theme-palette-dropdown {
  position: absolute;
  right: 0;
  top: calc(100% + 8px);
  background: #131b26;
  border: 1px solid #334155;
  border-radius: 12px;
  padding: 8px 10px;
  display: flex;
  gap: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6);
  z-index: 1000;
}
.theme-dot {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  cursor: pointer;
  transition: transform 0.2s;
  border: 2px solid transparent;
}
.theme-dot:hover { transform: scale(1.2); }
.theme-dot.active { border-color: #fff; transform: scale(1.15); }

/* Hero Banner */
.hero-wrap {
  width: 100%;
  max-width: 1140px;
  margin: 16px auto 0;
  padding: 0 12px;
  overflow: hidden;
}
.hero-banner {
  position: relative;
  overflow: hidden;
  border-radius: 16px;
  border: 1px solid #1e293b;
  min-height: 240px;
  display: flex;
  align-items: center;
  background: #090e15;
}
.hero-video {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 0;
}
.hero-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
  background: linear-gradient(
    90deg,
    rgba(11, 15, 21, 0.94),
    rgba(11, 15, 21, 0.75) 60%,
    rgba(11, 15, 21, 0.5)
  );
}
.hero-content {
  position: relative;
  z-index: 3;
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 20px;
  width: 100%;
}
.hero-logo-wrap {
  position: relative;
  width: 140px;
  height: 140px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}
.hero-logo {
  position: relative;
  z-index: 2;
  width: 120px;
  object-fit: contain;
  filter: drop-shadow(0 0 18px var(--primary-glow));
}
.ring {
  position: absolute;
  border-radius: 50%;
  border: 2px dashed var(--primary-light);
}
.ring-1 { inset: 0; animation: heroSpin 16s linear infinite; }
.ring-2 { inset: 14px; border-color: rgba(255, 255, 255, 0.2); animation: heroSpin 24s linear infinite reverse; }

.hero-text {
  width: 100%;
  min-width: 0;
  word-break: break-word;
}
.hero-tag {
  color: var(--primary-light);
  letter-spacing: 1.5px;
  font-weight: 700;
  font-size: 11px;
}
.hero-text h1 {
  font-size: clamp(20px, 4vw, 36px);
  line-height: 1.2;
  margin: 4px 0 8px;
  color: #fff;
  font-weight: 900;
}
.hero-text h1 span { color: var(--primary-light); }
.hero-text p {
  color: #cbd5e1;
  font-size: 12.5px;
  margin-bottom: 12px;
}
.btn-hero {
  background: var(--primary-color);
  color: #042e22;
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  font-weight: 800;
  font-size: 13px;
  cursor: pointer;
  font-family: inherit;
}

/* Game Catalog Grid */
.game-catalog-section {
  width: 100%;
  max-width: 1140px;
  margin: 18px auto 0;
  padding: 0 12px;
  overflow: hidden;
}
.section-badge-bar {
  background: #131b26;
  border-left: 4px solid var(--primary-color);
  padding: 8px 14px;
  border-radius: 6px;
  font-size: 13.5px;
  font-weight: 700;
  margin-bottom: 12px;
  transition: all 0.3s ease;
}
.game-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
  gap: 10px;
}
.catalog-card {
  background: #131b26;
  border: 1px solid #1e293b;
  border-radius: 12px;
  padding: 12px 8px;
  text-align: center;
  cursor: pointer;
  position: relative;
  transition: all 0.25s ease;
}
.catalog-card:hover,
.catalog-card.active {
  border-color: var(--primary-color);
  transform: translateY(-2px);
  box-shadow: 0 4px 14px var(--primary-glow);
}
.catalog-badge {
  position: absolute;
  top: 6px;
  right: 6px;
  font-size: 9px;
  background: rgba(255, 255, 255, 0.08);
  color: var(--primary-light);
  padding: 2px 5px;
  border-radius: 8px;
  font-weight: bold;
}
.game-logo-box {
  width: 56px;
  height: 56px;
  margin: 4px auto;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid #233044;
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
}
.game-title {
  font-size: 12px;
  font-weight: 700;
  color: #fff;
  margin-top: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.game-curr-tag {
  font-size: 10.5px;
  font-weight: bold;
  margin-bottom: 8px;
}
.btn-enter-topup {
  width: 100%;
  background: var(--primary-color);
  color: #042e22;
  border: none;
  padding: 5px 0;
  border-radius: 6px;
  font-size: 10.5px;
  font-weight: bold;
  cursor: pointer;
}

/* Top-up Area */
.topup-container {
  max-width: 1140px;
  margin: 20px auto 0;
  padding: 0 12px;
}
.active-game-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 14px;
  font-size: 13.5px;
  color: #94a3b8;
}
.dot-live {
  width: 8px;
  height: 8px;
  background: var(--primary-color);
  border-radius: 50%;
  box-shadow: 0 0 8px var(--primary-color);
}
.layout-2col {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 20px;
}

.box-card {
  background: #131b26;
  border: 1px solid #1e293b;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px;
  transition: all 0.3s ease;
}
.card-locked {
  opacity: 0.5;
  pointer-events: none;
}
.box-card-title {
  font-size: 14.5px;
  font-weight: 700;
  margin-bottom: 4px;
}
.box-card-sub {
  font-size: 12px;
  color: #64748b;
  margin-bottom: 14px;
}
.input-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.input-field label {
  display: block;
  font-size: 12px;
  margin-bottom: 5px;
  color: #cbd5e1;
}
.dark-input {
  width: 100%;
  background: #090e15;
  border: 1px solid #233044;
  color: #fff;
  padding: 9px 12px;
  border-radius: 7px;
  font-size: 13px;
  outline: none;
  transition: all 0.3s ease;
}
.dark-input:focus {
  border-color: var(--primary-color);
}
.check-action-wrap {
  display: flex;
  gap: 8px;
}
.btn-primary-action {
  background: var(--primary-color);
  color: #042e22;
  font-weight: 700;
  border: none;
  padding: 0 12px;
  border-radius: 7px;
  cursor: pointer;
  font-size: 12px;
}
.btn-block {
  width: 100%;
  height: 38px;
}

/* Alerts */
.alert-box {
  margin-top: 12px;
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 12.5px;
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
  width: 24px;
  height: 24px;
  background: var(--primary-color);
  color: #042e22;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}
.v-status { font-size: 10.5px; color: var(--primary-light); font-weight: 700; }
.v-name { font-size: 14px; font-weight: 800; color: #fff; }
.v-meta { font-size: 11px; color: #94a3b8; }

/* Packages Grid */
.package-header-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}
.sparkle-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--primary-color);
  color: var(--primary-light);
  padding: 3px 8px;
  border-radius: 16px;
  font-size: 11px;
}
.category-block { margin-bottom: 16px; }
.category-title-bar {
  background: #090e15;
  border-left: 3px solid var(--primary-color);
  padding: 6px 10px;
  border-radius: 4px;
  font-size: 12.5px;
  font-weight: 700;
  margin-bottom: 10px;
  color: #f1f5f9;
  transition: all 0.3s ease;
}
.packages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(115px, 1fr));
  gap: 8px;
}
.pkg-item {
  background: #090e15;
  border: 1px solid #1e293b;
  border-radius: 8px;
  padding: 10px 6px;
  text-align: center;
  cursor: pointer;
  position: relative;
  transition: all 0.2s ease;
}
.anim-hover:hover {
  border-color: var(--primary-color);
  transform: translateY(-2px);
}
.pkg-item.chosen {
  border-color: var(--primary-color);
  background: rgba(255, 255, 255, 0.06);
  box-shadow: 0 0 12px var(--primary-glow);
}
.pkg-top-tag { font-size: 9.5px; color: var(--primary-light); font-weight: 700; margin-bottom: 2px; }
.pkg-price-tag { font-size: 15px; font-weight: 800; color: #fff; margin-bottom: 2px; }
.pkg-name-text { font-size: 11px; font-weight: 600; color: #cbd5e1; }
.pkg-sub-text { font-size: 10px; color: #64748b; }

/* Desktop Summary */
.summary-box {
  background: #131b26;
  border: 1px solid #1e293b;
  border-radius: 12px;
  padding: 16px;
  position: sticky;
  top: 75px;
  transition: all 0.3s ease;
}
.sum-title { font-size: 13.5px; font-weight: 700; margin-bottom: 14px; }
.sum-row {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  margin-bottom: 8px;
  color: #94a3b8;
}
.sum-row strong, .sum-row span:last-child { color: #f1f5f9; }
.primary-text { color: var(--primary-light) !important; }
.sum-divider { border: none; border-top: 1px solid #1e293b; margin: 12px 0; }
.sum-total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
  font-size: 13px;
}
.sum-total-price { font-size: 20px; font-weight: 800; color: var(--primary-light); }
.btn-checkout-primary {
  width: 100%;
  background: var(--primary-color);
  color: #042e22;
  border: none;
  padding: 11px;
  border-radius: 7px;
  font-weight: 800;
  font-size: 13px;
  cursor: pointer;
}
.btn-checkout-primary:disabled { opacity: 0.4; cursor: not-allowed; }

.telegram-support-card {
  margin-top: 14px;
  background: rgba(255, 255, 255, 0.04);
  border: 1px dashed var(--primary-color);
  border-radius: 8px;
  padding: 10px;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}
.tg-icon { font-size: 20px; }
.tg-info strong { font-size: 11.5px; display: block; color: var(--primary-light); }
.tg-info p { font-size: 10px; color: #94a3b8; margin: 0; }

/* Mobile Sticky Bar */
.mobile-sticky-bar {
  display: none;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #101722;
  border-top: 1px solid #1e293b;
  padding: 10px 16px;
  z-index: 999;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 -4px 16px rgba(0,0,0,0.5);
  transition: all 0.3s ease;
}
.mobile-bar-info {
  display: flex;
  flex-direction: column;
}
.m-pkg-name { font-size: 12px; color: #cbd5e1; }
.m-pkg-price { font-size: 17px; font-weight: 800; color: var(--primary-light); }
.btn-mobile-pay {
  background: var(--primary-color);
  color: #042e22;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  font-weight: 800;
  font-size: 12.5px;
  cursor: pointer;
}

/* Floating Chat */
.floating-chat-btn {
  position: fixed;
  bottom: 20px;
  right: 16px;
  background: var(--primary-color);
  color: #042e22;
  padding: 9px 15px;
  border-radius: 25px;
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  box-shadow: 0 4px 15px var(--primary-glow);
  font-weight: 800;
  font-size: 12px;
  z-index: 900;
}

/* Payment Modal */
.pay-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.8);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 14px;
}
.pay-modal {
  position: relative;
  width: 100%;
  max-width: 360px;
  background: #131b26;
  border: 1px solid var(--primary-color);
  border-radius: 14px;
  padding: 20px 16px;
  text-align: center;
}
.pay-close { position: absolute; top: 10px; right: 12px; background: none; border: none; color: #94a3b8; font-size: 18px; cursor: pointer; }
.pay-title { font-size: 15px; font-weight: 800; margin-bottom: 10px; }
.pay-amount { font-size: 26px; font-weight: 900; color: var(--primary-light); margin: 8px 0; }
.pay-qr { width: 230px; max-width: 100%; background: #fff; padding: 6px; border-radius: 10px; margin: 0 auto 10px; display: block; }
.pay-timer { font-size: 12px; font-weight: 700; color: #fbbf24; margin-bottom: 6px; }
.pay-waiting { font-size: 12px; color: var(--primary-light); display: flex; align-items: center; justify-content: center; gap: 6px; margin-bottom: 6px; }
.pay-note { font-size: 10.5px; color: #64748b; margin: 10px 0 0; }
.pay-code { background: #090e15; border: 1px dashed var(--primary-color); border-radius: 8px; padding: 8px; cursor: pointer; display: flex; flex-direction: column; gap: 2px; }
.pay-code small { color: #94a3b8; font-size: 10px; }
.pay-code strong { font-size: 17px; color: var(--primary-light); }
.pay-code span { font-size: 10px; color: #64748b; }
.pay-big-icon { font-size: 46px; margin-bottom: 4px; }
.pay-note-lg { font-size: 12.5px; color: #cbd5e1; margin-bottom: 12px; }
.pay-gap { margin-top: 12px; }
.spinner {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(0, 0, 0, 0.2);
  border-top-color: #000;
  border-radius: 50%;
  display: inline-block;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
@keyframes heroSpin { to { transform: rotate(360deg); } }

/* ===== LIGHT MODE STYLES (កែសម្រួល Banner Overlay ឱ្យវីដេអូច្បាស់) ===== */
.sai-app.light-theme {
  background-color: #f1f5f9;
  color: #1e293b;
}
.sai-app.light-theme .navbar {
  background: #ffffff;
  border-bottom-color: #e2e8f0;
}
.sai-app.light-theme .brand-text h2 {
  color: #0f172a;
}
.sai-app.light-theme .theme-toggle-btn {
  background: #f8fafc;
  color: #0f172a;
}
.sai-app.light-theme .theme-palette-dropdown {
  background: #ffffff;
  border-color: #cbd5e1;
}

/* ស្រទាប់ងងឹតស្តើង ជួយឱ្យវីដេអូច្បាស់ និងអក្សរពណ៌សលេចធ្លោ */
.sai-app.light-theme .hero-overlay {
  background: linear-gradient(
    90deg,
    rgba(15, 23, 42, 0.75),
    rgba(15, 23, 42, 0.45) 60%,
    rgba(15, 23, 42, 0.2)
  );
}
.sai-app.light-theme .hero-text h1,
.sai-app.light-theme .hero-text p {
  color: #ffffff !important;
}

.sai-app.light-theme .box-card,
.sai-app.light-theme .summary-box,
.sai-app.light-theme .catalog-card,
.sai-app.light-theme .pay-modal {
  background: #ffffff;
  border-color: #e2e8f0;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}
.sai-app.light-theme .section-badge-bar,
.sai-app.light-theme .category-title-bar {
  background: #f8fafc;
  color: #0f172a;
  border-left-color: var(--primary-color);
}
.sai-app.light-theme .dark-input {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: #0f172a;
}
.sai-app.light-theme .pkg-item {
  background: #f8fafc;
  border-color: #e2e8f0;
}
.sai-app.light-theme .pkg-price-tag,
.sai-app.light-theme .game-title,
.sai-app.light-theme .box-card-title,
.sai-app.light-theme .sum-title,
.sai-app.light-theme .pay-title,
.sai-app.light-theme .v-name {
  color: #0f172a;
}
.sai-app.light-theme .pkg-name-text,
.sai-app.light-theme .sum-row strong,
.sai-app.light-theme .sum-row span:last-child {
  color: #334155;
}
.sai-app.light-theme .mobile-sticky-bar {
  background: #ffffff;
  border-top-color: #e2e8f0;
}
.sai-app.light-theme .m-pkg-name {
  color: #475569;
}
.sai-app.light-theme .pay-code {
  background: #f8fafc;
}
.sai-app.light-theme .pay-note-lg {
  color: #475569;
}

/* ===== RESPONSIVE STYLES (MOBILE) ===== */
@media (max-width: 920px) {
  .layout-2col {
    grid-template-columns: 1fr;
  }
  .summary-side {
    display: none;
  }
  .mobile-sticky-bar {
    display: flex;
  }
  .floating-chat-btn {
    bottom: 74px;
  }
}

@media (max-width: 640px) {
  .theme-label-desktop {
    display: none;
  }
  .hero-content {
    flex-direction: column;
    text-align: center;
    padding: 16px 12px;
  }
  .hero-logo-wrap {
    width: 100px;
    height: 100px;
  }
  .hero-logo {
    width: 85px;
  }
  .input-row {
    grid-template-columns: 1fr;
    gap: 8px;
  }
  .game-cards-grid {
    grid-template-columns: repeat(3, 1fr);
  }
  .packages-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 380px) {
  .game-cards-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .packages-grid {
    grid-template-columns: 1fr 1fr;
  }
}
</style>