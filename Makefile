deps:
	composer install

test: deps
	vendor/bin/phpunit -c phpunit.xml