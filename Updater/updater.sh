#!/bin/bash

kill $(ps aux | grep '[p]hp bot.bootstrap.php' | awk '{print $2}')

cd /home/devs/stack-guru && nohup php bot.bootstrap.php &