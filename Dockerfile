FROM php:7.1-cli

MAINTAINER Dmitry Rodin <madiedinro@gmail.com>

RUN apt-get update \
  && apt-get install --no-install-recommends -y apt-utils libcurl4-openssl-dev libevent-dev libssl-dev git gzip wget unzip curl \
  && rm -rf /var/lib/apt/lists/* \
  && docker-php-source extract \
  && docker-php-ext-install sockets \
  && pecl install event\
  && pecl install eio \
  && docker-php-ext-enable event eio \
  && curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer

ADD composer.json /tmp/composer.json
RUN cd /tmp && composer install
RUN mkdir -p /opt/app \
  && cp -a /tmp/vendor /opt/app/ \
  && cp /tmp/composer.lock /opt/app/

# From here we load our application's code in, therefore the previous docker
# "layer" thats been cached will be used if possible
WORKDIR /opt/app
ADD . /opt/app

# loading latest database
RUN ./update_db

EXPOSE 8080

CMD ["php", "server.php", "--host", "0.0.0.0", "--port", "8080"]
