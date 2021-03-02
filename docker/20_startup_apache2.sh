#!/bin/sh

# on boot remove apache2 pid file in case of crash of host / docker / apache2
if [ -f "/var/run/apache2/apache2.pid" ]; then
    rm -f "/var/run/apache2/apache2.pid"
fi
