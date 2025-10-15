FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
        git \
        curl \
        zlib1g-dev \
        libzip-dev \
        libpng-dev \
        libxml2-dev \
#        openssl \
#        libcurl4-openssl-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        zip \
        unzip \
        # Utils to support Makefile
        bsdmainutils \
      #  && apt-get install -y libmagickwand-dev --no-install-recommends \
        # clean up \
        && apt-get autoclean -y \
        && rm -rf /var/lib/apt/lists/* \
        && rm -rf /tmp/pear/

RUN \
    # pdo_mysql
     docker-php-ext-install pdo_mysql \
    && docker-php-ext-install sockets \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install zip gd iconv \
    #redis
    && pecl install -o -f redis \
    && docker-php-ext-enable redis \
    # clean up
    && apt-get autoclean -y \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/pear/

# Install npm
RUN apt-get update && apt-get install -y \
    curl \
    gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

#Install composer
RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
### BASE PART FOR ALL ENV ### < END

# # # xDebug # # #
RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /var/www
USER $user


COPY deploy/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY deploy/node/entrypoint.sh /usr/local/bin/node-entrypoint.sh
RUN chmod +x /usr/local/bin/node-entrypoint.sh

#RUN npm install
#RUN npm run build

#ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
