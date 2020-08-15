#!/usr/bin/env bash

export COMPOSE_INTERACTIVE_NO_CLI=1

COMPOSER_COMMAND="$*"

cd /var/www

sudo docker-compose run -w /var/www/app --rm composer composer $COMPOSER_COMMAND

echo "Replacing autoloader with correct reference..."

[[ -f /var/www/app/vendor/autoload.php ]] && rm /var/www/app/vendor/autoload.php

echo "Removing stale VendorFiles..."

sudo docker-compose exec php rm -rf /php-vendor

echo "Moving updated files to Container..."

sudo docker cp /var/www/app/vendor php:/php-vendor

echo "Executing ComposerScripts for Laravel - cache:clear"

sudo docker-compose exec php /var/www/app/bin/console cache:clear

echo "Executing ComposerScripts for Laravel - assets:install"

sudo docker-compose exec php /var/www/app/bin/console assets:install /var/www/app/public
