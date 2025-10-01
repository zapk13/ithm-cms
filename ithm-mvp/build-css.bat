@echo off
echo Building ITHM CMS Tailwind CSS...

REM Check if Node.js is installed
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Node.js is not installed. Using pre-built CSS.
    echo The project is ready to use with the existing tailwind-built.css file.
    pause
    exit /b 0
)

REM Check if npm is available
npm --version >nul 2>&1
if %errorlevel% neq 0 (
    echo npm is not available. Using pre-built CSS.
    echo The project is ready to use with the existing tailwind-built.css file.
    pause
    exit /b 0
)

REM Install dependencies if package.json exists
if exist package.json (
    echo Installing dependencies...
    npm install
)

REM Build CSS
echo Building CSS...
npx tailwindcss -i ./assets/css/tailwind.css -o ./assets/css/tailwind-built.css --minify

echo CSS build complete!
pause
