#!/bin/bash

echo "sopping cluster-demo"

pushd cluster-demo
docker-compose stop
popd
