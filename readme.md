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
- `php bin/phpunit`
- (optionnal) `php bin/phpunit --coverage-clover=coverage.xml`

#### Shorcut (test)
- `sh tests/runTests.sh`

### Local

#### Bootstrap 
- `cp .env.dist .env`
- `cp .env.test.dist .env.test`