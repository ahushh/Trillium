# Trillium

Imageboard engine based on the Symfony Components.

Requires PHP &gt;= 5.4 and MySQL.

## Installation

Assuming you are using Ubuntu 14.04 amd64.

- `sudo apt-get install nginx mysql-server php5-json php5-fpm php5-dev php-apc yui-compressor libzmq-dev libevent-dev php-pear curl php5-gd php5-mysql -y`
- `sudo pecl install zmq-beta`
- `sudo pecl install libevent`
- Run as root: `echo "extension=zmq.so" >> /etc/php5/mods-available/zmq.ini`
- Run as root: `echo "extension=libevent.so" >> /etc/php5/mods-available/libevent.ini`
- Edit `/etc/nginx/site-enabled/default` as following:
```
server {
    listen 80;
    server_name trillium;
    root /home/user/Trillium/public;
    index index.php;

    #site root is redirected to the app boot script
    location = / {
        try_files @site @site;
    }

    #all other locations try other files first and go to our front controller if none of them exists
    location / {
        try_files $uri $uri/ @site;
    }

    #return 404 for all php files as we do have a front controller
    location ~ \.php$ {
        return 404;
    }

    location @site {
        fastcgi_pass   unix:/var/run/php5-fpm.sock;
        include fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME $document_root/index.php;
        #uncomment when running via https
        #fastcgi_param HTTPS on;
    }
```
- Restart nginx and php5-fpm: `sudo service nginx restart && sudo service php5-fpm restart`
- `cd ~ && git clone https://github.com/Kilte/Trillium && cd Trillium`
- `sudo usermod -aG www-data user`
- Make sure `resources/cache` is writable
- Edit the configuration files in resources directory. Changing user and password in %your-env%/mysqli.json should usually be enough.
- Don't forget to change remember\_me key in the security configuration file
- Get composer: `curl -sS https://getcomposer.org/installer | php`
- Install dependencies: `php composer.phar install`
- Switch environment: `bin/console env your_environment` (Available environments: `development`, `production`)
- Load SQL Dump: `bin/console db`
- Dump system settings: `bin/console jss`
- Generate `javascript url generator`: `bin/console jug`
- Generate assets: `bin/console assets`
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
- Write/update tests, if necessary
- Update README.md, if necessary
- Push your branch to origin (git push origin awesome-feature)
- Send pull request
- ???
- PROFIT\!\!\!

Do not forget merge upstream changes:

    git remote add upstream https://github.com/Kilte/Trillium
    git checkout master
    git pull upstream
    git push origin master

Now you can remove your branch:

    git branch -d awesome-feature
    git push origin :awesome-feature

## LICENSE

The MIT License (MIT)
