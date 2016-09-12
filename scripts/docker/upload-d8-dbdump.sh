#!/usr/bin/env bash
# Performs a full manual upload of dbdump and datadir

set -euo pipefail
IFS=$'\n\t'

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_NAME="ddsdk2016"

if [ $# -lt 1 ]; then
    echo "Syntax $0 <path-to-sqldump>"
    exit
fi

cleanup() {
	if [[ ! -z ${TMP_PATH} ]]
    then
      rm -rf "${TMP_PATH}"
      cd
	fi
}
trap cleanup EXIT

# Get paths to the dump
IMPORT_FILEPATH="${SCRIPT_DIR}"/$1

TMP_PATH="${SCRIPT_DIR}/tmp"
rm -fr "${TMP_PATH}"
mkdir "${TMP_PATH}"
cd "${TMP_PATH}"

git clone git@github.com:reload/db-dump-worker.git
git clone git@github.com:reload/docker-db-datadir-worker.git

cd db-dump-worker/opt/util
./build-and-upload-dump.sh "${IMPORT_FILEPATH}" reload/db-data:${PROJECT_NAME}-latest
cd -
cd docker-db-datadir-worker/opt/datadir-preinit/
./preinit-tag.sh reload/db-data:${PROJECT_NAME}-latest reload/db-datadir:${PROJECT_NAME}-latest ../jobs/general-d8/reset-admin-pass.sql
