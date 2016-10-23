#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

# Put cron file into function.
# See docker-compose.yml for the source of the file.
if [ -r /root/tmp.drupal-cron ]; then
    # We do a cp to make sure the file is owned by root (the .conf
    # file is mounted into the volume and is owned by the outside user
    # -- besides that the . in the name makes it ignore by crond).
    cp /root/tmp.drupal-cron /etc/cron.d/drupal-cron
    chown root:root /etc/cron.d/drupal-cron
fi
