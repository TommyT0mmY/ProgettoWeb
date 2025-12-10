#Configurazioni del container PHP con Apache

FROM php:apache

# Installazione moduli PHP
RUN apt-get update && apt-get install -y \
    libzip-dev zip \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli zip

# Abilita il modulo rewrite e headers di Apache
RUN a2enmod rewrite headers

# Configurazioni di sicurezza
RUN echo "session.use_strict_mode = 1" > /usr/local/etc/php/conf.d/security-custom.ini \
    && echo "session.cookie_httponly = 1" >> /usr/local/etc/php/conf.d/security-custom.ini
