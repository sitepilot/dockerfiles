# Dockerfiles

This repository contains Docker images used for development and our managed hosting platform.

## Images


|Name|Tags|Software|Used by|
|----|----|--------|-------|
|[Runtime](./runtime)|8.0, 7.4|Openlitespeed, PHP 7.4/8.0, Composer, WPCLI, NodeJS, OpenSSH Server|[Managed Hosting Platform](https://sitepilot.io/)
|[Operator](./operator)|latest|Restic, Rsync, OpenSSH Client, MySQL Client|[Stack Package](https://github.com/sitepilot/stack/)
