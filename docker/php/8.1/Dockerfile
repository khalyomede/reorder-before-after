FROM ubuntu:22.04

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && \
    apt-get upgrade --yes && \
    apt-get install --yes \
    software-properties-common \
    git \
    zip && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get upgrade --yes && \
    apt-get install --yes \
    php8.1 \
    php8.1-dom \
    php8.1-curl \
    php8.1-intl && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer
