FROM php:7.4-fpm

#common soft
RUN apt-get update && apt-get install --fix-missing -y wget unzip autoconf pkg-config iputils-ping

#intl
RUN apt-get install -y zlib1g-dev libicu-dev g++ \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl

#xdebug
RUN pecl install xdebug-2.8.1 && docker-php-ext-enable xdebug

#composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
&& HASH="$(wget -q -O - https://composer.github.io/installer.sig)" \
&& php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
&& php composer-setup.php --install-dir=/usr/local/bin --filename=composer

