#!/bin/bash

## CONSTANTS ##################################################################
lock_file="/tmp/discord_updater.lock"
log_file="~/discord_autoupdate.log"
php_log_file="/var/log/php_errors.log"


## CODE #######################################################################

# Lock
[ -f $lock_file ] && exit
touch $lock_file

# Stop bot
sudo systemctl stop stackguru.service

# Change working directory
cd ~/stack-guru

# Update source code
git reset --hard
git pull

# Update dependencies
composer install --no-dev --optimize-autoloader

# Unlock
rm $lock_file

# Log update
echo "`date` | updated!" >> $log_file

sleep 1

# Start bot
sudo systemctl start stackguru.service
