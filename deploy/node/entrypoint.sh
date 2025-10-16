#!/bin/bash

if [ ! -f /var/www/node_modules/.package-lock.json ]; then
    composer setup
fi

php /var/www/artisan reverb:start --port=18080 --verbose
