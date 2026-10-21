FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql \
 && a2enmod rewrite headers

# Run Apache as the host user so bind-mounted logs/ and uploads/ stay writable
ARG UID=1000
ARG GID=1000
RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g ${GID} www-data

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/apache/app.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/app
COPY --chown=www-data:www-data . .
RUN mkdir -p logs public/uploads/pengaduan/tanggapan \
 && chown -R www-data:www-data logs public/uploads
