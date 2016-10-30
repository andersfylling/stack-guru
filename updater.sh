#!/bin/bash
# by /u/theartmaker

## CONSTANTS ##################################################################
lock_file="/tmp/discord_updater.lock" # lock file path
log_file="/home/devs/discord_autoupdate.log"

## CODE #######################################################################

if [ -f $lock_file ]; then
    exit
fi

echo "lock" > $lock_file

php_pid=$(ps aux | grep '[p]hp bot.bootstrap.php' | awk '{print $2}')

kill -9 $php_pid

if [ "$?" == "1" ]; then
    exit
fi

git -C /home/devs/stack-guru reset --hard
git -C /home/devs/stack-guru pull

rm $lock_file

echo "`date` | updated!" > $log_file

sleep 1
cd /home/devs/stack-guru
php bot.bootstrap.php
