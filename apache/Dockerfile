#Configurazioni del container PHP con Apache

FROM php:apache

# Installazione moduli PHP
RUN apt-get update && apt-get install -y \
    libzip-dev zip \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli zip

# Abilita il modulo rewrite e headers di Apache
RUN a2enmod rewrite headers

# Configurazioni di sicurezza
COPY security-custom.ini /usr/local/etc/php/conf.d/
