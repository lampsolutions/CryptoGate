#!/bin/sh

set -e

cd /app

export LANG=C
export LANG

sleep 30

exec php artisan payments:watch