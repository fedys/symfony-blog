FROM php:7.3-apache
RUN apt-get update \
 && apt-get install -y \
    libicu-dev \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    unzip \
 && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
 && docker-php-ext-install -j$(nproc) \
    intl \
    zip \
    opcache \
    pdo_mysql \
    gd \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite
RUN usermod -u 1000 www-data
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
COPY php.ini /usr/local/etc/php/
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer \
  && curl -sL https://deb.nodesource.com/setup_10.x | bash - \
  && apt-get install -y nodejs \
  && curl -o- -L https://yarnpkg.com/install.sh | bash \
  && curl -fsSL 'https://xdebug.org/files/xdebug-2.7.2.tgz' -o xdebug.tar.gz \
        && mkdir -p xdebug \
        && tar -xf xdebug.tar.gz -C xdebug --strip-components=1 \
        && rm xdebug.tar.gz \
        && ( \
        cd xdebug \
        && phpize \
        && ./configure --enable-xdebug \
        && make -j$(nproc) \
        && make install \
        ) \
        && rm -r xdebug \
        && docker-php-ext-enable xdebug \
        && echo 'xdebug.remote_enable=1\n\
    xdebug.remote_connect_back=1\n\
    xdebug.show_local_vars=1\n'\
    >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./wait-for.sh /usr/
