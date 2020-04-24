#!/usr/bin/env bash

read -ra HOSTS <<< $DEPLOY_HOSTS
for HOST in "${HOSTS[@]}"; do # access each element of array
    echo "Copying artifact to "$HOST
    scp -oStrictHostKeyChecking=no -i $PK_PATH ./build.tar.gz root@$HOST:/var/www/core-$CI_COMMIT_TAG.tar.gz
    ssh -oStrictHostKeyChecking=no -i $PK_PATH root@$HOST "rm /var/www/core-$CI_COMMIT_TAG || true"
    ssh -oStrictHostKeyChecking=no -i $PK_PATH root@$HOST "mkdir /var/www/core-$CI_COMMIT_TAG"
    ssh -oStrictHostKeyChecking=no -i $PK_PATH root@$HOST "tar -xzvf /var/www/core-$CI_COMMIT_TAG.tar.gz -C /var/www/core-$CI_COMMIT_TAG && rm /var/www/core-$CI_COMMIT_TAG.tar.gz"
    echo "Done "$HOST
done