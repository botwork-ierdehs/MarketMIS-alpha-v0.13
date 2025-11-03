# -----------------------------
# 1. Use the official PHP image
# -----------------------------
FROM php:8.2-apache

# -----------------------------
# 2. Install system dependencies and PHP extensions
# -----------------------------
RUN apt-get update && apt-get install -y \
    git zip unzip curl libpq-dev libzip-dev gnupg ca-certificates \
    && docker-php-ext-install pdo pdo_pgsql zip

# -----------------------------
# 3. Install Node.js (Render-safe method)
# -----------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    node -v && npm -v

# -----------------------------
# 4. Enable Apache rewrite module
# -----------------------------
RUN a2enmod rewrite

# -----------------------------
# 5. Set working directory
# -----------------------------
WORKDIR /var/www/html

# -----------------------------
# 6. Copy composer from composer image
# -----------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -----------------------------
# 7. Copy project files
# -----------------------------
COPY . .

# -----------------------------
# 8. Install backend dependencies
# -----------------------------
RUN composer install --optimize-autoloader --no-dev

# -----------------------------
# 9. Install and build frontend
# -----------------------------
RUN npm ci || npm install && npm run build

# -----------------------------
# 10. Ensure storage and cache directories exist, then set permissions
# -----------------------------
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

# -----------------------------
# 11. Expose port
# -----------------------------
EXPOSE 10000

# -----------------------------
# 12. Start Laravel server
# -----------------------------
CMD chmod -R 775 storage bootstrap/cache && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=10000
