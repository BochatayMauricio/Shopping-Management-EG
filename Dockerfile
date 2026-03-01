FROM php:8.2-apache

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Configurar Apache para usar el puerto dinámico (Railway usa $PORT)
ENV PORT=8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf && \
    sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf

# Configurar Apache para permitir .htaccess y AllowOverride
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copiar archivos de la aplicación
COPY . /var/www/html/

# Establecer permisos correctos
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Crear directorio para uploads si no existe
RUN mkdir -p /var/www/html/assets/stores && \
    chown -R www-data:www-data /var/www/html/assets && \
    chmod -R 775 /var/www/html/assets

EXPOSE 8080

# Usar script de inicio para puerto dinámico
CMD sed -i "s/\${PORT}/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && apache2-foreground
