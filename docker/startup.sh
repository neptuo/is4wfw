mkdir -p \
    /var/www/html/cache \
    /var/www/html/cache/images \
    /var/www/html/cache/modules \
    /var/www/html/cache/output \
    /var/www/html/cache/pages \
    /var/www/html/cache/templates \
    /var/www/html/cache/systemproperty \
    /var/www/html/instance \
    /var/www/html/instance/logs \
    /var/www/html/instance/modules \
    /var/www/html/instance/user \
    /var/www/html/instance/user/bundles \
    /var/www/html/instance/user/filesystem \
    /var/www/html/instance/user/public \

chmod 777 -R /var/www/html/cache
chmod 777 -R /var/www/html/instance

php /var/www/html/bin/modules-generator.php
apache2-foreground