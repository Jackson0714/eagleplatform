FROM php:5.6-fpm
LABEL maintainer "Billy Zhou"

RUN apt-get update && apt-get install -y libssl-dev && rm -rf /var/lib/apt/lists/*
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb
