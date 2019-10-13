#!/usr/bin/env bash

php bin/console d:d:c -nq
php bin/console d:m:m -nq

chmod -R 777 .

exec "apache2-foreground"

