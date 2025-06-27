FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    ca-certificates \
    gnupg \
    dirmngr \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g @vue/cli vite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ----------------------------------------------------
# phpDocumentor (bez konfliktu z Flysystem 3)
# ----------------------------------------------------
ARG PHPDOC_VERSION=3.8.0
RUN curl -Ls "https://github.com/phpDocumentor/phpDocumentor/releases/download/v${PHPDOC_VERSION}/phpDocumentor.phar" \
    -o /usr/local/bin/phpdoc \
 && chmod +x /usr/local/bin/phpdoc

RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

COPY . /var/www/html

RUN chown -R www:www /var/www/html

WORKDIR /var/www/html/laravel
RUN composer install

USER www

CMD ["php-fpm"]