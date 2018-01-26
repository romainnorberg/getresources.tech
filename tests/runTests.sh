#!/usr/bin/env bash

export APP_ENV=test;

php bin/console doctrine:schema:drop --env=test --force
php bin/console doctrine:schema:create --env=test -v
php bin/console doctrine:schema:update --dump-sql --force --env=test -v
php bin/console doctrine:schema:validate --env=test
php bin/console hautelook:fixtures:load --env=test -n -vv
bin/console search:clear -i sites --env=test -v
bin/console search:import -i sites --env=test -v

sleep 5 # leave time to Algolia index

php bin/phpunit