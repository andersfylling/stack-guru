# stack-guru

stack-guru is a Discord bot for the [/r/nootropics](https://www.reddit.com/r/nootropics) server

Written and maintained by:

  - [/u/anders_463](https://www.reddit.com/u/anders_463)
  - [/u/s1kx](https://www.reddit.com/u/s1kx)
  
Thanks to, for contribution:

  - [/u/theartmaker](https://www.reddit.com/u/theartmaker)
  
  
## Requirements

 - PHP version 7.1 and higher
 - Composer (installer for php packages)
 - MySQL
 - php.ini extensions:
 - - pdo_mysql
 - - mbstring

## How to install
When you have cloned the repo, go into ./config/ and copy the file "00_privateConstants_default.php" to "01_privateConstants_default.php" (name it whatever you want it just needs to match '01_*.php'). In this file you will set your required database credentials and your discord bot api token.
Use the latest sql file or mwb file to generate the empty database. The file name might be "stack-guru.sql"/"stack-guru.mwb"/"sg.mwb", just look at the date they were last edited and choose the latter one.

## First run
Run the command "php stack-guru.php", you must use linux due to vendor dns issues.
You can now run your commands.
