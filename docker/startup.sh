mkdir -p /var/www/html/instance \
    /var/www/html/instance/cache \
    /var/www/html/instance/cache/images \
    /var/www/html/instance/cache/pages \
    /var/www/html/instance/cache/systemproperty \
    /var/www/html/instance/cache/templates \
    /var/www/html/instance/logs \
    /var/www/html/instance/modules \
    /var/www/html/instance/user \
    /var/www/html/instance/user/bundles \
    /var/www/html/instance/user/filesystem \
    /var/www/html/instance/user/public \

chmod 777 -R /var/www/html/instance
apache2-foreground