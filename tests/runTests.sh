#!/usr/bin/env bash

export APP_ENV=test;

php bin/console hautelook:fixtures:load --env=test -n -vv
bin/console search:clear -i sites --env=test -v
bin/console search:import -i sites --env=test -v

sleep 5 # leave time to Algolia index

php bin/phpunit