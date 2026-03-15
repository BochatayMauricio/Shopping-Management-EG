# 1. Base de la imagen con PHP y Apache
FROM php:8.2-apache

# 2. Instalar dependencias del sistema y Certificados SSL (Crucial para Gmail)
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libssl-dev \
    ca-certificates \
    && update-ca-certificates \
    && docker-php-ext-install mysqli pdo pdo_mysql zip sockets \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 3. Habilitar mod_rewrite para rutas amigables
RUN a2enmod rewrite

# 4. Evitar avisos de ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 5. Configurar directorio de trabajo
WORKDIR /var/www/html

# 6. Copiar archivos del proyecto
COPY . .

# 7. Instalar Composer y dependencias de PHP (PHPMailer, etc.)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 8. Configurar permisos para Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 9. Exponer puerto (Render usará su propia variable PORT)
EXPOSE 80

# 10. COMANDO DE INICIO (Adaptado para Render y Local)
# Este bloque permite que Render asigne el puerto dinámicamente
CMD PORT="${PORT:-80}" && \
    echo "Listen $PORT" > /etc/apache2/ports.conf && \
    sed -i "s/<VirtualHost \*:.*>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/000-default.conf && \
    apache2-foreground