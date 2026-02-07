# DubSync - Video Dubbing Workflow System

DubSync is a comprehensive Laravel-based system for automated video dubbing workflow. It processes YouTube videos by extracting transcripts, translating them to Vietnamese, generating TTS (Text-to-Speech) audio, and creating synchronized subtitle files.

## Features

1. **YouTube Transcript Extraction** - Automatically fetches transcript with timestamps from YouTube videos
2. **Transcript Cleaning** - Normalizes and cleans transcript text
3. **Smart Segmentation** - Segments transcript into meaningful paragraphs
4. **Vietnamese Translation** - Translates content to Vietnamese while preserving meaning and rhythm
5. **TTS Generation** - Creates Vietnamese voice-over for each segment
6. **Time Alignment** - Automatically adjusts audio timing to match original timestamps
7. **Audio Merging** - Combines all segments into a single timeline
8. **Multi-format Export**:
   - SRT (SubRip) subtitle files
   - VTT (WebVTT) subtitle files
   - WAV audio files
   - MP3 audio files
   - JSON project files (for re-editing)

## Requirements

- PHP 8.1+
- Laravel 10.x
- MySQL/MariaDB
- Python 3.8+ (for YouTube transcript extraction)
- FFmpeg (for audio processing)
- Composer
- Node.js & NPM

## Installation

### 1. Install PHP Dependencies

```bash
composer install
```

### 2. Install Python Dependencies

```bash
pip install youtube-transcript-api
```

### 3. Install FFmpeg

**Windows:**
- Download from https://ffmpeg.org/download.html
- Add FFmpeg to system PATH

**Linux:**
```bash
sudo apt-get install ffmpeg
```

**macOS:**
```bash
brew install ffmpeg
```

### 4. Environment Configuration

Copy `.env.example` to `.env` and configure:

```env
# Google Translation API (Optional - for production)
GOOGLE_TRANSLATE_API_KEY=your_google_translate_api_key

# Google Text-to-Speech API (Optional - for production)
GOOGLE_TTS_API_KEY=your_google_tts_api_key

# File Storage
FILESYSTEM_DISK=local
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Create Storage Link

```bash
php artisan storage:link
```

### 8. Install Frontend Dependencies

```bash
npm install
npm run build
```

## Usage

### Accessing DubSync

1. Log in to your Laravel application
2. Navigate to "DubSync" from the main navigation
3. Enter a YouTube URL in the input field
4. Click "Bắt đầu xử lý" (Start Processing)

### Workflow Steps

1. **Extract Transcript** - The system automatically fetches the video transcript
2. **Review Segments** - Check and edit the extracted segments if needed
3. **Translate** - Click "Dịch sang tiếng Việt" to translate to Vietnamese
4. **Generate TTS** - Click "Tạo giọng nói TTS" to create voice-over
5. **Align Timing** - Click "Căn chỉnh thời lượng" to match original timing
6. **Merge Audio** - Click "Ghép audio" to create final audio track
7. **Export** - Select desired formats and export files

### Re-editing Segments

- You can edit any translated segment text
- Click "Tạo lại giọng nói cho đoạn này" to regenerate TTS for specific segments
- The JSON export allows you to save the project for later re-editing

## API Integration

### Google Cloud Translation API

To use production-quality translation:

1. Create a Google Cloud project
2. Enable Cloud Translation API
3. Create an API key
4. Add to `.env`: `GOOGLE_TRANSLATE_API_KEY=your_key`

### Google Cloud Text-to-Speech API

For high-quality Vietnamese TTS:

1. Enable Cloud Text-to-Speech API in Google Cloud
2. Create an API key
3. Add to `.env`: `GOOGLE_TTS_API_KEY=your_key`

## File Structure

```
app/
├── Http/Controllers/
│   └── DubSyncController.php       # Main controller
├── Models/
│   └── DubSyncProject.php          # Project model
└── Services/
    ├── YouTubeTranscriptService.php
    ├── TranscriptCleanerService.php
    ├── TranscriptSegmentationService.php
    ├── TranslationService.php
    ├── TTSService.php
    ├── AudioAlignmentService.php
    ├── AudioMergeService.php
    └── ExportService.php

resources/views/
└── dubsync/
    └── index.blade.php             # Main interface

storage/
├── app/dubsync/                    # Generated files
│   ├── tts/                        # TTS audio segments
│   ├── temp/                       # Temporary files
│   ├── projects/                   # Final merged audio
│   └── exports/                    # Exported files
└── scripts/
    └── get_youtube_transcript.py   # Python script for YouTube
```

## Development Mode

In development mode (without API keys), the system uses:

- Mock translations (basic English to Vietnamese mapping)
- Mock TTS (placeholder audio files)
- Sample YouTube transcript data

This allows you to test the full workflow without external API dependencies.

## Production Deployment

1. Set up proper API keys in `.env`
2. Configure a production database
3. Set `APP_ENV=production` in `.env`
4. Run `php artisan config:cache`
5. Run `php artisan route:cache`
6. Set up queue workers for background processing (optional)
7. Configure proper file storage (S3, etc.)

## Troubleshooting

### Python Script Not Working

- Ensure Python is in system PATH
- Verify `youtube-transcript-api` is installed
- Check script path in `YouTubeTranscriptService.php`

### FFmpeg Errors

- Verify FFmpeg is installed and in PATH
- Check FFmpeg version: `ffmpeg -version`
- Ensure proper file permissions for storage directories

### Audio Quality Issues

- Adjust TTS voice settings in `TTSService.php`
- Modify time-stretching parameters in `AudioAlignmentService.php`
- Change audio bitrate in export functions

## License

This project is proprietary software.

## Support

For issues or questions, please contact the development team.
