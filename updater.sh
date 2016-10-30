#!/bin/bash
# by /u/theartmaker

## CONSTANTS ##################################################################
lock_file="/tmp/discord_updater.lock"

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
wall starting php
php /home/devs/stack-guru/bot.bootstrap.php
wall phpret is \'$?\'
rm $lock_file
