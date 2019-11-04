FROM composer:1.9.1

WORKDIR /app

ENV COMPOSER_HOME /composer

RUN composer global require hirak/prestissimo -vvv

RUN composer global require -v \
	vantoozz/phpcdm:~1 \
	squizlabs/php_codesniffer:~3 \
	phploc/phploc:~5 \
	sebastian/phpcpd:~4 \
	dancryer/php-docblock-checker:~1 \
	phpstan/phpstan:~0 \
	phpunit/phpunit:~8 \
	phpmd/phpmd:~2 \
	povils/phpmnd:~2
