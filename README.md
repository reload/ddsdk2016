# dds.dk - Det Danske Spejderkorps
A Drupal 8 project powered by composer and docker.

## Getting up and running
Prerequisite: 
* Docker + docker-compose installed
* Access to https://hub.docker.com/r/reload/db-data or a databasedump

Then do either a manual or scripted reset.

### Manually
* Run docker-compose up from the root of the project
* Run `docker-compose run drush cim -y` 
* Run `docker-compose run drush updb -y` to run update-hooks
* Run `docker-compose run drush cr`
* Access your dockerhost on port 80 (eg. http://local.docker or localhost)

### Scripted
From the root of the project, execute `scripts/docker/docker-reset.sh`

