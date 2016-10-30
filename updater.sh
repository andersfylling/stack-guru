#!/bin/bash

git pull

pidID = ps aux | grep '[p]hp bot.bootstrap.php' | awk '{print $2}'
if ps -p $pidID > /dev/null
then
    kill $pidID
fi

nohup php bot.bootstrap.php &