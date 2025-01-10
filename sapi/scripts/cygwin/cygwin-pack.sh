#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__PROJECT__}

cd ${__PROJECT__}/
ldd ${__PROJECT__}/bin/php.exe

cd ${__PROJECT__}
APP_VERSION=$(${__PROJECT__}/bin/php.exe | head -n 1 | awk '{ print $2 }')
NAME="php-v${APP_VERSION}-cygwin-x64"

test -d /tmp/${NAME} && rm -rf /tmp/${NAME}
mkdir -p /tmp/${NAME}

cd ${__PROJECT__}/
ldd ${__PROJECT__}/bin/php.exe | grep -v '/cygdrive/' | awk '{print $3}' | xargs -I {} cp {} /tmp/${NAME}/

cp -f ${__PROJECT__}/bin/LICENSE /tmp/${NAME}/
# cp -f ${__PROJECT__}/bin/credits.html /tmp/${NAME}/

cd /tmp/${NAME}/

zip -r ${__PROJECT__}/${NAME}.zip .

cd ${__PROJECT__}
