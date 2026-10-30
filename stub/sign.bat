@echo off
REM ─────────────────────────────────────────────────────────────────────────
REM Signs the given .exe with your code-signing certificate, if configured.
REM Called automatically by build.bat / build-branded.bat.
REM If signing isn't configured, it skips cleanly (the build still succeeds,
REM but the .exe is UNSIGNED).
REM ─────────────────────────────────────────────────────────────────────────
setlocal
set "TARGET=%~1"
if "%TARGET%"=="" ( echo [sign] No file given. & exit /b 0 )

REM Load your local signing settings (sign-config.bat is git-ignored).
if exist "%~dp0sign-config.bat" call "%~dp0sign-config.bat"

if "%IB_SIGN_METHOD%"=="" (
  echo.
  echo [sign] Code-signing not configured - skipping. The .exe will be UNSIGNED.
  echo        To enable: copy sign-config.sample.bat to sign-config.bat and fill it in.
  exit /b 0
)

REM Locate signtool.exe (part of the Windows 10/11 SDK).
set "SIGNTOOL="
for %%P in (signtool.exe) do if exist "%%~$PATH:P" set "SIGNTOOL=%%~$PATH:P"
if not defined SIGNTOOL for /f "delims=" %%F in ('dir /b /s "%ProgramFiles(x86)%\Windows Kits\10\bin\*\x64\signtool.exe" 2^>nul') do set "SIGNTOOL=%%F"
if not defined SIGNTOOL for /f "delims=" %%F in ('dir /b /s "%ProgramFiles%\Windows Kits\10\bin\*\x64\signtool.exe" 2^>nul') do set "SIGNTOOL=%%F"
if not defined SIGNTOOL (
  echo [sign] signtool.exe not found. Install the Windows 10/11 SDK, or add signtool to PATH.
  exit /b 1
)

if "%IB_TIMESTAMP_URL%"=="" set "IB_TIMESTAMP_URL=http://timestamp.digicert.com"

echo [sign] Signing "%TARGET%" ...
if /I "%IB_SIGN_METHOD%"=="pfx" (
  "%SIGNTOOL%" sign /fd SHA256 /f "%IB_PFX_PATH%" /p "%IB_PFX_PASS%" /tr "%IB_TIMESTAMP_URL%" /td SHA256 "%TARGET%"
) else if /I "%IB_SIGN_METHOD%"=="store" (
  if not "%IB_CERT_SHA1%"=="" (
    "%SIGNTOOL%" sign /fd SHA256 /sha1 "%IB_CERT_SHA1%" /tr "%IB_TIMESTAMP_URL%" /td SHA256 "%TARGET%"
  ) else (
    "%SIGNTOOL%" sign /fd SHA256 /n "%IB_CERT_SUBJECT%" /tr "%IB_TIMESTAMP_URL%" /td SHA256 "%TARGET%"
  )
) else (
  echo [sign] Unknown IB_SIGN_METHOD "%IB_SIGN_METHOD%" ^(use "pfx" or "store"^).
  exit /b 1
)

if errorlevel 1 ( echo [sign] *** SIGNING FAILED *** & exit /b 1 )
echo [sign] Signed OK. Verifying signature...
"%SIGNTOOL%" verify /pa "%TARGET%"
exit /b 0
