# Development checks of Exponential. See doc/features/6.0/quality-checks.md.
#
#   make devtools          install PHP_CodeSniffer and PHPStan into .devtools/
#   make hooks             run the checks of .githooks/ on every commit and push
#   make check             syntax, manifest, code style and PHPStan, as CI does

BASE ?= origin/main
DEVTOOLS = .devtools/vendor/bin

.PHONY: devtools hooks check lint lint80 lint80-all manifest-check manifest-fix phpcs phpstan phpstan-baseline

# PHP_CodeSniffer and PHPStan from composer.dev.json, never into vendor/
devtools:
	COMPOSER=composer.dev.json composer install --no-interaction --no-progress

# Use the hooks of .githooks/ for this clone
hooks:
	git config core.hooksPath .githooks
	@echo "Git hooks enabled: pre-commit, commit-msg, pre-push"

check: lint manifest-check phpcs phpstan

# PHP syntax of the PHP files changed since $(BASE)
lint:
	@for file in $$(git diff --name-only --diff-filter=ACMR $$(git merge-base $(BASE) HEAD) -- '*.php'); do \
		php -l "$$file" >/dev/null || exit 1; \
	done; echo "lint: no syntax errors"

# PHP 8.0 syntax of the PHP files changed since $(BASE); PHP80 names the binary
# (PHP 8.0 is the oldest supported version, doc/bc/6.0/php-8.0-support.md)
PHP80 ?= $(or $(EXP_PHP80),$(shell command -v php8.0 2>/dev/null || command -v php80 2>/dev/null || ls /opt/plesk/php/8.0/bin/php 2>/dev/null),php8.0)
lint80:
	@for file in $$(git diff --name-only --diff-filter=ACMR $$(git merge-base $(BASE) HEAD) -- '*.php'); do \
		$(PHP80) -l "$$file" >/dev/null || exit 1; \
	done; echo "lint80: no PHP 8.0 syntax errors"

# PHP 8.0 syntax of every PHP file in git, one php -l per file (several files per
# call only work from PHP 8.3 on), 8 at a time; what CI runs
lint80-all:
	@git ls-files -z '*.php' | xargs -0 -n 1 -P 8 $(PHP80) -l 2>&1 | grep -v '^No syntax errors' | \
		{ if grep .; then exit 1; else echo "lint80-all: every PHP file is valid PHP 8.0"; fi; }

# share/filelist.md5 against the files
manifest-check:
	php bin/php/checkmanifest.php --all

# Rewrite stale checksums and add missing files to share/filelist.md5
manifest-fix:
	php bin/php/checkmanifest.php --fix

# Code style of the PHP files changed since $(BASE); only new violations fail
phpcs:
	php bin/php/checkcodestyle.php --base=$(BASE)

# Static analysis; only findings missing from phpstan-baseline.neon fail
phpstan:
	$(DEVTOOLS)/phpstan analyse -c phpstan.neon.dist --memory-limit=4G

# Accept all current findings as the new baseline
phpstan-baseline:
	$(DEVTOOLS)/phpstan analyse -c phpstan.neon.dist --memory-limit=4G --generate-baseline=phpstan-baseline.neon
