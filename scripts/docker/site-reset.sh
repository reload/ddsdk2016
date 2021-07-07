#!/usr/bin/env bash
# Prepares a site for development.
# The assumption is that we're bootstrapping with a database-dump from
# another environment, and need to import configuration and run updb.

set -euo pipefail
IFS=$'\n\t'
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
FILES=("web/sites/default/files" "private")

# Chmod to 777 if the file is not owned by www-data
cd "${SCRIPT_DIR}/../../"
mkdir -p "${FILES[@]}"
find "${FILES[@]}" \! -uid 33  \! -print0 -name .gitkeep | sudo xargs -0 chmod 777

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
  drush -y cache-rebuild && \
  echo ' * Update database' && \
  drush -y updatedb && \
  echo ' * Import configuration' && \
  drush -y config-import --preview=diff && \
  echo ' * Cache rebuild' && \
  drush cache-rebuild && \
  echo ' * Clearing search-api' && \
  drush search-api-clear &&
  echo ' * preparing admin login' && \
  drush uli
  "

printf "\nYou may now visit the site at http://ddsdk.docker (if you are running dory)."
