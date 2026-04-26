#!/usr/bin/env bash
set -e
php database/init.php
php -S 127.0.0.1:8080 -t public public/router.php
