# build static socat

构建静态 socat

## 构建命令

> 本项目 派生于[jingjingxyk/swoole-cli](https://github.com/jingjingxyk/swoole-cli/tree/new_dev)
> 项目的 `new_dev`分支的静态库构建流程

> 本项目 只需要关注 `.github/workflow` 目录里配置文件的变更

## 下载`build-static-socat`发行版

- [https://github.com/jingjingxyk/build-static-socat/releases](https://github.com/jingjingxyk/build-static-socat/releases)

## 构建文档

- [linux 版构建文档](docs/linux.md)
- [macOS 版构建文档](docs/macOS.md)
- [构建选项文档](docs/options.md)
- [搭建依赖库镜像服务](sapi/download-box/README.md)
- [quickstart](sapi/quickstart/README.md)

## Clone

```shell

git clone -b main https://github.com/jingjingxyk/build-static-socat.git

# 或者

git clone --recursive -b socat https://github.com/jingjingxyk/swoole-cli.git

```

```bash

cd swoole-cli
php prepare.php +socat
bash make-install-deps.sh
bash make.sh all-library
bash make.sh config

```

## 一条命令执行整个构建流程

```bash

cp build-release-example.sh build-release.sh

# 按你的需求修改配置  OPTIONS="${OPTIONS} --with-libavif=1 "
vi build-release.sh

# 执行构建流程
bash build-release.sh


```

## socat 源码构建参考

    http://www.dest-unreach.org/socat/

## 授权协议

* `build-static-socat` 使用了多个其他开源项目，请认真阅读自动生成的 `bin/LICENSE`
  文件中版权协议，遵守对应开源项目的 `LICENSE`
* `build-static-socat`本身的软件源代码、文档等内容以 `Apache 2.0 LICENSE`+`SWOOLE-CLI LICENSE`
  作为双重授权协议，用户需要同时遵守 `Apache 2.0 LICENSE`和`SWOOLE-CLI LICENSE`
  所规定的条款

## SWOOLE-CLI LICENSE

* 对 `swoole-cli` 代码进行使用、修改、发布的新项目必须含有 `SWOOLE-CLI LICENSE`的全部内容
* 使用 `swoole-cli`代码重新发布为新项目或者产品时，项目或产品名称不得包含 `swoole` 单词

