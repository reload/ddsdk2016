#!/usr/bin/env bash
# Performs a full manual upload of dbdump and datadir

set -euo pipefail
IFS=$'\n\t'

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_NAME="ddsdk2016"

if [ $# -lt 2 ]; then
    echo "Syntax $0 <path-to-sqldump> <environment-name>"
    exit
fi

RELATIVE_FILE_PATH=$1
ENVIONMENT_NAME=$2

cleanup() {
	if [[ ! -z ${TMP_PATH} ]]
    then
      sudo rm -rf "${TMP_PATH}"
      cd
	fi
}
trap cleanup EXIT

# Get paths to the dump
IMPORT_FILEPATH="${SCRIPT_DIR}/${RELATIVE_FILE_PATH}"

TMP_PATH="${SCRIPT_DIR}/tmp"
sudo rm -fr "${TMP_PATH}"
mkdir "${TMP_PATH}"
cd "${TMP_PATH}"

git clone git@github.com:reload/db-dump-worker.git
git clone git@github.com:reload/docker-db-datadir-worker.git

cd db-dump-worker/opt/util
./build-and-upload-dump.sh "${IMPORT_FILEPATH}" reload/db-data:${PROJECT_NAME}-${ENVIONMENT_NAME}-latest
cd -
cd docker-db-datadir-worker/opt/datadir-preinit/
./preinit-tag.sh reload/db-data:${PROJECT_NAME}-${ENVIONMENT_NAME}-latest reload/db-datadir:${PROJECT_NAME}-${ENVIONMENT_NAME}-latest ../jobs/general-d8/reset-admin-pass.sql
