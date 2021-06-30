.PHONY: code-style-check test

test: vendor
	vendor/bin/phpunit -c phpunit.xml

code-style-check: vendor
	php -d memory_limit=2048M vendor/bin/ecs check

vendor: composer.json
	composer install
	touch vendor

