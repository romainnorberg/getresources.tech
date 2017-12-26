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