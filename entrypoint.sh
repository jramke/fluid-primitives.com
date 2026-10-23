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

# Run TYPO3 cache commands
su -s /bin/sh www-data -c "vendor/bin/typo3 cache:flush || true"
su -s /bin/sh www-data -c "vendor/bin/typo3 cache:warmup || true"

# Start Apache
exec apache2-foreground
