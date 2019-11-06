FROM composer:1.9.1

WORKDIR /app

ENV COMPOSER_HOME /composer

RUN composer global require hirak/prestissimo -vvv

RUN composer global require -v \
	codacy/coverage:~1
