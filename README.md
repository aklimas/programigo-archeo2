# programigo-base

### Wymagania
## PHP: >=8.1
## Symfony 6.2

# Baza podpięta


###Start systemu poprzez (przy starciu projektu):

composer install

php bin/console doctrine:schema:drop --force \
php bin/console doctrine:schema:update --force \
php bin/console --env=dev doctrine:fixtures:load

##Weryfikacjia kodu \
vendor/bin/phpstan analyse -l 0 src

##Układanie kodu
vendor/bin/php-cs-fixer fix src

##Odświeżanie get i set
php bin/console make:entity --regenerate





