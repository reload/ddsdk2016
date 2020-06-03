# dds.dk - Det Danske Spejderkorps
A Drupal 8 project hosted on platform.sh

* Confluence space: [DDS](https://reload.atlassian.net/wiki/spaces/DDS)
* Technical [Documentation](documentation/index.md)

# Development

## Prerequisites
* Docker + docker-compose installed

### On a Mac
Docker for Mac, docker-sync and Dory is recommended. 
More info [on Confluence](https://reload.atlassian.net/wiki/spaces/RW/pages/153288705/Docker+for+Mac)

### On Linux
If you have docker and docker-compose installed, and a way to resolve dns for the container, you are all set. See https://reload.atlassian.net/wiki/spaces/RW/pages/791543813/Docker+DNS+p+Linux for DNS.

## Branching
"Master/feature branch", see [Confluence](https://reload.atlassian.net/wiki/spaces/RW/pages/744882179/Branching)

## Deployment
Site is hosted on platform.sh, deployments to production are made by pushing to master.

### Scripted (recommended)
From the root of the project, execute `make reset`. This will pull down any running project containers, reset any state and start up with a fresh production database.

### Manually
(see scripts/docker/docker-reset.sh and scripts/docker/docker-site.sh)

The following are the basic steps that should bring your site up.
* Ensure docker-sync / dns-gen / dory and any other support-containers are running 
* Stop any running instances of the project-containers
* Run `docker-compose up` from the root of the project
* Wait for the `db` and `fpm` container to report as healthy (`docker-compose ps`)
* Run `docker-compose exec -w /var/www fpm composer install`
* Run `docker-compose exec fpm drush cr`
* Run `docker-compose exec fpm drush -y updb -y` to run update-hooks
* Run `docker-compose exec fpm drush -y cim` to import configuration
* Run `docker-compose exec fpm drush -y search-api-clear` clear the search index to bring it and the site in sync
* Access your dockerhost on port 80 (eg. http://local.docker or localhost)

### Customizations
You can disable docker-sync by adding 
```
NO_DOCKER_SYNC=1
```
To .env in the root of the project. Use this to eg. test the setup for linux-
uses when you've made a change that introduces changes that could get masked
by docker-sync.
