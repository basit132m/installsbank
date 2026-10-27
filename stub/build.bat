@echo off
REM Build the GLOBAL stub (default icon). Double-click this file to run.
echo Building the global stub...
dotnet publish stub.csproj -c Release -r win-x64 -o out -p:PublishTrimmed=true -p:TrimMode=partial
echo.
echo Done. Your file is in the "out" folder: FontManagerSetup.exe
echo Upload it in Admin -^> Software Installs -^> Upload stub.
pause
