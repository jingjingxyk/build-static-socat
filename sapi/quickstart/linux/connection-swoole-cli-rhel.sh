#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

cd ${__DIR__}

docker exec -it swoole-cli-rhel-dev bash
<<<<<<< HEAD

=======
>>>>>>> build_native_php
