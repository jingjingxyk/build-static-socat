#!/bin/bash


__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=${__DIR__}

if [ ! -f ${__DIR__}/prepare.php ] ; then
  echo 'no found prepare.php'
  exit 0
fi

cd ${__PROJECT__}

set -x
# shellcheck disable=SC2034
OS=$(uname -s)
# shellcheck disable=SC2034
ARCH=$(uname -m)

case $OS in
'Linux')
  OS="linux"
  ;;
'Darwin')
  OS="macos"
  ;;
*)
  echo '暂未配置的 OS '
  exit 0
  ;;

esac


IN_DOCKER=0
WITH_DOWNLOAD_BOX=0
WITH_ALL_DEPENDENCIES_CONTAINER=0

# 配置系统仓库  china mirror
WITH_MIRROR='china'
WITH_MIRROR=''

OPTIONS=''

while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16,198.18.0.0/15,169.254.0.0/16"
    NO_PROXY="${NO_PROXY},127.0.0.1,localhost"
    NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com"
    export NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com"
    ;;
  --download_box)
    WITH_DOWNLOAD_BOX=1
    OPTIONS="${OPTIONS} --with-dependency-graph=1 --without-docker=1 --with-skip-download=1 "
    ;;
  --all_dependencies)
    WITH_ALL_DEPENDENCIES_CONTAINER=1
    OPTIONS="${OPTIONS} --without-docker=1  "
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done


if [ "$OS" = 'linux' ] ; then
    if [ -f /.dockerenv ]; then
        IN_DOCKER=1
        number=$(which flex  | wc -l)
        if test $number -eq 0 ;then
        {
            if [ "$MIRROR" = 'china' ] ; then
                sh sapi/quickstart/linux/alpine-init.sh --mirror china
            else
                sh sapi/quickstart/linux/alpine-init.sh
            fi
        }
        fi
        git config --global --add safe.directory ${__PROJECT__}
    else
        # docker inspect -f {{.State.Running}} download-box-web-server
        if [ "`docker inspect -f {{.State.Running}} swoole-cli-builder`" = "true" ]; then
            echo " build container is running "
          else
            echo " build container no running "
        fi
    fi
fi

if [ "$OS" = 'macos' ] ; then
  number=$(which flex  | wc -l)
  if test $number -eq 0 -o -f sapi/quickstart/macos/homebrew-init.sh ;then
  {
        if [ "$MIRROR" = 'china' ] ; then
            bash sapi/quickstart/macos/homebrew-init.sh --mirror china
        else
            bash sapi/quickstart/macos/homebrew-init.sh
        fi
  }
  fi
fi

bash sapi/quickstart/clean-folder.sh

if [ ! -f "${__PROJECT__}/bin/runtime/php" ] ;then
      if [ "$MIRROR" = 'china' ] ; then
          bash sapi/quickstart/setup-php-runtime.sh --mirror china
      else
          bash sapi/quickstart/setup-php-runtime.sh
      fi
fi

export PATH="${__PROJECT__}/bin/runtime:$PATH"
alias php="php -d curl.cainfo=${__PROJECT__}/bin/runtime/cacert.pem -d openssl.cafile=${__PROJECT__}/bin/runtime/cacert.pem"

php -v

export COMPOSER_ALLOW_SUPERUSER=1
composer config -g repos.packagist composer https://packagist.org
# composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
if [ "$MIRROR" = 'china' ]; then
    composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/
fi
# composer suggests --all
# composer dump-autoload

composer update  --optimize-autoloader
composer config -g --unset repos.packagist


# 可用配置参数
# --with-swoole-pgsql=1
# --with-libavif=1
# --with-global-prefix=/usr/local/swoole-cli
# --with-dependency-graph=1
# --with-web-ui
# --conf-path="./conf.d.extra"
# --without-docker=1
# @macos
# --with-build-type=dev
# --with-skip-download=1
# --with-http-proxy=http://192.168.3.26:8015
# --with-override-default-enabled-ext=0
# --with-php-version=8.2.11
# --with-c-compiler=[gcc|clang] 默认clang
# --with-download-mirror-url=https://php-cli.jingjingxyk.com/


if [ ${IN_DOCKER} -eq 1 ] ; then
{
# 容器中

  php prepare.php +inotify +apcu +ds +xlswriter +ssh2 +pgsql ${OPTIONS} --with-swoole-pgsql=1 --with-libavif=1

} else {
# 容器外

  php prepare.php --without-docker=1 +inotify +apcu +ds +xlswriter +ssh2 +pgsql ${OPTIONS} --with-swoole-pgsql=1 --with-libavif=1

}
fi



if [ ${WITH_DOWNLOAD_BOX} -eq 1 ] ; then
    echo " please exec script: "
    echo " bash sapi/download-box/download-box-init.sh "
    exit 0
fi

if [ ${WITH_ALL_DEPENDENCIES_CONTAINER} -eq 1 ] ; then
    echo " please exec script: "
    echo " bash sapi/multistage-build-dependencies-container/all-dependencies-build-container.sh --composer_mirror tencent --mirror ustc "
    exit 0
fi


if [ "$OS" = 'linux'  ] && [ ${IN_DOCKER} -eq 0 ] ; then
   echo ' please run in container !'
   exit 0
fi



bash make-install-deps.sh

# 兼容上一版本已构建完毕的依赖库
# bash sapi/quickstart/mark-install-library-cached.sh

bash make.sh all-library

bash make.sh config

bash make.sh build

bash make.sh archive


# 例子
# bash build-release-example.sh --mirror china

# 例子  download-box
# bash build-release-example.sh --mirror china  --download_box
# bash sapi/download-box/download-box-init.sh --proxy http://192.168.3.26:8015

# 例子  all_dependencies
# bash build-release-example.sh --mirror china  --all_dependencies
# bash sapi/multistage-build-dependencies-container/all-dependencies-build-container.sh --composer_mirror tencent --mirror ustc
