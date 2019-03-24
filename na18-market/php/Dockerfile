FROM php:5.6-apache
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql
COPY 000-default.conf /etc/apache2/sites-available/
COPY default-ssl.conf /etc/apache2/sites-available/
COPY .bashrc /root/
RUN cd /etc/apache2/mods-enabled \
    && ln -s ../mods-available/rewrite.load
WORKDIR /var/www/html