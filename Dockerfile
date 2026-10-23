FROM php:8.3-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
    libxml2-dev libicu-dev libzip-dev \
    curl \
    nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd intl pdo_mysql mysqli zip opcache \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP settings
RUN { \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 240'; \
    echo 'max_input_vars = 1500'; \
    echo 'upload_max_filesize = 32M'; \
    echo 'post_max_size = 32M'; \
} > /usr/local/etc/php/conf.d/typo3.ini

# Configure Apache document root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
    </Directory>' >> /etc/apache2/apache2.conf

# Working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy files
COPY . .

# Remove the local path repository and resolve the newest published release from Packagist.
# "*@beta" also accepts prereleases, so 1.0.0-beta.1 wins over 0.19.1 and a later stable release wins over both.
# The docs site and its registry (ui:add) then match what users get from Composer and npm.
RUN composer config --unset repositories.fluid-primitives \
    && composer require jramke/fluid-primitives:"*@beta" --no-update \
    && composer update jramke/fluid-primitives --with-dependencies --no-dev --no-scripts

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts \
    && composer run-script post-install-cmd || true

# Build frontend assets with the npm package of the same release as the Composer package.
# "npm install" instead of "npm ci": it replaces the "file:" dependency on the submodule with the published package,
# so the root package-lock.json doesn't have to match the library's version after each release.
RUN npm install fluid-primitives@"$(php -r 'require "vendor/autoload.php"; echo Composer\InstalledVersions::getPrettyVersion("jramke/fluid-primitives");')" \
    && npm run docs:build \
    && rm -rf node_modules /root/.npm

# Copy entrypoint
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port
EXPOSE 80

# Use custom entrypoint
ENTRYPOINT ["/entrypoint.sh"]
