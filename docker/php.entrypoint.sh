#!/usr/bin/env bash

HOST_DOMAIN="docker.for.mac.host.internal"
ping -q -c1 ${HOST_DOMAIN} > /dev/null 2>&1
if [[ $? -ne 0 ]]; then
  HOST_IP=$(ip route | awk 'NR==1 {print $3}')
  echo -e "$HOST_IP\t$HOST_DOMAIN" >> /etc/hosts
fi

set -e

if [ "${1#-}" != "$1" ]; then
        set -- php "$@"
fi

exec "$@"
