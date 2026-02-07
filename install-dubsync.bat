@echo off
echo ========================================
echo DubSync Installation Script
echo ========================================
echo.

echo [1/6] Checking PHP...
php --version
if errorlevel 1 (
    echo ERROR: PHP not found!
    pause
    exit /b 1
)
echo PHP OK!
echo.

echo [2/6] Checking Python...
python --version
if errorlevel 1 (
    echo ERROR: Python not found!
    pause
    exit /b 1
)
echo Python OK!
echo.

echo [3/6] Checking FFmpeg...
ffmpeg -version > nul 2>&1
if errorlevel 1 (
    echo WARNING: FFmpeg not found! Please install FFmpeg for audio processing.
    echo Download from: https://ffmpeg.org/download.html
    pause
) else (
    echo FFmpeg OK!
)
echo.

echo [4/6] Installing Python dependencies...
pip install -r requirements.txt
if errorlevel 1 (
    echo ERROR: Failed to install Python dependencies!
    pause
    exit /b 1
)
echo Python dependencies installed!
echo.

echo [5/6] Installing PHP dependencies...
call composer install
if errorlevel 1 (
    echo ERROR: Failed to install PHP dependencies!
    pause
    exit /b 1
)
echo PHP dependencies installed!
echo.

echo [6/6] Creating storage directories...
if not exist "storage\app\dubsync\tts\" mkdir storage\app\dubsync\tts
if not exist "storage\app\dubsync\temp\" mkdir storage\app\dubsync\temp
if not exist "storage\app\dubsync\projects\" mkdir storage\app\dubsync\projects
if not exist "storage\app\dubsync\exports\" mkdir storage\app\dubsync\exports
if not exist "storage\scripts\" mkdir storage\scripts
echo Storage directories created!
echo.

echo ========================================
echo Installation Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Configure your .env file
echo 2. Run: php artisan migrate
echo 3. Run: npm install ^&^& npm run build
echo 4. Access /dubsync in your browser
echo.
echo For detailed instructions, see:
echo - DUBSYNC_QUICKSTART.md
echo - DUBSYNC_README.md
echo.
pause
