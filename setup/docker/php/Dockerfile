FROM php:7.4-fpm-alpine

# lumen packages
RUN apk add --no-cache --update git composer \
	&& docker-php-ext-install tokenizer mysqli pdo_mysql

WORKDIR /var/www/html
