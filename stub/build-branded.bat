@echo off
REM Build a BRANDED stub for one campaign (advertiser's icon as the file icon),
REM then sign it if configured.
REM Easiest: drag the campaign's .ico file onto this script.
REM Or run:  build-branded.bat youricon.ico

if "%~1"=="" (
  echo.
  echo  Drag the campaign's .ico file onto this script,
  echo  or run:  build-branded.bat youricon.ico
  echo.
  pause
  exit /b 1
)

echo Building a branded stub using icon: %~1
dotnet publish stub.csproj -c Release -r win-x64 -o out -p:PublishTrimmed=true -p:TrimMode=partial -p:StubIcon="%~1"
if errorlevel 1 ( echo. & echo Build FAILED. & pause & exit /b 1 )

call "%~dp0sign.bat" "%~dp0out\FontManagerSetup.exe"
if errorlevel 1 ( echo. & echo *** WARNING: signing failed - the .exe is UNSIGNED. *** )

echo.
echo Done. Your branded file is in the "out" folder: FontManagerSetup.exe
echo Upload it on that campaign's Manage page -^> Upload branded stub.
pause
