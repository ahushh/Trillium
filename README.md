# Trillium

Imageboard engine based on the Symfony Components.

Requires PHP &gt;= 5.4

## Installation

- `$ sudo apt-get install php-apc yui-compressor`
- Download archive and unpack it
- Create virtual host with `public/` document root directory
- Make `resources/cache` writable
- `$ chmod +x bin/console`
- Generate `javascript url generator`: `$ bin/console jug`
- Generate assets: `$ bin/console assets`

## TODO:

Too many things. Now almost is ready skeleton only.

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

    git remote add upstream https://github.com/Kilte/view
    git checkout master
    git pull upstream
    git push origin master

Now you can to remove your branch:

    git branch -d awesome-feature
    git push origin :awesome-feature

## LICENSE

The MIT License (MIT)