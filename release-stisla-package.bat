@echo off
setlocal enabledelayedexpansion

set "ROOT=%~dp0"
if "%ROOT:~-1%"=="\" set "ROOT=%ROOT:~0,-1%"
set "PKG=%ROOT%\stisla-package"

if not exist "%PKG%\.git" (
    echo [ERROR] Git repository not found in "%PKG%".
    exit /b 1
)

if exist "%ROOT%\sync-stisla-stubs.bat" (
    call "%ROOT%\sync-stisla-stubs.bat"
    if errorlevel 1 exit /b 1
)

for /f "delims=" %%T in ('git -C "%PKG%" describe --tags --abbrev^=0 2^>nul') do set "LAST_TAG=%%T"
if defined LAST_TAG (
    echo Latest tag: %LAST_TAG%
) else (
    echo No existing tags found.
)

set "TAG=%~1"
if "%TAG%"=="" (
    set /p "TAG=Enter new tag (e.g. v1.0.2): "
)
if "%TAG%"=="" (
    echo [ERROR] Tag cannot be empty.
    exit /b 1
)

set "MSG=%~2"
if "%MSG%"=="" (
    set /p "MSG=Enter commit message [Release %TAG%]: "
)
if "%MSG%"=="" set "MSG=Release %TAG%"

git -C "%PKG%" add -A
git -C "%PKG%" diff --cached --quiet
if errorlevel 1 (
    git -C "%PKG%" commit -m "%MSG%"
    if errorlevel 1 (
        echo [ERROR] Git commit failed.
        exit /b 1
    )
) else (
    echo No file changes to commit. Proceeding to tag...
)

git -C "%PKG%" push origin master
if errorlevel 1 (
    echo [ERROR] Failed to push branch master.
    exit /b 1
)

git -C "%PKG%" tag "%TAG%"
if errorlevel 1 (
    echo [ERROR] Failed to create tag %TAG%.
    exit /b 1
)

git -C "%PKG%" push origin "%TAG%"
if errorlevel 1 (
    echo [ERROR] Failed to push tag %TAG%.
    exit /b 1
)

echo.
echo Successfully released %TAG% to origin!
exit /b 0
