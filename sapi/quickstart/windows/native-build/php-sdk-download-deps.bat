@echo off

rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%
cd /d %__PROJECT__%\var\windows-build-deps\php-sdk-binary-tools\

phpsdk_buildtree phpdev

phpsdk_deps -u

cd /d %__PROJECT__%

