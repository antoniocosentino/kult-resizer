FROM php:7.0-apache

RUN apt-get update -qq && apt-get install -y -qq \
        libicu-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libcurl4-openssl-dev \
        software-properties-common  \
        libcurl3 curl \
        git \
        zip \
        unzip \
        inotify-tools

RUN apt-get update -qq && apt-get install -y -qq \
        build-essential \
        libxml2-dev libxslt1-dev zlib1g-dev \
        git \
        mysql-client \
        sshpass \
        nano \
        sudo \
        vim \
        graphviz \
        netcat-openbsd

RUN docker-php-ext-install iconv mcrypt mbstring \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && docker-php-ext-install zip \
    && docker-php-ext-install curl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql

COPY src /var/www/html/

ADD /src/start.sh /home/root/start.sh
RUN chmod 777 /home/root/start.sh
