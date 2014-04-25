# Trillium

Imageboard engine based on the Symfony Components.

Requires PHP &gt;= 5.4

## Installation

- `$ sudo apt-get install php-apc yui-compressor libzmq-dev libevent-dev`
- `$ sudo pecl install zmq`
- `$ sudo pecl install libevent`
- Download archive and unpack it
- Create virtual host with `public/` document root directory
- Make `resources/cache` writable
- `$ chmod +x bin/console`
- Edit the configuration files in resources directory
- Don't forget to change remember\_me key in the security configuration file
- Create the MySQL database
- Switch environment: `bin/console env your_environment` (Available environments: `development`, `production`)
- Note: check out, that database is exists for current environment, otherwise you cannot to run console command.
- Load SQL Dump: `$ bin/console db`
- Dump system settings: `$ bin/console jss`
- Generate `javascript url generator`: `$ bin/console jug`
- Generate assets: `$ bin/console assets`
- Run WebSocket server: `bin/ws` (Logs are available in the resources/logs/ws-%your\_env%.log file)
- To get access to the control panel, login with the following credentials:
    - Username: root
    - Password: 123456

## TODO:

- Too many things.
- See TODO.md to get the current tasks.

## Contributing

- Fork it
- Create your feature branch (git checkout -b awesome-feature)
- Make your changes
- Write/update tests, if it necessary
- Update README.md, if it necessary
- Push your branch to origin (git push origin awesome-feature)
- Send pull request
- ???
- PROFIT\!\!\!

Do not forget merge upstream changes:

    git remote add upstream https://github.com/Kilte/Trillium
    git checkout master
    git pull upstream
    git push origin master

Now you can to remove your branch:

    git branch -d awesome-feature
    git push origin :awesome-feature

## LICENSE

The MIT License (MIT)