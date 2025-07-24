FROM php:8.2-apache

# Instalar Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Instalar extensões PHP necessárias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar package.json do frontend primeiro
COPY frontend/package*.json /tmp/frontend/
WORKDIR /tmp/frontend
RUN npm install

# Copiar código do frontend e fazer build
COPY frontend/ /tmp/frontend/
RUN npm run build

# Copiar arquivos da aplicação
COPY src/ /var/www/html/

# Copiar build do Vue.js para pasta admin
RUN cp -r /tmp/frontend/dist/* /var/www/html/admin/ || mkdir -p /var/www/html/admin

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expor porta 80
EXPOSE 80