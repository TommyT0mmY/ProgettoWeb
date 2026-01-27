#Configurazioni del container PHP con Apache

FROM php:apache

# Installazione moduli PHP
RUN apt-get update && apt-get install -y \
    libzip-dev zip \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli zip

# Prepare session directory with safe permissions
RUN mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions \
    && chmod 1733 /var/lib/php/sessions

# Abilita il modulo rewrite e headers di Apache
RUN a2enmod rewrite headers

# Configurazioni di sicurezza
COPY security-custom.ini /usr/local/etc/php/conf.d/
