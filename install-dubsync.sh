#!/bin/bash

echo "========================================"
echo "DubSync Installation Script"
echo "========================================"
echo ""

echo "[1/6] Checking PHP..."
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP not found!"
    exit 1
fi
php --version
echo "PHP OK!"
echo ""

echo "[2/6] Checking Python..."
if ! command -v python3 &> /dev/null; then
    echo "ERROR: Python not found!"
    exit 1
fi
python3 --version
echo "Python OK!"
echo ""

echo "[3/6] Checking FFmpeg..."
if ! command -v ffmpeg &> /dev/null; then
    echo "WARNING: FFmpeg not found! Please install FFmpeg for audio processing."
    echo "Ubuntu/Debian: sudo apt-get install ffmpeg"
    echo "macOS: brew install ffmpeg"
else
    ffmpeg -version | head -n 1
    echo "FFmpeg OK!"
fi
echo ""

echo "[4/6] Installing Python dependencies..."
pip3 install -r requirements.txt
if [ $? -ne 0 ]; then
    echo "ERROR: Failed to install Python dependencies!"
    exit 1
fi
echo "Python dependencies installed!"
echo ""

echo "[5/6] Installing PHP dependencies..."
composer install
if [ $? -ne 0 ]; then
    echo "ERROR: Failed to install PHP dependencies!"
    exit 1
fi
echo "PHP dependencies installed!"
echo ""

echo "[6/6] Creating storage directories..."
mkdir -p storage/app/dubsync/tts
mkdir -p storage/app/dubsync/temp
mkdir -p storage/app/dubsync/projects
mkdir -p storage/app/dubsync/exports
mkdir -p storage/scripts
chmod -R 775 storage/app/dubsync
echo "Storage directories created!"
echo ""

echo "========================================"
echo "Installation Complete!"
echo "========================================"
echo ""
echo "Next steps:"
echo "1. Configure your .env file"
echo "2. Run: php artisan migrate"
echo "3. Run: npm install && npm run build"
echo "4. Access /dubsync in your browser"
echo ""
echo "For detailed instructions, see:"
echo "- DUBSYNC_QUICKSTART.md"
echo "- DUBSYNC_README.md"
echo ""
