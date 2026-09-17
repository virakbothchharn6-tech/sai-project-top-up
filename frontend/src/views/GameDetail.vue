<script setup>
import { ref, computed, nextTick } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();
const gameSlug = route.params.slug || "mobile-legends";

// 1. Data រៀបចំ Server Grouped by Country
const serverGroups = ref([
  {
    country: "Cambodia",
    flag: "🇰🇭",
    servers: [
      "KH Server 01 (Asia)",
      "KH Server 02 (Phnom Penh)",
      "KH VIP Server",
    ],
  },
  {
    country: "Thailand",
    flag: "🇹🇭",
    servers: ["TH Bangkok 01", "TH Bangkok 02", "TH Esport Server"],
  },
  {
    country: "Vietnam",
    flag: "🇻🇳",
    servers: ["VN Hanoi 01", "VN HCMC 02"],
  },
  {
    country: "Global / Other",
    flag: "🌐",
    servers: ["Global Server 1001", "Global Server 2002", "Advanced Server"],
  },
]);

// Diamond Packages List
const packages = ref([
  { id: 1, name: "86 Diamonds", diamonds: 86, bonus: "+8 Bonus", price: 1.5 },
  {
    id: 2,
    name: "172 Diamonds",
    diamonds: 172,
    bonus: "+16 Bonus",
    price: 3.0,
  },
  {
    id: 3,
    name: "257 Diamonds",
    diamonds: 257,
    bonus: "+25 Bonus",
    price: 4.5,
  },
  {
    id: 4,
    name: "344 Diamonds",
    diamonds: 344,
    bonus: "+34 Bonus",
    price: 6.0,
  },
  {
    id: 5,
    name: "429 Diamonds",
    diamonds: 429,
    bonus: "+43 Bonus",
    price: 7.5,
  },
  {
    id: 6,
    name: "706 Diamonds",
    diamonds: 706,
    bonus: "+71 Bonus",
    price: 12.0,
  },
  {
    id: 7,
    name: "2195 Diamonds",
    diamonds: 2195,
    bonus: "+360 Bonus",
    price: 35.0,
  },
  {
    id: 8,
    name: "Weekly Diamond Pass",
    diamonds: 210,
    bonus: "Special VIP",
    price: 1.99,
  },
]);

// State Management
const selectedCountry = ref("");
const selectedServer = ref("");
const playerId = ref("");
const isChecking = ref(false);
const verifiedPlayer = ref(null);
const checkError = ref("");
const selectedPackage = ref(null);

// Section Ref សម្រាប់ Auto Scroll
const packageSectionRef = ref(null);

// ទាញយក List Servers តាម Country ដែលបានរើស
const availableServers = computed(() => {
  const found = serverGroups.value.find(
    (g) => g.country === selectedCountry.value,
  );
  return found ? found.servers : [];
});

const onCountryChange = () => {
  selectedServer.value = "";
  verifiedPlayer.value = null;
  checkError.value = "";
};

