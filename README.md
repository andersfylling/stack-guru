# stack-guru

stack-guru is a Discord bot for the [/r/nootropics](https://www.reddit.com/r/nootropics) server

Written and maintained by (OUTDATED):

  - [/u/anders_463](https://www.reddit.com/u/anders_463)
  
  
## Setup and common issues on new clones

   https://stackoverflow.com/questions/2424343/undefined-class-constant-mysql-attr-init-command-with-pdo
 * #### Undefined class constant 'MYSQL_ATTR_INIT_COMMAND' with pdo

## How to install
Requirements:
 - php composer
 - php -v 7.1
 - MySQL / MariaDB

When you have cloned the repo, go into ./config/ and copy the file "00_privateConstants_default.php" to "01_privateConstants_default.php". In this file you will set your required database credentials and your discord bot api token.
Use the latest sql file or mwb file to generate the empty database. The file name might be "stack-guru.sql"/"stack-guru.mwb"/"sg.mwb", just look at the date they were last edited and choose the latter one.


## First run
Run the command "php stack-guru.php", you must use linux due to vendor dns issues.
When the bot has started, run `!initiate` to let the bot save the guild object.
You can now run your commands.
