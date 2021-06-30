.PHONY: code-style-check code-style-fix phpcs phpcbf test

test: vendor
	vendor/bin/phpunit -c phpunit.xml

code-style-check: phpcs

code-style-fix: phpcbf

vendor: composer.json
	composer install
	touch vendor

phpcs: vendor
	vendor/bin/phpcs --standard=phpcs.xml -spn --encoding=utf-8 lib/ --report-width=150

phpcbf: vendor
	vendor/bin/phpcbf --standard=phpcs.xml --encoding=utf-8 lib/

