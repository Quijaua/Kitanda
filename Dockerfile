FROM php:8.2-apache

# Instalar extensões necessárias e cliente MySQL
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    unzip \
    git \
    wget \
    default-mysql-client \
    && docker-php-ext-install \
    pdo_mysql \
    intl \
    zip \
    && docker-php-ext-enable pdo_mysql intl zip

# Ativar mod_rewrite no Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar wait-for-it para aguardar dependências
RUN wget -q https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh -O /usr/local/bin/wait-for-it && \
    chmod +x /usr/local/bin/wait-for-it

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar código da aplicação
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Instalar dependências PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
