export

.DEFAULT_GOAL := help

# Get directory name of the root dir and use it as Docker Composes
# `COMPOSE_PROJECT_NAME` (if not set already).
#
# Docker Compose set the project name from the dir as default. But
# then we cannot access it via an environment variable. And we would
# like that to make our docker-compose.yml a bit more dynamic.
export COMPOSE_PROJECT_NAME ?= $(notdir $(abspath $(dir docker-compose.yml)))

_start-timer:
	@rm -rf .reset-info
	@echo "RESET_START_TIME=$$(date +%s)" >> .reset-info

_end-timer:
	@echo "RESET_END_TIME=$$(date +%s)" >> .reset-info

_read-timer:
	@echo ''
	@echo '=============== Timing ================'
	@. ./.reset-info && \
	RESET_TOTAL_SECS=$$((RESET_END_TIME-RESET_START_TIME)) && \
	RESET_MINS=$$(( RESET_TOTAL_SECS / 60 )) && \
  RESET_SECS=$$(( RESET_TOTAL_SECS % 60 )) && \
	echo "Reset took $$RESET_MINS minutes $$RESET_SECS seconds" && \
	echo "(not including waiting for password)"

_common-reset: _docker-pull docker-up php-vendor site-reset import-po search-api-index _end-timer docker-dns-up reset-info _read-timer

reset: _start-timer docker-remove _common-reset ##	 Remove and reset everything

enable-cache: ## Enable hard caches, making your local site act more like prod.
	@(rm -rf web/sites/default/nocache.settings.php || true)
	@$(MAKE) cache-info

disable-cache: ## (DEFAULT) Disabling hard caches, making your local site act less like prod, but easier to work with.
	@(rm -rf web/sites/default/nocache.settings.php || true)
	@(cd web/sites/default && ln -s nocache.settings.php-DISABLED nocache.settings.php)
	@$(MAKE) cache-info

cache-info:
	@echo ''
	@echo '============= Cache info =============='
	@if [ -f web/sites/default/nocache.settings.php ]; then \
		echo "Prod-like Cache is DISABLED \r\nAKA: you will NOT need to use drush cr as often."; \
		echo "You can enable it by running 'make enable-cache'"; \
	else \
	  echo "Prod-like Cache is ENABLED \r\nAKA: the site will behave more like prod\r\nand make development more dificult."; \
	  echo "You can disable it by running 'make disable-cache'"; \
	fi;

docker-up:
	@docker compose up --build -d

docker-dns-up: ## Start DNS, to access the sites on *.docker
	@if command -v dory > /dev/null ; then \
		dory down; \
		dory up; \
		dory restart; \
	else \
		echo "dory command not found - SITE.docker may not work."; \
	fi

docker-remove: ## Removing existing docker containers
	@(docker compose down --remove-orphans || true)
	@(docker compose down --volumes || true)

docker-logs: ## Show docker logs from the containers
	docker compose logs -f --tail=50

php-vendor: ## Install PHP packages, using composer.
	@docker compose up fpm -d
	@docker compose exec fpm sh -c  "\
    echo ' * Waiting php container to be ready' && \
		wait-for-it -t 100 localhost:9000 && \
		git config --global --add safe.directory '*' && \
		echo 'composer installing' && \
		git config --global --add safe.directory '*' && \
		cd /var/www && yes | /usr/bin/composer install --no-interaction"

site-reset: ## Resetting the drupal site.
	@mkdir -p "web/sites/default/files"
	@mkdir -p "web/sites/default/files/translations"
	@docker compose up fpm -d
	@docker compose exec fpm sh -c  "\
    echo 'Site reset' && \
    echo '* Waiting for PHP and DB to be ready..' && \
    wait-for-it -t 100 localhost:9000 && \
    wait-for-it -t 100 db:3306 && \
    echo ' * Run drush deploy' && \
    drush -y deploy && \
		drush user:password testeditor testeditor"

import-po: ## Imports the da.po files. This is also done on reset targets.
	@docker compose up fpm -d
	@docker compose exec fpm sh -c  "\
		cd web && \
		find -L modules \( -path 'modules/contrib' -prune \) -o -name 'da.po' -print0 | xargs -0 -I {} drush locale-import da {}"

import-translations: import-po

reset-info: ## Display information after site has been reset.
	@clear
	@docker compose exec fpm sh -c "\
		echo '============= Drupal info =============' && \
    echo '  Site is available at:' && \
    drush browse && \
		echo '' && \
		echo '  Editor login:' && \
		drush uli --name testeditor && \
		echo '' && \
		echo '  Admin login:' && \
    drush uli"
	@$(MAKE) cache-info

search-api-index: ## Clear + re-index search api index.
	docker-compose exec fpm drush search-api-clear
	docker-compose exec fpm drush search-api-index

checks: ##	 Run the same checks as GitHub actions will do when pushing to PR.
	@docker compose up node fpm -d

	@docker compose exec node sh -c  "\
		npm run format && \
		npm run lint"

	@docker compose exec fpm sh -c  "\
		cd /var/www && \
		echo ' * Updating and validating composer.lock..' && \
		composer update --lock --quiet && \
		composer validate --no-check-all --no-check-publish && \
		echo ' * Running PHPStan..' && \
		phpstan && \
		echo ' * Running PHPCF to fix automatically..' && \
		(phpcbf) && \
		echo ' * Running PHPCS..' && \
		vendor/bin/phpcs"

# We'd like to only pull for images once a day.
# To achive this we only pull if we can't find a ".last-pull-<date>" file that
# contains todays date in its name. If we can't find the file we do the pull and
# and touch the file. This way we only do a daily pull.
LAST_PULL_FILE:=.last-pull-$(shell date +%Y%m%d)
_docker-pull: $(LAST_PULL_FILE)
$(LAST_PULL_FILE):
    # We could not find the daily file, clear out any old files, do the pull
    # and generate the file.
	rm -f .last-pull-*
	docker compose pull
	touch $(LAST_PULL_FILE)

help: ##	 Display a list of the public targets
# Find lines that starts with a word-character, contains a colon and then a
# doublehash (underscores are not word-characters, so this excludes private
# targets), then strip the hash and print.
	@grep -E -h "^\w.*:.*##" $(MAKEFILE_LIST) | sed -e 's/\(.*\):.*##\(.*\)/\1	\2/'
