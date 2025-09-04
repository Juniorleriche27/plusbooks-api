# -------- Étape 1 : Build vendors (Composer) --------
FROM composer:2 AS vendor
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1

# 1) Installer les dépendances SANS scripts (artisan pas encore présent)
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-scripts

# 2) Copier tout le code, puis ré-exécuter install AVEC scripts
COPY . .
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# -------- Étape 2 : Runtime (PHP + Apache) --------
FROM php:8.2-apache

# Extensions nécessaires (pgsql) + modules Apache
RUN apt-get update \
 && apt-get install -y libpq-dev zip unzip \
 && docker-php-ext-install pdo pdo_pgsql \
 && a2enmod rewrite

# DocumentRoot -> /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf /etc/apache2/sites-available/default-ssl.conf

WORKDIR /var/www/html
COPY . /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor

# Permissions pour cache & storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
