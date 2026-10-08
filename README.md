# 🎮 Sai Top-Up — Game Top-Up Store

[![Live Demo](https://img.shields.io/badge/Live%20Demo-sai--top.com-brightgreen?style=for-the-badge&logo=googlechrome&logoColor=white)](https://sai-top.com)
[![Docker](https://img.shields.io/badge/Docker-Containerized-blue?style=for-the-badge&logo=docker&logoColor=white)](https://sai-top.com)
[![SSL](https://img.shields.io/badge/SSL-Let's%20Encrypt-success?style=for-the-badge&logo=letsencrypt&logoColor=white)](https://sai-top.com)
[![Laravel](https://img.shields.io/badge/Laravel-11-red?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue.js-3-emerald?style=for-the-badge&logo=vuedotjs&logoColor=white)](https://vuejs.org)

> 🌐 **Production Website:** [https://sai-top.com](https://sai-top.com)  
> Full-stack e-commerce web application for purchasing in-game top-up packages (credits, diamonds, UC, etc.) for popular mobile and PC games. Built with a modern **Laravel + Vue.js** monorepo architecture, integrated with **Telegram Bot API** for real-time payment confirmation, and fully containerized with **Docker** on Ubuntu VPS with custom domain and Let's Encrypt SSL.

---

## 📸 Screenshots & Demo

| Dark Mode (Hero Banner) | Light Mode (Hero Banner) |
| :---: | :---: |
| <img src="docs/screenshots/hero%20banner-dark.png" width="450" alt="Hero Dark Mode"/> | <img src="docs/screenshots/light%20banner%20.png" width="450" alt="Hero Light Mode"/> |

| KHQR Payment Modal | Telegram Bot Real-time Confirmation |
| :---: | :---: |
| <img src="docs/screenshots/khqr%20-model.png" width="450" alt="KHQR Payment"/> | <img src="docs/screenshots/Telegrame-bot.png" width="380" alt="Telegram Bot Notification"/> |

---

## 📖 Introduction

**Sai Top-Up** is a personal full-stack project simulating a real-world game top-up platform (similar to Codashop or Smile One). Users can browse supported games, select denomination packages, input their Player ID & Zone ID, and verify in-game identities via real-time **Check Name** verification before completing purchases.

The checkout flow features **ACLEDA KHQR** payment integration. When an order is processed, a **Telegram Bot** alerts the administrator with interactive inline action buttons (`⚠️ ឆែកលុយរួចហើយ - Confirm` / `Reject`) to approve transactions directly from Telegram.

### Key Highlights & Architecture:
- **Full-Stack Monorepo:** Laravel 11 (PHP 8.2+) RESTful backend with Vue 3 / Vite frontend.
- **Production Deployment:** Hosted on Ubuntu 20.04 VPS with custom domain (`sai-top.com`) and automated Let's Encrypt SSL (HTTPS).
- **Reverse Proxy & Routing:** Custom Nginx reverse proxy serving both frontend routes and backend `/api/` endpoints.
- **In-Game Verification:** Real-time mobile ID verification endpoints.
- **Telegram Polling Worker:** Background artisan worker (`telegram:poll`) running in Docker to handle real-time admin webhook callbacks.
- **Modern UI:** Tailwind CSS dark/light theme switching with responsive mobile layout.

---

## ✨ Features

- 🎮 **Game Catalog**: Browse popular supported games (Mobile Legends, Free Fire, Honor of Kings).
- 🆔 **Account Verification**: Instant in-game **Check Name** lookup for Game ID & Server ID.
- 💎 **Interactive Package Selection**: Clean denomination selector showing packages, passes, and prices.
- 💳 **ACLEDA KHQR Payment**: Dynamic KHQR code generation with countdown timer and order code.
- 🤖 **Telegram Order Integration**: Automated notification sent to Telegram channel/group when a customer places an order.
- ⚡ **Admin Telegram Polling**: Background Artisan CLI worker handling `Confirm` / `Reject` callbacks safely in real-time.
- 🌓 **Theme Switcher**: Fully responsive UI supporting customizable dark and light aesthetics.
- 🐳 **Dockerized Setup**: Monorepo orchestration with Docker Compose (Nginx, PHP-FPM, MySQL, phpMyAdmin, Telegram Worker).

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Live Production** | [https://sai-top.com](https://sai-top.com) (Ubuntu 20.04 VPS / Let's Encrypt SSL) |
| **Backend** | Laravel 11 (PHP 8.2+) |
| **Frontend** | Vue 3, Vite, Tailwind CSS, Pinia |
| **Database** | MySQL 8.0 / phpMyAdmin |
| **Integrations** | Telegram Bot API, ACLEDA KHQR |
| **DevOps** | Docker, Docker Compose, Nginx Reverse Proxy |

---

## 📂 Project Structure

```text
sai-project-top-up/
├── backend/
│   ├── app/
│   │   ├── Console/Commands/   # Telegram polling commands (TelegramPoll.php)
│   │   ├── Http/Controllers/   # OrderController & API endpoints
│   │   ├── Models/             # Order and Game models
│   │   └── Services/           # Telegram notification service
│   ├── database/migrations/    # Orders and Game schema migrations
│   └── routes/api.php          # API routes
├── frontend/
│   ├── public/                 # Media assets (MLBB videos & hero renders)
│   ├── src/views/Home.vue      # Main interactive store view
│   └── vite.config.js          # Vite config with allowedHosts for production
├── docs/screenshots/           # Readme preview images
├── nginx/
│   └── default.conf            # Nginx SSL reverse proxy configuration
├── docker-compose.yml          # Container stack orchestration
└── README.md

--

## 🔄 Order Lifecycle Flow

<img width="5356" height="11373" alt="architecture-diagram" src="https://github.com/user-attachments/assets/4392f0d3-0814-40e1-b730-5e2d306cf1f9" />

---

## 🚀 Getting Started

### Prerequisites
- [Docker Desktop](https://www.docker.com/) & Docker Compose
- [Git](https://git-scm.com/)

### 1. Clone the repository
```bash
git clone https://github.com/virakbothchharn6-tech/sai-project-top-up.git
cd sai-project-top-up
```

### 2. Configure Environment Variables
```bash
cp backend/.env.example backend/.env
```
Ensure your `backend/.env` has your Telegram Bot Token and Chat ID configured:
```env
Code snippet
APP_URL=[https://sai-top.com](https://sai-top.com)
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here

### 3. Build & Run Containers
```bash
docker compose up -d --build
```

### 4. Setup Backend & Database
```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

### 5. Run Telegram Polling (for order actions)
```bash
docker compose exec app php artisan telegram:poll
```

### 6. Access the Application
- **LiveProduct**: `https://sai-top.com/`
- **Local Web Store**:http://localhost:5175
- **Backend API**: `https://sai-top.com/api (Local: http://localhost:8000/api)`
- **phpMyAdmin**: `http://localhost:8080`

---

## 📌 Roadmap

- [x] Game Catalog & Top-Up Packages UI
- [x] Player ID / Zone ID Check Name verification
- [x] KHQR Payment modal demo
- [x] Telegram Bot integration (Order alerts & approval polling)
- [x] Mobile Legends Hero Banner with dynamic theme switching
- [ ] ABA PayWay direct gateway integration
- [ ] Admin Web Dashboard for manual order fulfillment
- [ ] User authentication & order history lookup

---
+
## 📄 License

This project is open-source and created for educational and portfolio demonstration purposes.
