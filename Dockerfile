FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

# Cambiar Apache para que escuche en 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf && \
    sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html/

EXPOSE 8080
