# Session 1: Environment Setup & Docker Configuration

## Overview
In this session, we set up the containerized development environment for **Sai Top-Up** using Docker and Docker Compose, combining Laravel (Backend) and Vue.js (Frontend) into a unified monorepo.

## Objectives
- Initialize project folders and configure `docker-compose.yml`.
- Set up PHP-FPM, Nginx, MySQL, and Node.js containers.
- Verify container communication and repository connection on GitHub.

---

## Step 1 — Create the project folder structure

```bash
mkdir sai-top-up && cd sai-top-up
mkdir backend frontend nginx
```

Resulting structure:
```
sai-top-up/
├── backend/          # Laravel API (added in Session 2)
├── frontend/          # Vue.js app (added in Session 4)
├── nginx/             # Reverse proxy config
└── docker-compose.yml
```

## Step 2 — Write the backend Dockerfile

`backend/Dockerfile`:
```dockerfile
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --optimize-autoloader

CMD ["php-fpm"]
```

## Step 3 — Write the frontend Dockerfile

`frontend/Dockerfile`:
```dockerfile
FROM node:20-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .

EXPOSE 5173
CMD ["npm", "run", "dev", "--", "--host"]
```

## Step 4 — Configure Nginx as reverse proxy

`nginx/default.conf`:
```nginx
server {
    listen 80;
    index index.php index.html;
    root /var/www/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass backend:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Step 5 — Write `docker-compose.yml`

```yaml
version: "3.8"

services:
  backend:
    build: ./backend
    container_name: sai_backend
    volumes:
      - ./backend:/var/www
    depends_on:
      - mysql

  frontend:
    build: ./frontend
    container_name: sai_frontend
    volumes:
      - ./frontend:/app
    ports:
      - "5173:5173"

  nginx:
    image: nginx:alpine
    container_name: sai_nginx
    ports:
      - "8000:80"
    volumes:
      - ./backend:/var/www
      - ./nginx:/etc/nginx/conf.d
    depends_on:
      - backend

  mysql:
    image: mysql:8.0
    container_name: sai_mysql
    environment:
      MYSQL_DATABASE: sai_top_up
      MYSQL_ROOT_PASSWORD: root
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

## Step 6 — Build and start the containers

```bash
docker-compose up -d --build
docker ps
```
You should see `sai_backend`, `sai_frontend`, `sai_nginx`, and `sai_mysql` all running.

## Step 7 — Initialize Git and push to GitHub

```bash
git init
git add .
git commit -m "Session 1: Docker environment setup"
git remote add origin https://github.com/virakbothchharn6-tech/sai-project-top-up.git
git push -u origin main
```

## ✅ Result
By the end of this session, we have a working multi-container environment (Laravel + Vue.js + MySQL + Nginx) ready for backend and frontend development in the next sessions.
