#!/bin/sh
set -e

# Ensure writable directories exist
mkdir -p /var/www/html/var/log \
         /var/www/html/public/fileadmin \
         /var/www/html/public/typo3temp

# Only what TYPO3 writes at runtime belongs to the web user. The code, the configuration and vendor stay owned
# by root, so a bug that writes a file cannot change them.
chown -R www-data:www-data /var/www/html/var \
                           /var/www/html/public/fileadmin \
                           /var/www/html/public/typo3temp
chmod -R 775 /var/www/html/var \
             /var/www/html/public/fileadmin \
             /var/www/html/public/typo3temp

# The caches live in the database and in var/, so a new image has to start with empty ones
su -s /bin/sh www-data -c "vendor/bin/typo3 cache:flush || true"

# Builds the caches through the web server, with the host of the site, so the entries are the ones a visitor gets.
# Runs next to Apache: the port has to be open at once, a proxy shows a bad gateway until it is.
warm() {
    host=$(php -r 'echo parse_url(getenv("APP_URL"), PHP_URL_HOST);')
    su -s /bin/sh www-data -c "vendor/bin/typo3 cache:warmup || true"

    # Waits for Apache, and renders the home page on the way
    curl -fsS -o /dev/null --max-time 300 --retry 60 --retry-delay 2 --retry-connrefused \
        -H "Host: $host" http://127.0.0.1/ || { echo "Caches not warmed: the home page does not answer"; return 0; }

    { echo /the-pitch; sed -n 's|^ \+- \(.*\)|/\1|p' packages/docs/Resources/Private/Content/nav.yaml; } \
        | xargs -P 4 -I{} curl -fsS -o /dev/null --max-time 300 \
            -H "Host: $host" 'http://127.0.0.1{}' || true

    # The URL of the search index carries a cHash that only the site can sign, so it is read from a page
    search=$(curl -fsS --max-time 300 -H "Host: $host" http://127.0.0.1/docs/introduction \
        | grep -o '/?tx_docs_docs[^"]*SearchIndex[^"]*' | head -n 1 | sed 's/\\u0026/\&/g')
    if [ -n "$search" ]; then
        curl -fsS -o /dev/null --max-time 300 -H "Host: $host" "http://127.0.0.1$search" || true
    fi
    echo "Caches warmed"
}
warm &

# Start Apache
exec apache2-foreground
