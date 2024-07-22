#!/usr/bin/env bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex

:<<'EOF'
-c, --continue [true|false]
-s, --split=<N>
-x, --max-connection-per-server=<NUM>
-k, --min-split-size=<SIZE>
-j, --max-concurrent-downloads=<N>
-i, --input-file=<FILE>

-s:要用多少镜像来下载每个文件，镜像应该列在一行中

-j:需要同时下载多少个文件(输入文件中的行)

-x:要从每个镜像下载多少流。

  aria2c -d ${__DIR__} -o all-deps.zip -c -s 2 http://10.1.20.7/all-deps.zip http://10.1.20.8/all-deps.zip

EOF

# 解决 /dev/null 目录无权限
# rm -f /dev/null ; mknod -m 666 /dev/null c 1 3

RESULT_CODE=0
while [ $RESULT_CODE -eq 0 ]; do
  curl -fSLo all-deps.zip http://10.1.20.7/all-deps.zip
  echo '' > all-deps.zip && rm -f all-deps.zip
  RESULT_CODE=$?
  sleep 1  # 每隔1秒检查一次
done


# nohup : 不挂断的运行  脱离tty 脱离控制台
# & 符号表示将该命令或脚本放入后台运行


