FROM php:7.4-apache
MAINTAINER p-Clerick
RUN apt-get update && apt-get install -y --fix-missing \
    apt-utils \
    gnupg

RUN echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list
RUN echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list
RUN curl -sS --insecure https://www.dotdeb.org/dotdeb.gpg | apt-key add -

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    libxml2-dev \
    mc \
    git \
    unzip \
    wget \
    libpng-dev
RUN pecl install xdebug-3.1.1;
RUN docker-php-ext-enable xdebug;

RUN docker-php-ext-install  soap zip pdo_mysql bcmath exif sockets gd


RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"

RUN a2enmod rewrite
RUN service apache2 restart

ENV TZ=Europe/Kiev
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN echo 'root:root' | chpasswd

RUN groupadd --gid 1001 dev \
  && useradd --uid 1000 --gid dev --shell /bin/bash --create-home dev
RUN usermod -aG www-data dev

RUN chown -R dev:www-data /home/dev
RUN chmod -R 777 /home/dev

USER dev
RUN echo "export TERM=xterm-256color" > /home/dev/.profile




