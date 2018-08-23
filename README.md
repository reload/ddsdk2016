# dds.dk - Det Danske Spejderkorps
A Drupal 8 project powered by composer and docker.

## Getting up and running
Prerequisite: 
* Docker + docker-compose installed
* Access to https://hub.docker.com/r/reload/db-data or a databasedump

Then do either a manual or scripted reset.

### Manually
* Run docker-compose up from the root of the project
* Run `docker-compose exec fpm sh -c 'cd $(git rev-parse --show-toplevel) && composer install'`
* Run `docker-compose exec fpm drush cim -y`
* Run `docker-compose exec fpm drush updb -y` to run update-hooks
* Run `docker-compose exec fpm drush cr`
* Access your dockerhost on port 80 (eg. http://local.docker or localhost)

### Scripted
From the root of the project, execute `scripts/docker/docker-reset.sh`

