#!/usr/bin/env bash

chmod 600 $PK_PATH

read -ra HOSTS <<< $DEPLOY_HOSTS
for HOST in "${HOSTS[@]}"; do # access each element of array
    echo "Copying artifact to "$HOST
    scp -oStrictHostKeyChecking=no -i $PK_PATH ./build.tar.gz root@$HOST:$ROOT_PATH-$CI_COMMIT_TAG.tar.gz
    ssh -oStrictHostKeyChecking=no -i $PK_PATH root@$HOST "rm -r $ROOT_PATH-$CI_COMMIT_TAG || true"
    ssh -oStrictHostKeyChecking=no -i $PK_PATH root@$HOST "mkdir $ROOT_PATH-$CI_COMMIT_TAG"
    ssh -oStrictHostKeyChecking=no -i $PK_PATH root@$HOST "tar -xzvf $ROOT_PATH-$CI_COMMIT_TAG.tar.gz -C $ROOT_PATH-$CI_COMMIT_TAG && rm $ROOT_PATH-$CI_COMMIT_TAG.tar.gz"
    echo "Done "$HOST
done