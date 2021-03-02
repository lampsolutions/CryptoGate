#!/bin/sh

CONTAINER_BOOT_TIME=$(date +%s)

# on boot of container set current boot time
touch /container_boot_time
echo "${CONTAINER_BOOT_TIME}" > /container_boot_time
