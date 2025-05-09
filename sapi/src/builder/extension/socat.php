<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'socat'
    ];
    $ext = (new Extension('socat'))
        ->withHomePage('http://www.dest-unreach.org/socat/')
        ->withManual('http://www.dest-unreach.org/socat/')
        ->withLicense('https://repo.or.cz/socat.git/blob/refs/heads/master:/COPYING', Extension::LICENSE_LGPL);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->withReleaseArchive('socat', function (Preprocessor $p) {
        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $socat_prefix = SOCAT_PREFIX;
        $system_arch = $p->getSystemArch();
        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cd {$builddir}/socat
                cp -f socat {$workdir}/bin/
                cp -rf doc {$workdir}/bin/socat-docs
                cd {$workdir}/bin/
                APP_VERSION=$({$workdir}/bin/socat -V | grep 'socat version' | awk '{ print $3 }')
                APP_NAME='socat'
                echo \${APP_VERSION} > {$workdir}/APP_VERSION
                echo \${APP_NAME} > {$workdir}/APP_NAME
                strip {$workdir}/bin/socat

EOF;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
            file {$workdir}/bin/socat
            otool -L {$workdir}/bin/socat
            tar -cJvf {$workdir}/\${APP_NAME}-\${APP_VERSION}-macos-{$system_arch}.tar.xz \${APP_NAME} LICENSE
EOF;
        } else {
            $cmd .= <<<EOF
              file {$workdir}/bin/socat
              readelf -h {$workdir}/bin/socat
              tar -cJvf {$workdir}/\${APP_NAME}-\${APP_VERSION}-linux-{$system_arch}.tar.xz \${APP_NAME} LICENSE

EOF;
        }
        return $cmd;
    });
};
