#!/usr/bin/php

sciezka="/home/programigo/domains/programigo.com/base/"

cd $sciezka

php -d memory_limit=-1 "$sciezka"bin/console d:s:d --force
php -d memory_limit=-1 "$sciezka"bin/console d:s:d --force
yes | php -d memory_limit=-1 "$sciezka"bin/console --env=dev doctrine:fixtures:load
echo "Gotowe"