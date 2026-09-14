# Session 8: Production Deployment & Hosting

## Overview
Prepare and deploy the containerized **Sai Top-Up** platform to a live production server.

## Objectives
- Configure production `.env` variables and optimize the Nginx server block.
- Run a Docker production build (`docker-compose -f docker-compose.prod.yml up -d`).
- Perform final testing on the live domain.

---

## Step 1 — Prepare a production `.env`

`backend/.env.production`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://saitopup.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=sai_top_up
DB_USERNAME=sai_user
DB_PASSWORD=CHANGE_ME_STRONG_PASSWORD

KHQR_MODE=live
ABA_PAYWAY_MERCHANT_ID=
ABA_PAYWAY_API_KEY=
```

## Step 2 — Build the frontend for production

```bash
docker-compose exec frontend npm run build
```
This outputs static files to `frontend/dist`, served by Nginx instead of the Vite dev server.

## Step 3 — Write `docker-compose.prod.yml`

```yaml
version: "3.8"

services:
  backend:
    build: ./backend
    container_name: sai_backend_prod
    restart: unless-stopped
    env_file: ./backend/.env.production
    depends_on:
      - mysql

  nginx:
    image: nginx:alpine
    container_name: sai_nginx_prod
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./backend:/var/www
      - ./frontend/dist:/var/www/public/app
      - ./nginx/prod.conf:/etc/nginx/conf.d/default.conf
      - ./certbot/conf:/etc/letsencrypt
    depends_on:
      - backend

  mysql:
    image: mysql:8.0
    container_name: sai_mysql_prod
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: sai_top_up
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql_prod_data:/var/lib/mysql

volumes:
  mysql_prod_data:
```

## Step 4 — Configure production Nginx with HTTPS

`nginx/prod.conf`:
```nginx
server {
    listen 80;
    server_name saitopup.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name saitopup.com;

    ssl_certificate     /etc/letsencrypt/live/saitopup.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/saitopup.com/privkey.pem;

    root /var/www/public;
    index index.php;

    location /api {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass backend:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location / {
        root /var/www/public/app;
        try_files $uri $uri/ /index.html;
    }
}
```

## Step 5 — Issue an SSL certificate with Certbot

```bash
docker run -it --rm \
  -v ./certbot/conf:/etc/letsencrypt \
  -v ./certbot/www:/var/www/certbot \
  certbot/certbot certonly --webroot -w /var/www/certbot \
  -d saitopup.com
```

## Step 6 — Deploy

```bash
docker-compose -f docker-compose.prod.yml up -d --build
docker-compose -f docker-compose.prod.yml exec backend php artisan migrate --force
docker-compose -f docker-compose.prod.yml exec backend php artisan config:cache
```

## Step 7 — Final testing checklist

- [ ] Homepage loads game catalog over HTTPS
- [ ] Game ID / Zone ID Check Name works against the live API
- [ ] KHQR QR code generates and payment status polling works
- [ ] Admin dashboard is reachable only when logged in
- [ ] ABA PayWay credentials added once merchant approval is received

## ✅ Result
**Sai Top-Up** is now live on a production domain with HTTPS, a built frontend, a hardened Laravel backend, and KHQR payments working end-to-end — with ABA PayWay ready to be switched on once credentials are available.
