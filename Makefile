COMPOSER_VERSION := 2.5.8
PHPUNIT_VERSION := 10.2.4
PHPSTAN_VERSION := 1.10.25
PHP_CS_FIXER_VERSION := 3.21.1
PHPUNIT_GLOBALS_VERSION := 3.1.2

SRC_FILES := $(shell find src test -name '*.php')


## Manage Dependencies
.PHONY: install
install: composer.lock vendor

.PHONY: update
update: tools/composer
	php tools/composer update

composer.lock vendor: tools/composer
	php tools/composer install


## PHP CS Fixer
.PHONY: check-style
check-style: tools/php-cs-fixer
	php tools/php-cs-fixer fix -v --ansi --dry-run --diff

.PHONY: fix-style
fix-style: tools/php-cs-Fixer
	php tools/php-cs-fixer fix -v --ansi


## PHPStan
.PHONY: static-check
static-check: tools/phpstan
	php tools/phpstan analyze --ansi


## PHPUnit
.PHONY: test
test: tools/phpunit tools/phpunit.d/zalas-phpunit-globals-extension.phar
	php tools/phpunit --colors=always

.PHONY: coverage
coverage: coverage.xml

coverage.xml: tools/phpunit tools/phpunit.d/zalas-phpunit-globals-extension.phar $(SRC_FILES)
	php tools/phpunit --coverage-clover=coverage.xml


## Tool Aliases
.PHONY: composer
composer: tools/composer

.PHONY: php-cs-fixer
php-cs-fixer: tools/php-cs-fixer

.PHONY: phpstan
phpstan: tools/phpstan

.PHONY: phpunit
phpunit: tools/phpunit


## Download Tools
tools:
	@mkdir -p tools

tools/phpunit.d:
	@mkdir -p tools/phpunit.d

tools/composer: tools/composer-installer | tools
	@php tools/composer-installer --version=${COMPOSER_VERSION} --filename=tools/composer

tools/composer-installer: | tools
	@curl -s -o tools/composer-installer https://getcomposer.org/installer
	@echo "`curl -s https://composer.github.io/installer.sig`  tools/composer-installer" | sha384sum -c -

tools/php-cs-fixer: | tools
	@curl -s -L -o tools/php-cs-fixer https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/v$(PHP_CS_FIXER_VERSION)/php-cs-fixer.phar

tools/phpstan: | tools
	@curl -s -L -o tools/phpstan https://github.com/phpstan/phpstan/releases/download/$(PHPSTAN_VERSION)/phpstan.phar

tools/phpunit: | tools
	@curl -s -L -o tools/phpunit https://phar.phpunit.de/phpunit-$(PHPUNIT_VERSION).phar

tools/phpunit.d/zalas-phpunit-globals-extension.phar: | tools/phpunit.d
	@curl -s -L -o tools/phpunit.d/zalas-phpunit-globals-extension.phar https://github.com/jakzal/phpunit-globals/releases/download/v$(PHPUNIT_GLOBALS_VERSION)/zalas-phpunit-globals-extension.phar


## Cleanup
.PHONY: clean
clean:
	rm -rf tools vendor composer.lock coverage.xml
