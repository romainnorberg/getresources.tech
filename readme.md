[![Build Status](https://travis-ci.org/romainnorberg/getresources.tech.svg?branch=master)](https://travis-ci.org/romainnorberg/getresources.tech)
[![codecov](https://codecov.io/gh/romainnorberg/getresources.tech/branch/master/graph/badge.svg)](https://codecov.io/gh/romainnorberg/getresources.tech)
[![BCH compliance](https://bettercodehub.com/edge/badge/romainnorberg/getresources.tech?branch=master)](https://bettercodehub.com/)

***

### Requirements:
- Yarn (`brew install yarn`)

### Web Assets

doc: http://symfony.com/doc/current/frontend.html

**# compile assets once**: `./node_modules/.bin/encore dev`

**# recompile assets automatically when files change**: `./node_modules/.bin/encore dev --watch`

**# compile assets, but also minify & optimize them**: `./node_modules/.bin/encore production`

**# shorter version of the above 3 commands**: 
- `yarn run encore dev`
- `yarn run encore dev --watch`
- `yarn run encore production`

### Tests

#### Fixtures:
- `APP_ENV=test php bin/console hautelook:fixtures:load -n -vv`

#### Run

Tests can be run with command 

- `./vendor/phpunit/phpunit/phpunit --testdox`
- (optionnal) `./vendor/phpunit/phpunit/phpunit --coverage-clover=coverage.xml`

or for one file : 

`./vendor/phpunit/phpunit/phpunit tests/Controller/DefaultControllerTest.php --testdox`

#### Shorcut (test)
- `sh tests/runTests.sh`

### Local

#### Bootstrap 
- `cp .env.dist .env`
- `cp .env.test.dist .env.test`

#### Running
- `redis-server --port 6380 --requirepass 'secret'`
- `yarn run encore dev --watch`
- `php -d variables_order=EGPCS -S localhost:8000 -t public`

### Migration
- `bin/console doctrine:migrations:diff`
- `bin/console doctrine:migrations:migrate --write-sql="src/Migrations/update.sql"`

### Hosting
Hosted on clever-cloud.com

#### Env var
- CC_POST_BUILD_HOOK=sh clevercloud/hook/post_build_hook.sh