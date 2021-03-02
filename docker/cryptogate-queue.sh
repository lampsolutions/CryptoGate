#!/bin/sh

set -e

cd /app

export LANG=C
export LANG

export LOG_CHANNEL=stderr
export LOG_CHANNEL

sleep 5

[ -d /data/storage ] || mkdir -p /data/storage
chown -R www-data:www-data /data/storage/

exec /sbin/setuser www-data php artisan queue:work --timeout=30
