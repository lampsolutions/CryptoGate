#!/bin/sh

set -e

cd /app

# create storage directory if it does not exist
[ -d /data/storage ] || mkdir -p /data/storage
[ -d /data/storage/framework ] || mkdir /data/storage/framework
[ -d /data/storage/framework/sessions ] || mkdir /data/storage/framework/sessions
[ -d /data/storage/framework/views ] || mkdir /data/storage/framework/views
[ -d /data/storage/framework/cache ] || mkdir /data/storage/framework/cache
[ -d /data/storage/framework/logs ] || mkdir /data/storage/framework/logs
[ -d /data/storage/sessions ] || mkdir /data/storage/sessions
[ -d /data/storage/views ] || mkdir /data/storage/views
[ -d /data/storage/cache ] || mkdir /data/storage/cache
[ -d /data/storage/logs ] || mkdir /data/storage/logs
# set permissions for laravel storage
chown -R www-data:www-data /data/storage/

# for sqlite3 db
chown www-data:www-data /data

touch /data/paygate.db
chown www-data:www-data /data/paygate.db
CACHE_DRIVER=file php artisan migrate --force --quiet
php artisan view:clear
php artisan view:cache

export APACHE_RUN_USER=www-data
export APACHE_RUN_GROUP=www-data
export APACHE_PID_FILE=/var/run/apache2/apache2.pid
export APACHE_RUN_DIR=/var/run/apache2
export APACHE_LOCK_DIR=/var/lock/apache2
export APACHE_LOG_DIR=/var/log/apache2

export LANG=C
export LANG

exec /usr/sbin/apache2 -f /etc/apache2/apache2.conf -DFOREGROUND -DNO_DETACH
