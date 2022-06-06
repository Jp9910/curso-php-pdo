FROM php:7.4-cli

#RUN apt-get update

COPY ./ /var/www/html/

WORKDIR /var/www/html/

CMD ["php", "-S", "0.0.0.0:8080"]
