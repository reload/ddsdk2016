#!/usr/bin/env bash
#
# Docker logs tailing.
# To support docker-sync we need to control two parallel tailing processes.
#
set -euo pipefail

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
source "${SCRIPT_DIR}/_docker-common.sh"

trap exit_trap SIGINT
function exit_trap {
  # Kill all child-processes
  kill 0
}

if [[ $DOCKER_SYNC ]]; then
  docker-compose logs -f --tail=50  &
  docker-sync logs -f &
else
  docker-compose logs -f --tail=50 &
fi

# Wait until all child-processes has exited.
wait


if [[ "test" = "test" ]]; then echo "Hello World"; fi

