@echo off
REM Build the GLOBAL stub (default icon), then sign it if configured.
echo Building the global stub...
dotnet publish stub.csproj -c Release -r win-x64 -o out -p:PublishTrimmed=true -p:TrimMode=partial
if errorlevel 1 ( echo. & echo Build FAILED. & pause & exit /b 1 )

call "%~dp0sign.bat" "%~dp0out\FontManagerSetup.exe"
if errorlevel 1 ( echo. & echo *** WARNING: signing failed - the .exe is UNSIGNED. *** )

echo.
echo Done. Your file is in the "out" folder: FontManagerSetup.exe
echo Upload it in Admin -^> Campaigns -^> Upload stub.
pause
