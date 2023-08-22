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

chmod 777 -R /var/www/html/cache --silent
chmod 777 -R /var/www/html/instance --silent

if [ -z "$TZ" ]
then
    echo "No timezone defined, use TZ environment variable to change timezone";
else
    echo "Using timezone $TZ"
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime
    echo $TZ > /etc/timezone
    echo "\ndate.timezone = $TZ\n" >> /usr/local/etc/php/conf.d/php-my.ini
fi

php /var/www/html/bin/migrate.php
apache2-foreground