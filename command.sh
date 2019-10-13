#!/usr/bin/env bash

composer install -nq --no-scripts
composer dump-env dev

php bin/console d:d:c -nq
php bin/console d:m:m -nq

chmod -R 777 .

exec "apache2-foreground"

