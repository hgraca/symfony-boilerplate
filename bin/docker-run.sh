#!/usr/bin/env bash

docker run --rm \
  -e PHP_IDE_CONFIG="serverName=$(basename $(pwd))" \
  -v $(pwd)/build/php.ini:/usr/local/etc/php/php.ini \
  -v $(pwd)/build/xdebug.ini:/usr/local/etc/php/conf.d/xdebug.ini \
  -v ~/.cache/composer:/.composer/cache/ \
  -v $(pwd):/$(basename $(pwd)) \
  -w /$(basename $(pwd)) \
  -u $(id -u):$(id -g) \
  $@
