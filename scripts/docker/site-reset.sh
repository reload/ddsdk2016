#!/usr/bin/env bash
# Prepares a site for development.
# The assumption is that we're bootstrapping with a database-dump from
# another environment, and need to import configuration and run updb.

set -euo pipefail
IFS=$'\n\t'
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Chmod to 777 if the file is not owned by www-data
cd "${SCRIPT_DIR}/../../"
find web/sites/default/files \! -uid 33  \! -print0 -name .gitkeep | sudo xargs -0 chmod 777

# Make sites/default read-only and executable
sudo chmod 555 web/sites/default
time docker-compose exec fpm sh -c  "\
  echo ' * Waiting php container to be ready' \
  && wait-for-it -t 60 localhost:9000 \
  && echo ' * Waiting for the database to be available' \
  && wait-for-it -t 60 db:3306 \
  && echo 'composer installing' \
  && cd /var/www && composer install && cd /var/www/web \
  echo 'Site reset' && \
  echo ' * Rebuilding cache' && \
  setuser www-data drush -y cache-rebuild && \
  echo ' * Update database' && \
  setuser www-data drush -y updatedb && \
  echo ' * Entity update' && \
  setuser www-data drush -y entup && \
  echo ' * Import configuration' && \
  setuser www-data drush -y config-import --preview=diff && \
  echo ' * Cache rebuild' && \
  setuser www-data drush cache-rebuild && \
  echo ' * Clearing search-api' && \
  setuser www-data drush search-api-clear
  "
