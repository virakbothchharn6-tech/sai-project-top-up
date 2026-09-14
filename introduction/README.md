# 🎮 Sai Top-Up — Game Top-Up Store

A full-stack e-commerce web application for purchasing in-game top-up packages (credits, diamonds, UC, etc.) for popular mobile and PC games. Built with a modern **Laravel + Vue.js** monorepo architecture and fully containerized with **Docker** for easy setup and deployment.

---

## 📖 Introduction

**Sai Top-Up** is a personal full-stack project that simulates a real-world game top-up store, similar to services like Codashop or Garena Shop. Users can browse a list of supported games, select a game, and top up in-game currency (diamonds, UC, credits, etc.) or purchase items such as skins.

For each purchase, the user enters their **Game ID** and **Zone ID**, and the system performs a **Check Name** step to verify the in-game account before proceeding to payment. Payment is currently integrated with **KHQR** as a demo, with **ABA PayWay** integration planned as the project progresses.

This project was built and developed step by step, session by session — starting from setting up the project folder structure, through backend (Laravel) and frontend (Vue.js) development, Docker containerization, and eventually payment integration and website hosting.

This project was built to practice:
- Full-stack development (Laravel + Vue.js)
- RESTful API design
- Database design & seeding
- Payment gateway integration (KHQR, and later ABA PayWay)
- Containerized deployment workflows using Docker

---

## 📝 Description

Sai Top-Up allows users to:
1. Browse a catalog of supported games
2. View top-up packages (with pricing) for each selected game
3. Enter their **Game ID** and **Zone ID**, then verify the account via **Check Name**
4. Proceed to payment via **KHQR** (demo), with **ABA PayWay** coming soon
5. Manage games & packages through an admin-ready backend structure

The project separates concerns cleanly between the **backend (API)** and **frontend (UI)**, communicating via a RESTful API, and everything runs together using Docker Compose.

---

## ✨ Features

- 🎮 Browse list of supported games
- 💰 View top-up packages (with pricing) per game
- 🆔 Enter Game ID & Zone ID with account **Check Name** verification
- 💳 Payment integration with **KHQR** (demo)
- 🔌 RESTful API built with Laravel
- ⚡ Reactive frontend built with Vue.js
- 🐳 Fully Dockerized (backend, frontend, database)
- 🌱 Database seeders for sample games & packages

---

## 🛠️ Tech Stack

| Layer    | Technology              |
|----------|---------------------------|
| Backend  | Laravel (PHP)            |
| Frontend | Vue.js + Vite             |
| Database | MySQL / SQLite            |
| DevOps   | Docker & Docker Compose   |

---

## 📂 Project Structure

```
sai-top-up/
├── backend/          # Laravel API
├── frontend/          # Vue.js application
├── nginx/             # Web server configuration
├── docker-compose.yml
└── README.md
```

---

## 🚀 Getting Started (Step by Step)

### Prerequisites
Make sure you have the following installed:
- [Docker](https://www.docker.com/) & Docker Compose
- [Git](https://git-scm.com/)

### Step 1 — Clone the repository
```bash
git clone https://github.com/virakbothchharn6-tech/sai-project-top-up.git
cd sai-project-top-up
```

### Step 2 — Set up environment variables
Copy the example environment file for the backend:
```bash
cp backend/.env.example backend/.env
```
Copy the example environment file for the frontend:
```bash
cp frontend/vite.config.js.example frontend/vite.config.js
```

### Step 3 — Build and run with Docker
```bash
docker-compose up -d --build
```

### Step 4 — Install backend dependencies
```bash
docker-compose exec backend composer install
```

### Step 5 — Generate app key
```bash
docker-compose exec backend php artisan key:generate
```

### Step 6 — Run database migrations & seeders
```bash
docker-compose exec backend php artisan migrate --seed
```

### Step 7 — Install frontend dependencies
```bash
docker-compose exec frontend npm install
```

### Step 8 — Access the application
- Frontend: `http://localhost:5173` (or your configured port)
- Backend API: `http://localhost:8000/api`

---

## 🗄️ Database Seeders

The project includes seeders for:
- `GameSeeder` — sample games
- `TopUpPackageSeeder` — sample top-up packages per game

Run seeders anytime with:
```bash
docker-compose exec backend php artisan db:seed
```

---

## 📌 Roadmap

- [x] Browse games & top-up packages
- [x] Game ID / Zone ID input with Check Name verification
- [x] KHQR payment integration (demo)
- [ ] ABA PayWay payment integration
- [ ] User authentication
- [ ] Admin dashboard for managing games/packages
- [ ] Order history
- [ ] Website hosting / production deployment

---

## 📄 License

This project is for personal/educational purposes.
