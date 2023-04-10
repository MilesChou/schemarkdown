#!/usr/bin/make -f

.PHONY: all clean clean-all check test analyse coverage container sqlite

# ---------------------------------------------------------------------

all: test

clean:
	rm -rf ./build
	rm -rf ./generated

clean-all: clean
	rm -rf ./vendor

check:
	php vendor/bin/phpcs

test: clean check
	php vendor/bin/pest

test-fast: clean
	php vendor/bin/pest

coverage: test
	@if [ "`uname`" = "Darwin" ]; then open build/coverage/index.html; fi

container:
	@docker-compose down -v
	@docker-compose up -d
	@docker-compose logs -f

sqlite:
	@sqlite3 tests/Fixtures/sqlite.db < tests/Fixtures/sqlite.sql
