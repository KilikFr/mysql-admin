#!/bin/bash

echo "Down and delete cluster-demo"

pushd cluster-demo
docker-compose down --volumes --remove-orphans
popd
