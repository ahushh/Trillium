# Trillium

Imageboard engine based on the Symfony Components.

Requires PHP &gt;= 5.4

## Installation

- `$ sudo apt-get install php-apc yui-compressor`
- Download archive and unpack it
- Create virtual host with `public/` document root directory
- Make `resources/cache` writable
- `$ chmod +x bin/console`
- Edit the configuration files in resources directory
- Don't forget to change remember\_me key in the security configuration file
- Generate `javascript url generator`: `$ bin/console jug`
- Dump system settings: `$ bin/console jss`
- Generate assets: `$ bin/console assets`
- Load SQL Dump: `$ bin/console db`
- Switch environment: `bin/console env your_environment` (Available environments: `development`, `production`)

## TODO:

Too many things.

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