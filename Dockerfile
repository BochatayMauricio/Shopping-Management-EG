FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Configurar ServerName para evitar warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos
COPY . .

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Crear script de inicio
RUN echo '#!/bin/bash\n\
PORT=${PORT:-80}\n\
echo "Listen $PORT" > /etc/apache2/ports.conf\n\
sed -i "s/<VirtualHost \\*:.*>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/000-default.conf\n\
echo "Starting Apache on port $PORT"\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]