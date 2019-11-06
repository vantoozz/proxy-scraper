#!/usr/bin/env bash

docker run -it -v "$(pwd)":/app  --env CODACY_PROJECT_TOKEN="${CODACY_PROJECT_TOKEN}" \
  "$(docker build -q -f=docker/codacy.dockerfile docker)" \
    php /composer/vendor/bin/codacycoverage clover build/logs/clover.xml
