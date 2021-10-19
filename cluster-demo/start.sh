#!/bin/bash

echo "starting cluster-demo"

pushd cluster-demo
docker-compose up -d
popd