// 4. Logic ផ្ទៀងផ្ទាត់ Check ID
const handleCheckId = async () => {
  checkError.value = "";
  verifiedPlayer.value = null;

  if (!selectedCountry.value) {
    checkError.value = "សូមជ្រើសរើសប្រទេសជាមុនសិន!";
    return;
  }
  if (!selectedServer.value) {
    checkError.value = "សូមជ្រើសរើស Server ID!";
    return;
  }
  if (!playerId.value.trim()) {
    checkError.value = "សូមបញ្ចូល Player ID របស់អ្នក!";
    return;
  }

  isChecking.value = true;
  try {
    const res = await axios.post("http://localhost/api/check-player", {
      player_id: playerId.value.trim(),
      server_id: selectedServer.value,
      game: gameSlug,
    });

    if (res.data && res.data.success) {
      verifiedPlayer.value = {
        username: res.data.username,
        playerId: playerId.value.trim(),
        server: selectedServer.value,
      };

      // ជោគជ័យ: Auto-scroll ទៅកាន់ផ្នែក Diamond Package Selection
      await nextTick();
      if (packageSectionRef.value) {
        packageSectionRef.value.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    }
  } catch (err) {
    if (err.response && err.response.data && err.response.data.message) {
      checkError.value = err.response.data.message;
    } else {
      checkError.value = "រកមិនឃើញ Player នេះទេ! សូមពិនិត្យ ID/Server ម្តងទៀត";
    }
  } finally {
    isChecking.value = false;
  }
};

const selectPackage = (pkg) => {
  if (!verifiedPlayer.value) {
    checkError.value =
      "សូមផ្ទៀងផ្ទាត់ Player ID អោយបានជោគជ័យសិន មុននឹងជ្រើសកញ្ចប់!";
    window.scrollTo({ top: 200, behavior: "smooth" });
    return;
  }
  selectedPackage.value = pkg;
};
</script>

<template>
  <div class="sai-topup-container">
    <!-- Top Glowing Bar -->
    <div class="top-nav-accent"></div>

    <!-- Header Section -->
    <header class="hero-header">
      <div class="status-pill">
        <span class="live-dot"></span> Instant Delivery 24/7
      </div>
      <h1 class="brand-title">SAI <span class="highlight">TOP-UP</span></h1>
      <p class="hero-sub">
        Recharge {{ gameSlug.replace("-", " ").toUpperCase() }} diamonds
        instantly. ផ្ទៀងផ្ទាត់ Player ID របស់អ្នក
        រួចជ្រើសរើសកញ្ចប់ពេជ្រដើម្បីទូទាត់ប្រាក់ភ្លាមៗ។
      </p>

      <div class="trust-stats">
        <span>📦 10M+ Orders</span>
        <span>⭐ 4.9/5 Rating</span>
        <span>⚡ Instant Delivery</span>
        <span>🎧 24/7 Support</span>
      </div>
    </header>

    <!-- Main Content Layout -->
    <main class="topup-layout">
      <!-- Left Column: Steps Form -->
      <div class="steps-column">
        <!-- Progress Steps Tracker -->
        <div class="step-tracker">
          <div
            class="step-item"
            :class="{ active: !verifiedPlayer, completed: verifiedPlayer }"
          >
            <div class="step-circle">1</div>
            <span>Player ID</span>
          </div>
          <div class="step-line" :class="{ done: verifiedPlayer }"></div>
          <div
            class="step-item"
            :class="{
              active: verifiedPlayer && !selectedPackage,
              completed: selectedPackage,
            }"
          >
            <div class="step-circle">2</div>
            <span>Package</span>
          </div>
          <div class="step-line" :class="{ done: selectedPackage }"></div>
          <div class="step-item" :class="{ active: selectedPackage }">
            <div class="step-circle">3</div>
            <span>Payment</span>
          </div>
        </div>

        <!-- STEP 1: Identification Card -->
        <section class="card-box">
          <div class="card-box-header">
            <span class="badge-num">1</span>
            <div>
              <h3>ជ្រើសរើស SERVER & បញ្ចូល PLAYER ID</h3>
              <p class="subtext">
                ជ្រើសរើសប្រទេស និង Server រួចចុច Check ID ដើម្បីផ្ទៀងផ្ទាត់ឈ្មោះ
              </p>
            </div>
          </div>

          <div class="form-body">
            <!-- 2. Country Select Dropdown -->
            <div class="input-group">
              <label>ជ្រើសរើសប្រទេស (Country)</label>
              <select
                v-model="selectedCountry"
                @change="onCountryChange"
                class="sai-select"
              >
                <option value="" disabled>-- សូមជ្រើសរើសប្រទេស --</option>
                <option
                  v-for="g in serverGroups"
                  :key="g.country"
                  :value="g.country"
                >
                  {{ g.flag }} {{ g.country }}
                </option>
              </select>
            </div>

            <!-- 2. Server Select Dropdown -->
            <div class="input-group">
              <label>ជ្រើសរើស Server / Zone ID</label>
              <select
                v-model="selectedServer"
                class="sai-select"
                :disabled="!selectedCountry"
              >
                <option value="" disabled>
                  {{
                    selectedCountry
                      ? "-- ជ្រើសរើស Server ក្នុង " + selectedCountry + " --"
                      : "-- សូមរើសប្រទេសជាមុនសិន --"
                  }}
                </option>
                <option v-for="srv in availableServers" :key="srv" :value="srv">
                  {{ srv }}
                </option>
              </select>
            </div>

            <!-- 3. Player ID Field + Check ID Button -->
            <div class="input-group">
              <label>បញ្ចូល Player ID</label>
              <div class="input-with-action">
                <input
                  v-model="playerId"
                  type="text"
                  placeholder="e.g. 12345678"
                  class="sai-input"
                  @keyup.enter="handleCheckId"
                />
                <button
                  type="button"
                  class="btn-check-id"
                  :disabled="isChecking"
                  @click="handleCheckId"
                >
                  <span v-if="isChecking" class="btn-spinner"></span>
                  <span v-else>🔍 Check ID</span>
                </button>
              </div>
            </div>

            <!-- Error Message -->
            <div v-if="checkError" class="alert-box error">
              ⚠️ {{ checkError }}
            </div>

            <!-- Verified Result Showcase -->
            <div v-if="verifiedPlayer" class="alert-box success">
              <div class="user-badge">
                <span class="check-icon">✓</span>
                <div>
                  <div class="verified-title">ផ្ទៀងផ្ទាត់ជោគជ័យ!</div>
                  <div class="verified-name">
                    ឈ្មោះគណនី: <strong>{{ verifiedPlayer.username }}</strong>
                  </div>
                  <div class="verified-meta">
                    ID: {{ verifiedPlayer.playerId }} | Server:
                    {{ verifiedPlayer.server }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- STEP 2: Diamond Packages Section (Target Auto Scroll) -->
        <section
          ref="packageSectionRef"
          class="card-box"
          :class="{ 'blur-lock': !verifiedPlayer }"
        >
          <div class="card-box-header">
            <span class="badge-num">2</span>
            <div>
              <h3>ជ្រើសរើសកញ្ចប់ពេជ្រ (SELECT DIAMOND PACKAGE)</h3>
              <p class="subtext">ជ្រើសកញ្ចប់ពេជ្រដែលអ្នកចង់បញ្ចូល</p>
            </div>
          </div>

          <div class="package-grid">
            <div
              v-for="pkg in packages"
              :key="pkg.id"
              class="package-card"
              :class="{ selected: selectedPackage?.id === pkg.id }"
              @click="selectPackage(pkg)"
            >
              <div class="diamond-icon">💎</div>
              <div class="pkg-diamonds">{{ pkg.diamonds }} ពេជ្រ</div>
              <div class="pkg-bonus">{{ pkg.bonus }}</div>
              <div class="pkg-price">${{ pkg.price.toFixed(2) }}</div>
            </div>
          </div>
        </section>
      </div>

      <!-- Right Column: Order Summary Sidebar -->
      <aside class="sidebar-column">
        <div class="summary-card">
          <h3 class="summary-title">🧾 ORDER SUMMARY</h3>

          <div class="summary-row">
            <span class="label">Brand:</span>
            <span class="val highlight-brand">Sai Top-Up</span>
          </div>

          <div class="summary-row">
            <span class="label">ហ្គេម:</span>
            <span class="val">{{
              gameSlug.replace("-", " ").toUpperCase()
            }}</span>
          </div>

          <div class="summary-row">
            <span class="label">Country:</span>
            <span class="val">{{ selectedCountry || "—" }}</span>
          </div>

          <div class="summary-row">
            <span class="label">Player ID:</span>
            <span class="val">{{
              verifiedPlayer?.playerId || playerId || "—"
            }}</span>
          </div>

          <div class="summary-row">
            <span class="label">Player Name:</span>
            <span class="val text-success">{{
              verifiedPlayer?.username || "—"
            }}</span>
          </div>

          <div class="summary-row">
            <span class="label">Server:</span>
            <span class="val">{{ selectedServer || "—" }}</span>
          </div>

          <div class="summary-row">
            <span class="label">កញ្ចប់ពេជ្រ:</span>
            <span class="val">{{ selectedPackage?.name || "—" }}</span>
          </div>

          <hr class="summary-divider" />

          <div class="total-row">
            <span>ទឹកប្រាក់សរុប:</span>
            <span class="total-price"
              >${{ (selectedPackage?.price || 0).toFixed(2) }}</span
            >
          </div>

          <button
            class="btn-proceed"
            :disabled="!verifiedPlayer || !selectedPackage"
          >
            បន្តទៅទូទាត់ប្រាក់ (Payment) ➔
          </button>

          <p class="secure-note">
            🛡️ ប្រតិបត្តិការមានសុវត្ថិភាពខ្ពស់
            ពេជ្រនឹងផ្ញើចូលគណនីហ្គេមរបស់អ្នកភ្លាមៗក្រោយទូទាត់។
          </p>
        </div>
      </aside>
    </main>
  </div>
</template>

<style scoped>
/* Color Palette: Sai Top-Up Emerald Dark Mode */
.sai-topup-container {
  background-color: #0b1118;
  min-height: 100vh;
  color: #e2e8f0;
  font-family:
    "Kantumruy Pro",
    "Inter",
    system-ui,
    -apple-system,
    sans-serif;
  padding-bottom: 80px;
}

.top-nav-accent {
  height: 4px;
  background: linear-gradient(90deg, #10b981, #34d399, #059669);
}

/* Header */
.hero-header {
  text-align: center;
  padding: 40px 20px 24px;
  max-width: 800px;
  margin: 0 auto;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(16, 185, 129, 0.12);
  border: 1px solid rgba(16, 185, 129, 0.4);
  color: #34d399;
  font-size: 12px;
  padding: 4px 14px;
  border-radius: 20px;
  margin-bottom: 16px;
}

.live-dot {
  width: 7px;
  height: 7px;
  background-color: #10b981;
  border-radius: 50%;
  box-shadow: 0 0 8px #10b981;
}

.brand-title {
  font-size: 38px;
  font-weight: 800;
  letter-spacing: 1px;
  margin-bottom: 10px;
  color: #ffffff;
}

.highlight {
  background: linear-gradient(90deg, #10b981, #34d399);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-sub {
  color: #94a3b8;
  font-size: 15px;
  line-height: 1.6;
}

.trust-stats {
  display: flex;
  justify-content: center;
  gap: 20px;
  flex-wrap: wrap;
  margin-top: 20px;
  font-size: 13px;
  color: #cbd5e1;
}

/* 2-Column Layout */
.topup-layout {
  max-width: 1120px;
  margin: 20px auto 0;
  padding: 0 20px;
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 28px;
}

@media (max-width: 900px) {
  .topup-layout {
    grid-template-columns: 1fr;
  }
}

/* Step Tracker */
.step-tracker {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
  padding: 0 10px;
}

.step-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #64748b;
  font-weight: 600;
}

.step-circle {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #1e293b;
  border: 1px solid #334155;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
}

.step-item.active .step-circle {
  background: #10b981;
  color: #062e22;
  border-color: #34d399;
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
}

.step-item.active {
  color: #34d399;
}

.step-item.completed .step-circle {
  background: #047857;
  color: white;
}

.step-line {
  flex: 1;
  height: 2px;
  background: #1e293b;
  margin: 0 12px;
}

.step-line.done {
  background: #10b981;
}

/* Card Box */
.card-box {
  background: #131b26;
  border: 1px solid #1e293b;
  border-radius: 14px;
  padding: 24px;
  margin-bottom: 24px;
  transition: all 0.3s ease;
}

.blur-lock {
  opacity: 0.5;
  pointer-events: none;
  filter: grayscale(0.5);
}

.card-box-header {
  display: flex;
  gap: 14px;
  align-items: flex-start;
  margin-bottom: 20px;
  border-bottom: 1px solid #1e293b;
  padding-bottom: 14px;
}

.badge-num {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(16, 185, 129, 0.15);
  color: #34d399;
  border: 1px solid rgba(16, 185, 129, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 15px;
}

.card-box-header h3 {
  font-size: 16px;
  font-weight: 700;
  color: #f8fafc;
  margin: 0 0 4px;
}

.card-box-header .subtext {
  font-size: 13px;
  color: #94a3b8;
  margin: 0;
}

/* Form inputs */
.form-body {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.input-group label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #cbd5e1;
  margin-bottom: 8px;
}

.sai-select,
.sai-input {
  width: 100%;
  background: #0d1520;
  border: 1px solid #2d3c50;
  color: #f8fafc;
  padding: 12px 14px;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
  transition: all 0.2s;
}

.sai-select:focus,
.sai-input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
}

.input-with-action {
  display: flex;
  gap: 10px;
}

.btn-check-id {
  background: #10b981;
  color: #062e22;
  font-weight: 700;
  border: none;
  border-radius: 8px;
  padding: 0 20px;
  font-size: 14px;
  cursor: pointer;
  white-space: nowrap;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn-check-id:hover:not(:disabled) {
  background: #34d399;
  box-shadow: 0 0 12px rgba(16, 185, 129, 0.4);
}

.btn-check-id:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Alerts */
.alert-box {
  border-radius: 8px;
  padding: 12px 16px;
  font-size: 14px;
}

.alert-box.error {
  background: rgba(239, 68, 68, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.4);
  color: #fca5a5;
}

.alert-box.success {
  background: rgba(16, 185, 129, 0.12);
  border: 1px solid rgba(16, 185, 129, 0.4);
  color: #a7f3d0;
}

.user-badge {
  display: flex;
  align-items: center;
  gap: 12px;
}

.check-icon {
  width: 28px;
  height: 28px;
  background: #10b981;
  color: #042f24;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

.verified-title {
  font-size: 12px;
  color: #34d399;
  text-transform: uppercase;
  font-weight: bold;
}

.verified-name {
  font-size: 16px;
  color: #ffffff;
}

.verified-meta {
  font-size: 12px;
  color: #94a3b8;
}

/* Diamond Packages Grid */
.package-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 14px;
}

.package-card {
  background: #0d1520;
  border: 1px solid #1e293b;
  border-radius: 10px;
  padding: 14px 10px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
}

.package-card:hover {
  border-color: #10b981;
  transform: translateY(-2px);
}

.package-card.selected {
  border-color: #10b981;
  background: rgba(16, 185, 129, 0.08);
  box-shadow: 0 0 14px rgba(16, 185, 129, 0.25);
}

.diamond-icon {
  font-size: 24px;
  margin-bottom: 4px;
}

.pkg-diamonds {
  font-size: 14px;
  font-weight: 700;
  color: #f8fafc;
}

.pkg-bonus {
  font-size: 11px;
  color: #34d399;
  margin-bottom: 8px;
}

.pkg-price {
  font-size: 15px;
  font-weight: 800;
  color: #ffffff;
  background: #1e293b;
  padding: 4px 8px;
  border-radius: 6px;
}

.package-card.selected .pkg-price {
  background: #10b981;
  color: #062e22;
}

/* Right Summary Sidebar */
.summary-card {
  background: #131b26;
  border: 1px solid #1e293b;
  border-radius: 14px;
  padding: 22px;
  position: sticky;
  top: 24px;
}

.summary-title {
  font-size: 15px;
  font-weight: 700;
  color: #f8fafc;
  margin: 0 0 18px;
  letter-spacing: 0.5px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 13px;
}

.summary-row .label {
  color: #94a3b8;
}

.summary-row .val {
  font-weight: 600;
  color: #f1f5f9;
  text-align: right;
}

.highlight-brand {
  color: #34d399;
  font-weight: 700;
}

.text-success {
  color: #34d399;
}

.summary-divider {
  border: none;
  border-top: 1px solid #1e293b;
  margin: 16px 0;
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  font-size: 15px;
  font-weight: 700;
}

.total-price {
  font-size: 22px;
  color: #34d399;
}

.btn-proceed {
  width: 100%;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  font-weight: 700;
  border: none;
  border-radius: 8px;
  padding: 13px 16px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
}

.btn-proceed:hover:not(:disabled) {
  opacity: 0.95;
  transform: translateY(-1px);
}

.btn-proceed:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.secure-note {
  font-size: 11px;
  color: #64748b;
  line-height: 1.5;
  text-align: center;
  margin: 14px 0 0;
}

/* Spinner Animation */
.btn-spinner {
  width: 16px;
  height: 16px;
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
