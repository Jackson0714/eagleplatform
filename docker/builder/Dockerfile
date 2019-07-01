FROM ubuntu:latest
MAINTAINER sean 13700324600@163.com
ENV TZ Asia/Shanghai
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       tzdata \
    && dpkg-reconfigure tzdata
RUN apt-get install -y --no-install-recommends \
    php-mongodb \
    php-mbstring \
    php-xml \
    composer \
    npm \
    git
RUN composer config -g repo.packagist composer https://packagist.phpcomposer.com \
    && npm install -g cnpm --registry=https://registry.npm.taobao.org
WORKDIR /srv