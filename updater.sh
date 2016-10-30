#!/bin/bash

git reset --hard HEAD && git pull && kill $(ps aux | grep '[p]hp bot.bootstrap.php' | awk '{print $2}') && nohup php bot.bootstrap.php &