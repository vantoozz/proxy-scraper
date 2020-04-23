FROM php:7.4.5-cli

RUN apt-get update \
    && apt-get install -y libzip-dev zip unzip git iproute2 libssl-dev\
    && pecl install xdebug-2.9.4 \
    && docker-php-ext-enable xdebug \
    && rm -rf /var/lib/apt/lists/*

COPY docker/php.ini /usr/local/etc/php/

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer \
    && composer global require hirak/prestissimo

RUN  composer global require -v \
        squizlabs/php_codesniffer:~3 \
        phploc/phploc:~5 \
        sebastian/phpcpd:~4 \
        dancryer/php-docblock-checker:~1 \
        phpstan/phpstan:~0 \
        phpmd/phpmd:~2 \
        povils/phpmnd:~2 \
        vantoozz/phpcdm:~1

ENV PHP_IDE_CONFIG="serverName=docker"

COPY docker/php.entrypoint.sh /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

WORKDIR /opt/project

CMD ["php"]
