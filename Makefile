# Run Laravel fully inside Docker. No host PHP or Composer needed.
#
# Why a Makefile: the raw `docker run` invocations are long and repetitive.
# Each target below is a thin wrapper that mounts the project into a short-lived
# container, runs a single command, and disappears. The host stays clean.
#
# Why PHP 8.4: the official `composer:2` image ships with PHP 8.4, so its
# `platform_check.php` pins the app to 8.4. Keeping runtime and install
# PHP versions matched means dependency checks don't trip on boot.

PWD_WIN := $(shell pwd -W 2>/dev/null || pwd)
MOUNT   := -v "$(PWD_WIN):/app" -w /app
COMPOSER_IMG := composer:2
PHP_IMG      := php:8.4-cli
PORT ?= 8000

# composer <cmd> — e.g. `make composer CMD="require laravel/sanctum"`
composer:
	MSYS_NO_PATHCONV=1 docker run --rm $(MOUNT) $(COMPOSER_IMG) $(CMD)

# artisan <cmd> — e.g. `make artisan CMD="route:list"`
artisan:
	MSYS_NO_PATHCONV=1 docker run --rm $(MOUNT) $(PHP_IMG) php artisan $(CMD)

# make serve — boots the app on http://localhost:8000
serve:
	MSYS_NO_PATHCONV=1 docker run --rm -it $(MOUNT) -p $(PORT):$(PORT) $(PHP_IMG) \
		php -S 0.0.0.0:$(PORT) -t public

# make tinker — REPL
tinker:
	MSYS_NO_PATHCONV=1 docker run --rm -it $(MOUNT) $(PHP_IMG) php artisan tinker

# make test — PHPUnit
test:
	MSYS_NO_PATHCONV=1 docker run --rm $(MOUNT) $(PHP_IMG) php artisan test

.PHONY: composer artisan serve tinker test
