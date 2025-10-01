#!/bin/bash

echo "Building ITHM CMS Tailwind CSS..."

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "Node.js is not installed. Using pre-built CSS."
    echo "The project is ready to use with the existing tailwind-built.css file."
    exit 0
fi

# Check if npm is available
if ! command -v npm &> /dev/null; then
    echo "npm is not available. Using pre-built CSS."
    echo "The project is ready to use with the existing tailwind-built.css file."
    exit 0
fi

# Install dependencies if package.json exists
if [ -f "package.json" ]; then
    echo "Installing dependencies..."
    npm install
fi

# Build CSS
echo "Building CSS..."
npx tailwindcss -i ./assets/css/tailwind.css -o ./assets/css/tailwind-built.css --minify

echo "CSS build complete!"
