#!/bin/bash

if [ ! -f /var/www/package-lock.json ]; then
    npm install
    npm run build
    vite build
fi

php /var/www/artisan reverb:start --port=18080 --verbose
