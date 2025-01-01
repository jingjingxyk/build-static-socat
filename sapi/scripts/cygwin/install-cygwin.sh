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

# cp -f /cygdrive/c/setup-x86_64.exe  /cygdrive/c/cygwin/bin/setup-x86_64.exe
# cp -f /cygdrive/c/setup.exe  /cygdrive/c/cygwin/bin/setup-x86_64.exe

# download cygwin
# wget https://cygwin.com/setup-x86_64.exe

# cygwin 移动到 bin 目录
# mv setup-x86_64.exe C:/cygwin64/bin/setup-x86_64.exe

## 设置 站点镜像 地址 为
##  http://mirrors.ustc.edu.cn/cygwin/
##  或者
##  https://mirrors.tuna.tsinghua.edu.cn/cygwin/
## 多个包之间，用逗号分隔

SITE='https://mirrors.kernel.org/sourceware/cygwin/'
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    if [ "$2" = 'china' ]; then
      SITE='https://mirrors.ustc.edu.cn/cygwin/'
    fi
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

set +u
if [ -z "${GITHUB_ACTION}" ]; then
  # 非 github 构建环境下创建启动图标
  setup-x86_64.exe --quiet-mode --disable-buggy-antivirus --site $SITE
fi
set -u
setup-x86_64.exe --no-desktop --no-shortcuts --no-startmenu --quiet-mode --quiet-mode --disable-buggy-antivirus --site $SITE --packages make,git,curl,wget,tar,libtool,bison,gcc-g++,autoconf,automake,openssl,libpcre2-devel,libssl-devel,libcurl-devel,libxml2-devel,libxslt-devel,libgmp-devel,zlib-devel,libbz2-devel,liblz4-devel,liblzma-devel,libzip-devel,libreadline-devel,coreutils
setup-x86_64.exe --no-desktop --no-shortcuts --no-startmenu --quiet-mode --quiet-mode --disable-buggy-antivirus --site $SITE --packages zip unzip
setup-x86_64.exe --no-desktop --no-shortcuts --no-startmenu --quiet-mode --quiet-mode --disable-buggy-antivirus --site $SITE --packages libwrap-devel
