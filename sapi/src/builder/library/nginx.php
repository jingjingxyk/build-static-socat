<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $builderDir = $p->getBuildDir();
    $workDir = $p->getWorkDir();

    $nginx_prefix = NGINX_PREFIX;

    $openssl = $p->getLibrary('openssl');
    $zlib = $p->getLibrary('zlib');
    $pcre2 = $p->getLibrary('pcre2');

    $p->addLibrary(
        (new Library('nginx'))
            ->withHomePage('https://nginx.org/')
            ->withLicense('https://github.com/nginx/nginx/blob/master/docs/text/LICENSE', Library::LICENSE_SPEC)
            ->withUrl('https://nginx.org/download/nginx-1.21.0.tar.gz')
            ->withManual('https://github.com/nginx/nginx')
            ->withManual('http://nginx.org/en/docs/configure.html')
            ->withDocumentation('https://nginx.org/en/docs/')
            ->withFile('nginx-1.24.0.tar.gz')
            /*
            ->withDownloadScript(
                'nginx',
                <<<EOF
                cat > ~/.hgrc <<__EOF__
[http_proxy]
host=http://192.168.3.26:8015
[https_proxy]
host=http://192.168.3.26:8015

__EOF__
                # pip3 install mercurial -i https://pypi.tuna.tsinghua.edu.cn/simple
                # hg clone  http://hg.nginx.org/nginx
                # hg update -C release-1.25.1
                git clone -b release-1.25.1 --depth 1 --progress  https://github.com/nginx/nginx.git

                # git clone --depth 1 --progress   https://github.com/chobits/ngx_http_proxy_connect_module.git

EOF
            )
            */
            ->withPrefix($nginx_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($nginx_prefix)
            ->withConfigure(
                <<<EOF
:<<'===EOF==='
             set -x


            # nginx use PCRE2 library  on  nginx 1.21.5
            # now nginx is built with the PCRE2 library by default.

            # sed -i "50i echo 'stop preprocessor'; exit 3 " ./configure

            ./configure --help

            # 使用 zlib openssl pcre2 新的源码目录
            mkdir -p {$builderDir}/nginx/openssl
            mkdir -p {$builderDir}/nginx/zlib
            mkdir -p {$builderDir}/nginx/pcre2
            tar --strip-components=1 -C {$builderDir}/nginx/openssl -xf  {$workDir}/pool/lib/openssl-3.0.8-quic1.tar.gz
            tar --strip-components=1 -C {$builderDir}/nginx/zlib    -xf  {$workDir}/pool/lib/zlib-1.2.11.tar.gz
            tar --strip-components=1 -C {$builderDir}/nginx/pcre2   -xf  {$workDir}/pool/lib/pcre2-10.42.tar.gz
            PACKAGES=" libxml-2.0 libexslt libxslt openssl zlib"
            PACKAGES="\$PACKAGES libpcre2-16  libpcre2-32  libpcre2-8   libpcre2-posix"
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)"
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)"
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)"
            ./configure \
            --prefix={$nginx_prefix} \
            --with-openssl={$builderDir}/nginx/openssl \
            --with-pcre={$builderDir}/nginx/pcre2 \
            --with-zlib={$builderDir}/nginx/zlib \
===EOF===
            set -x
            # patch -p1 < {$builderDir}/ngx_http_proxy_connect_module/patch/proxy_connect_rewrite_102101.patch

            ./configure --help
            PACKAGES=" libxml-2.0 libexslt libxslt openssl zlib"
            PACKAGES="\$PACKAGES libpcre  libpcre16  libpcre32  libpcrecpp  libpcreposix"
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)"
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)"
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)"
             ./configure \
            --prefix={$nginx_prefix} \
            --with-http_ssl_module \
            --with-http_gzip_static_module \
            --with-http_stub_status_module \
            --with-http_realip_module \
            --with-http_auth_request_module \
            --with-http_v2_module \
            --with-http_flv_module \
            --with-http_sub_module \
            --with-stream \
            --with-stream_ssl_preread_module \
            --with-stream_ssl_module \
            --with-threads \
            --with-cc-opt="-static -O2   \$CPPFLAGS " \
            --with-ld-opt="-static  \$LDFLAGS "
            # --add-module={$builderDir}/ngx_http_proxy_connect_module/


            #--with-cc-opt="-O2 -static -Wl,-pie \$CPPFLAGS"
            # --with-ld-opt=parameters — sets additional parameters that will be used during linking.
            # --with-cc-opt=parameters — sets additional parameters that will be added to the CFLAGS variable.
EOF
            )
            //->withMakeOptions('CFLAGS="-O2 -s" LDFLAGS="-static"')
            ->withBinPath($nginx_prefix . '/bin/')
            ->withDependentLibraries('libxml2', 'libxslt', 'openssl', 'zlib', 'pcre', 'ngx_http_proxy_connect_module')
    );
};