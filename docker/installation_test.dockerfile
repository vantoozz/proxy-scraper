FROM php:7.4.15-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip\
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

WORKDIR /opt/project

RUN cd /opt/project \
    && composer init --name vantoozz/proxy-scraper-test \
    && composer require  -v \
        vantoozz/proxy-scraper:3.0.0-rc1 \
        guzzlehttp/guzzle:~7 \
        guzzlehttp/psr7 \
        hanneskod/classtools

COPY examples/01-auto_configuration.php /opt/project/examples/01-auto_configuration.php

RUN php /opt/project/examples/01-auto_configuration.php

CMD ["php", "/opt/project/examples/01-auto_configuration.php"]
