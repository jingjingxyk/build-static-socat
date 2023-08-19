<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    // 查看更多 https://git.ffmpeg.org/gitweb

    //更多静态库参考： https://github.com/BtbN/FFmpeg-Builds/tree/master/scripts.d

    //https://github.com/zshnb/ffmpeg-gpu-compile-guide.git

    $ffmpeg_prefix = FFMPEG_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $ldflags = $p->getOsType() == 'macos' ? ' ' : ' -static ';
    $cflags = $p->getOsType() == 'macos' ? ' ' : ' --static ';
    $libs = $p->getOsType() == 'macos' ? ' -lc++ ' : ' -lstdc++ ';

    $CPPFLAGS = $p->getOsType() == 'macos' ? ' ' : " -I/usr/include ";
    $LDFALGS = $p->getOsType() == 'macos' ? ' ' : " -L/usr/lib ";

    $ldexeflags = $p->getOsType() == 'macos' ? ' ' : ' -Bstatic ';

    $lib = new Library('ffmpeg');
    $lib->withHomePage('https://ffmpeg.org/')
        ->withLicense(
            'https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md',
            Library::LICENSE_LGPL
        )
        //->withUrl('https://github.com/FFmpeg/FFmpeg/archive/refs/tags/n6.0.tar.gz')
        //->withFile('ffmpeg-v6.tar.gz')
        ->withManual('https://trac.ffmpeg.org/wiki/CompilationGuide')
        ->withFile('ffmpeg-latest.tar.gz')
        ->withDownloadScript(
            'FFmpeg',
            <<<EOF
            # git clone --depth=1  --single-branch  https://git.ffmpeg.org/ffmpeg.git
            git clone -b master --depth=1  https://github.com/FFmpeg/FFmpeg.git
EOF
        )
        ->withPrefix($ffmpeg_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($ffmpeg_prefix)
        //->withBuildLibraryCached(false)
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            # 汇编编译器
            apk add yasm nasm

EOF
        )
        ->withBuildLibraryCached(false)
        ->withConfigure(
            <<<EOF

            #  libavresample 已弃用，默认编译时不再构建它

            set -x
            ./configure --help
            ./configure --help | grep shared
            ./configure --help | grep static
            ./configure --help | grep  '\-\-extra'
            ./configure --help | grep  'enable'
            ./configure --help | grep  'disable'

            PACKAGES='openssl libwebp  libxml-2.0  freetype2 gmp liblzma' # libssh2
            PACKAGES="\$PACKAGES SvtAv1Dec SvtAv1Enc "
            PACKAGES="\$PACKAGES aom "
            PACKAGES="\$PACKAGES dav1d "
            PACKAGES="\$PACKAGES lcms2 "
            PACKAGES="\$PACKAGES x264 "
            # PACKAGES="\$PACKAGES x265 numa "
            PACKAGES="\$PACKAGES sdl2 "
            PACKAGES="\$PACKAGES ogg "
            PACKAGES="\$PACKAGES opus "
            PACKAGES="\$PACKAGES openh264 "
            PACKAGES="\$PACKAGES vpx "
            PACKAGES="\$PACKAGES fdk-aac "
            PACKAGES="\$PACKAGES fribidi "
            PACKAGES="\$PACKAGES librabbitmq "

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) "
            CPPFLAGS="\$CPPFLAGS -I{$libxml2_prefix}/include/ "
            CPPFLAGS="\$CPPFLAGS  {$CPPFLAGS} "
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) "
            LDFLAGS="\$LDFLAGS  {$LDFALGS} "
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)"
            ./configure  \
            --prefix=$ffmpeg_prefix \
            --enable-gpl \
            --enable-version3 \
            --disable-shared \
            --enable-nonfree \
            --enable-static \
            --enable-openssl \
            --enable-libwebp \
            --enable-libxml2 \
            --enable-libsvtav1 \
            --enable-libaom \
            --enable-lcms2 \
            --enable-gmp \
            --enable-libx264 \
            --enable-random \
            --enable-libfreetype \
            --enable-libvpx \
            --enable-ffplay \
            --enable-sdl2 \
            --enable-libdav1d \
            --enable-libopus \
            --enable-libopenh264 \
            --enable-libfdk-aac \
            --enable-libfribidi \
            --enable-librabbitmq \
            --enable-random \
            --disable-libxcb \
            --disable-libxcb-shm \
            --disable-libxcb-xfixes \
            --disable-libxcb-shape  \
            --disable-xlib  \
            --extra-cflags="{$cflags}   \${CPPFLAGS} " \
            --extra-ldflags="{$ldflags} \${LDFLAGS} " \
            --extra-libs="{$libs}       \${LIBS} " \
            --cc={$p->get_C_COMPILER()} \
            --cxx={$p->get_CXX_COMPILER()}

            # libxcb、xlib 是 x11 相关的库
            # --extra-ldexeflags="{$ldexeflags}"
            # --pkg-config-flags=" {$cflags} "
            # --pkg-config=pkg-config
            # --ld={$p->getLinker()}
            # --enable-libx265
            # --enable-libssh
            # --enable-cross-compile
            # --enable-libspeex


EOF
        )
        ->withPkgName('libavcodec')
        ->withPkgName('libavdevice')
        ->withPkgName('libavfilter')
        ->withPkgName('libavformat')
        ->withPkgName('libavutil')
        ->withPkgName('libswresample')
        ->withPkgName('libswscale')
        ->withBinPath($ffmpeg_prefix . '/bin/')
        ->withDependentLibraries(
            'openssl',
            'zlib',
            'liblzma',
            'libxml2',
            'libwebp',
            'svt_av1',
            'dav1d',
            'aom',
            'freetype',
            "gmp",
            "lcms2",
            "libx264",
            "liblzma",
            "libvpx",
            "sdl2",
            'libogg',
            'libopus',
            'openh264',
            'fdk_aac',
            'libfribidi',
            'rabbitmq_c',
            //'speex' //被opus 取代
        ) //  "libx265", 'libssh2',
    ;

    $p->addLibrary($lib);
};
