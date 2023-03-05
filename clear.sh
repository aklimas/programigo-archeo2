#!/usr/bin/php

sciezka="/home/programigo/domains/programigo.com/base/"
repozytorium="aklimas/programigo-base.git"

galaz="main"

cd $sciezka
git pull https://appcodepf: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@github.com/$repozytorium $galaz $galaz

php -d memory_limit=-1 "$sciezka"bin/console d:s:u --force
php -d memory_limit=-1 "$sciezka"bin/console cache:clear
echo "Gotowe"