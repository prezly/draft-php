deps:
	composer install

test: deps
	vendor/bin/phpunit -c phpunit.xml

phpcs:
	vendor/bin/phpcs --standard=phpcs.xml -spn --encoding=utf-8 lib/ --report-width=150

phpcbf:
	vendor/bin/phpcbf --standard=phpcs.xml --encoding=utf-8 lib/
