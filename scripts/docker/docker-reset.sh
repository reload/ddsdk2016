#!/usr/bin/env bash
# Removes all containers and starts up a clean environment
# Invokes site-reset.sh after container startup.

set -euo pipefail
IFS=$'\n\t'
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
SLEEP_BEFORE_RESET=10


echoc () {
	echo -e "\e[38;5;34m$1\e[0m"
}

# Preemptive sudo lease - to let you go out and grab a coffee while the script
# runs.
sudo echo ""

# Clear all running containers.
echoc "*** Removing existing containers" 
docker-compose kill && docker-compose rm -v -f

# Composer silently kills any valid sudo leases, to avoid elevation-exploits in
# scripts - we disable this to make sure we only have to give sudo a password
# once.
echoc "Composer install'ing"
COMPOSER_ALLOW_SUPERUSER=1 composer install

# Start up containers in the background and continue imidiately
echoc "*** Starting new containers" 
docker-compose up --remove-orphans -d

# Sleep while containers are starting up then perform a reset
sleep ${SLEEP_BEFORE_RESET} 

# Perform the drupal-specific reset
echoc "*** Resetting Drupal"
"${SCRIPT_DIR}/site-reset.sh"

# Done, bring the background docker-compose logs back into foreground
echoc "*** Done, watching logs"
docker-compose logs -f

