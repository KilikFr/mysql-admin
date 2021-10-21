# MySQL Admin

A tool to handle MySQL replication (and more ?).

## Installation

! IMPORTANT !

you should map (or save) your config/external directory (because it will contain Key to encrypt/decrypt stored passwords).

## How to use

* [compare 2 servers (one master with one slave)](doc/console-app-diff-tables)

## To work on this project (with a demo cluster)

```shell
cd ~/PhpstormProjects
git clone git@github.com:KilikFr/mysql-admin.git
cd mysql-admin
./cluster-demo/start.sh
make upgrade
./cluster-demo/init-replication.sh
```

## Work this fixtures

```shell
touch .fixtures
make fixtures
```

## Work this symfony console

```shell
make php
./bin/console
```
