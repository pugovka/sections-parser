# Farpost Vladivostok Sections Parser

Parsing sections from https://www.farpost.ru/vladivostok/

## Requirements

- PHP v5.5.9 or higher.
- [Composer](https://getcomposer.org/download)

## Optional if using [Apache2](https://httpd.apache.org/download.cgi)

- `a2enmod deflate` enable compression
- `a2enmod headers` enable cache headers
- `a2enmod expires` enable expire headers
- `a2enmod rewrite` enable mapping urls to a filesystem path

## Usage in development mode

1. `composer install` install dependencies
2. `php bin/console server:start 127.0.0.1:8000` run dev server

## Usage in development mode with cache and compression

1. `zlib.output_compression = On` set in php.ini
2. `composer install` install dependencies
3. `php bin/console server:start 127.0.0.1:8000 --router=app/router.php` run dev server

## Usage in production mode

1. `export SYMFONY_ENV=prod` set environment to production
2. `composer install --no-dev` install dependencies
3. `php bin/console cache:clear --env=prod --no-debug` clear Symfony cache

## Apache2 with mod_php configuration

- Edit /etc/apache2/sites-enabled/000-default.conf
```
<VirtualHost *:80>
    ServerName domain.tld
    ServerAlias www.domain.tld

    DocumentRoot /var/www/project/web
    <Directory /var/www/project/web>
        AllowOverride All
        Require all granted
        Allow from All
    </Directory>
</VirtualHost>
```