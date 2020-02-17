#!/usr/bin/env bash

for file in ./examples/*.php; do
  if [[ -f ${file} ]]; then
    php ${file}
    if [ $? != 0 ]; then
      echo "Example broken: "
      echo ${file}
      exit 1
    fi
  fi
done
