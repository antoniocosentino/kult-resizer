FROM php:7.0-apache

RUN apt-get update && \
    apt-get clean

COPY src /var/www/html/

ADD /src/start.sh /home/root/start.sh
RUN chmod 777 /home/root/start.sh
