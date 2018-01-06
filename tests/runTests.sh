#!/usr/bin/env bash

APP_ENV=test php bin/console hautelook:fixtures:load -n -vv
APP_ENV=test bin/console search:clear -i sites -v
APP_ENV=test bin/console search:import -i sites -v

sleep 5 # leave time to Algolia index

php bin/phpunit