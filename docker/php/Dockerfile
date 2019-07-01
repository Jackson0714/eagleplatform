FROM php:7.2-fpm
MAINTAINER sean 13700324600@163.com
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.2/install.sh | bash \
    && /bin/bash -c 'cd /root/.nvm/ \
    && . nvm.sh \
    && nvm install node \
    && nvm use node \
    && npm install -g cnpm --registry=https://registry.npm.taobao.org'
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar  /usr/local/bin/composer \
    && composer config -g repo.packagist composer https://packagist.phpcomposer.com
RUN apt-get update \
    && apt-get install -y git
EXPOSE 9000