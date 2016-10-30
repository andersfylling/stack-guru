#!/bin/bash

git pull

if ps -p $(ps aux | grep '[p]hp bot.bootstrap.php' | awk '{print $2}') > /dev/null
then
    kill $(ps aux | grep '[p]hp bot.bootstrap.php' | awk '{print $2}')
fi

nohup php bot.bootstrap.php &