# 
# PHP Composer Dependencies
# 
FROM composer:2.5.8 as vendor
 
WORKDIR /app
COPY database/ database/
 
COPY composer.json composer.json
COPY composer.lock composer.lock
 
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist


# 
# Frontend JS Dependencies
# 
FROM node:lts-alpine as frontend

# For Backend CMS
WORKDIR /app

RUN node -v
RUN mkdir -p ./public
COPY package.json ./
RUN npm install
RUN ls -la
RUN ls -la ./public


# 
# App
# 
# FROM mcpidinfra/php7.4-apache:latest as appbase
FROM php:8.1.21-apache-bullseye as appbase

# TIMEZONE
ENV TZ=Asia/Jakarta
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
    
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    nano \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_pgsql

# Expose database port
EXPOSE 3306
EXPOSE 5432

# Set environment
ENV APP_HOME /var/www/html

RUN usermod -u 'stat -c %u /var/www/html' www-data || true
RUN groupmod -g 'stat -c %g /var/www/html' www-data || true

# Enable apache modules
RUN a2enmod rewrite headers
RUN echo "ServerName hris-dev.kalonay.com" >> /etc/apache2/apache2.conf
RUN sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf

COPY ./build/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy app to folder as an environment
COPY ./ /var/www/html

# Set WORKDIR
WORKDIR /var/www/html

# Copy Composer dependencies
COPY --from=vendor /app/vendor ./vendor

# Copy Frontend build
COPY --from=frontend /app/node_modules ./node_modules
# COPY --from=frontend /app/public/js ./public/js

# setting up the app
COPY .env.example /var/www/html/.env
RUN php artisan key:generate
RUN php artisan storage:link
RUN sed -i "s/APP_ENV=/APP_ENV=development/g" /var/www/html/.env
RUN sed -i "s/APP_URL=/APP_URL=hris-dev.kalonay.com/g" /var/www/html/.env
RUN sed -i "s/DB_HOST=/DB_HOST=103.150.117.59/g" /var/www/html/.env
RUN sed -i "s/DB_PASSWORD=/DB_PASSWORD=main@kalon4y/g" /var/www/html/.env

# Backward directory for the next step
RUN cd ../

# Change ownership of our applications
RUN mkdir /var/www/.npm && chown -R 33:33 /var/www/.npm
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/public \
    /var/www/html/bootstrap/cache 

RUN chmod -R ug+rwx /var/www/html/storage/logs
RUN chmod -R ug+rwx /var/www/html/storage/framework
RUN chmod -R ug+rwx /var/www/html/bootstrap/cache

# Finishing the app
RUN rm Dockerfile
RUN php artisan config:cache && php artisan optimize:clear
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port=2025"]
