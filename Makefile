#
# DDS.DK Makefile

# =============================================================================
# MAIN COMMAND TARGETS
# =============================================================================
.DEFAULT_GOAL := help

help: ## Display a list of the public targets
# Find lines that starts with a word-character, contains a colon and then a
# doublehash (underscores are not word-characters, so this excludes private
# targets), then strip the hash and print.
	@grep -E -h "^\w.*:.*##" $(MAKEFILE_LIST) | sed -e 's/\(.*\):.*##\(.*\)/\1	\2/'

reset: _reset-container-state ## Stop all containers, reset their state and start up again.

up:  ## Take the whole environment up without altering the existing state of the containers.
	docker-compose up -d --remove-orphans

stop: ## Stop all containers without altering anything else.
	docker-compose stop

logs: ## Follow docker logs from the containers
	docker-compose logs -f --tail=50

status: ## Show status of the environment
	docker-compose ps
	docker-compose top

# Use the current list of plantuml files to define a list of required pngs.
diagrams = $(patsubst %.plantuml,%.png,$(wildcard documentation/diagrams/*.plantuml))

# The default docs target depends on all png-files
docs: $(diagrams) ## Build plantuml-diagrams for the documentation

# Static pattern that maps between diagram pngs and plantuml-files.
$(diagrams): documentation/diagrams/%.png : documentation/diagrams/%.plantuml
	@echo '$< -> $@'
	rm -f $@
	cat $< | docker run --rm -i think/plantuml -tpng > $@

# =============================================================================
# HELPERS
# =============================================================================
# These targets are usually not run manually.

# Fetch and replace updated containers and db-dump images and run composer install.
_reset-container-state: _docker-pull
	./scripts/docker/docker-reset.sh

_ensure-php:
	docker-compose up -d --no-deps fpm

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
	docker-compose pull
	touch $(LAST_PULL_FILE)

.PHONY: default help reset up stop logs status docs _reset-container-state _ensure-php _docker-pull

