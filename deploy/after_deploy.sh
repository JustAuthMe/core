#!/usr/bin/env bash

read -ra HOSTS <<< $DEPLOY_HOSTS
for HOST in "${HOSTS[@]}"; do
    echo "Enabling artifact on "$HOST
    ssh -oStrictHostKeyChecking=no -i $PK_PATH root@$HOST "chown www-data:www-data -R /var/www/core-$CI_COMMIT_TAG && (rm -r /var/www/core || true) && mv /var/www/core-$CI_COMMIT_TAG /var/www/core"
done