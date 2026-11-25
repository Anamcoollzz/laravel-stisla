@echo off
setlocal enabledelayedexpansion

set "ROOT=%~dp0"
if "%ROOT:~-1%"=="\" set "ROOT=%ROOT:~0,-1%"
set "STUBS=%ROOT%\stisla-package\stubs"

echo Syncing project files to "%STUBS%"...

for %%D in (
    "app\Http\Controllers"
    "app\Models"
    "app\Console\Commands"
    "app\Exports"
    "app\Imports"
    "app\Http\Middleware"
    "app\Http\Requests"
    "app\Mail"
    "app\Observers"
    "app\Providers"
    "app\Services"
    "resources\views"
    "database\migrations"
    "database\seeders"
    "database\factories"
    "lang"
) do (
    if exist "%ROOT%\%%~D" (
        robocopy "%ROOT%\%%~D" "%STUBS%\%%~D" /MIR /NFL /NDL /NJH /NJS /NC /NS /NP >nul
        if errorlevel 8 (
            echo [ERROR] Failed syncing %%~D
            exit /b 1
        )
    )
)

if exist "%ROOT%\database\indonesia.sql" (
    copy /Y "%ROOT%\database\indonesia.sql" "%STUBS%\database\indonesia.sql" >nul
)

if not exist "%STUBS%\config" mkdir "%STUBS%\config"
for %%F in (captcha.php company.php excel.php permission.php settings.php stisla.php) do (
    if exist "%ROOT%\config\%%F" (
        copy /Y "%ROOT%\config\%%F" "%STUBS%\config\%%F" >nul
    )
)

if not exist "%STUBS%\routes" mkdir "%STUBS%\routes"
for %%F in (
    stisla-web.php
    stisla-pages-web.php
    spark-admin-web.php
    ecommerce-pages-web.php
    news-web.php
    spp-payment-web.php
    lp-compro-web.php
) do (
    if exist "%ROOT%\routes\%%F" (
        copy /Y "%ROOT%\routes\%%F" "%STUBS%\routes\%%F" >nul
    )
)

for %%P in (assets lp-assets lp-build mantu modules news-assets node_modules templates) do (
    if exist "%ROOT%\public\%%P" (
        robocopy "%ROOT%\public\%%P" "%STUBS%\public\%%P" /MIR /NFL /NDL /NJH /NJS /NC /NS /NP >nul
        if errorlevel 8 (
            echo [ERROR] Failed syncing public\%%P
            exit /b 1
        )
    )
)

echo Done! All stubs synced successfully.
exit /b 0
