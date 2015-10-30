#!/usr/bin/env bash
echo "coverage = $COVERAGE, slug = $TRAVIS_REPO_SLUG, commit = $TRAVIS_COMMIT"
if [ -n "$COVERAGE" ]; then
#	rm -rf .git
	cd ~/builds/ss/shop
	wget https://scrutinizer-ci.com/ocular.phar
	pwd
	ls -al ~
	ls -al ~/builds
	ls -al ~/builds/ss
	ls -al ~/builds/ss/shop
	php ocular.phar code-coverage:upload -v --format=php-clover ~/builds/ss/shop/coverage.xml
fi
