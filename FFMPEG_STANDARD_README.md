# 🎬 FFmpeg Standard System - README

## 📚 Tổng quan

Hệ thống chuẩn hóa việc tạo MP3 và MP4 bằng FFmpeg, đảm bảo tương thích với:
- ✅ **YouTube** - Upload success, xử lý nhanh
- ✅ **Windows Media Player** - Phát mượt mà
- ✅ **AI Processing** - Format chuẩn, metadata đầy đủ

---

## 📋 Documents

| Document | Mô tả | Khi nào dùng |
|----------|-------|--------------|
| **[FFMPEG_STANDARD_GUIDE.md](./FFMPEG_STANDARD_GUIDE.md)** | Hướng dẫn chi tiết đầy đủ | Đọc để hiểu sâu về service |
| **[FFMPEG_MIGRATION_GUIDE.md](./FFMPEG_MIGRATION_GUIDE.md)** | Hướng dẫn chuyển đổi code | Migrate code cũ sang mới |
| **[FFMPEG_QUICK_REFERENCE.md](./FFMPEG_QUICK_REFERENCE.md)** | ⚡ Tra cứu nhanh | Copy-paste code mẫu |

---

## 🚀 Quick Start

### 1. Cài đặt FFmpeg

**Windows:**
```powershell
# Download từ: https://ffmpeg.org/download.html
# Extract vào C:\ffmpeg
# Add C:\ffmpeg\bin vào PATH

# Kiểm tra
ffmpeg -version
```

**Linux/Mac:**
```bash
# Ubuntu/Debian
sudo apt install ffmpeg

# MacOS
brew install ffmpeg

# Kiểm tra
ffmpeg -version
```

### 2. Cấu hình .env

```env
FFMPEG_PATH=ffmpeg
FFPROBE_PATH=ffprobe

# Hoặc full path nếu không có trong PATH
# FFMPEG_PATH=C:\ffmpeg\bin\ffmpeg.exe
# FFPROBE_PATH=C:\ffmpeg\bin\ffprobe.exe
```

### 3. Sử dụng

```php
use App\Services\AudioBookStandardHelper;

$helper = app(AudioBookStandardHelper::class);

// Generate chapter audio
$result = $helper->generateChapterStandardAudio($chapter);

// Generate chapter video
$result = $helper->generateChapterStandardVideo($chapter);
```

---

## 📖 Documentation Flow

### 🎯 Nếu bạn là...

#### Người mới
1. Đọc [FFMPEG_STANDARD_GUIDE.md](./FFMPEG_STANDARD_GUIDE.md) - Hiểu cơ bản
2. Xem [FFMPEG_QUICK_REFERENCE.md](./FFMPEG_QUICK_REFERENCE.md) - Code mẫu
3. Test với chapter đơn lẻ

#### Developer đang migrate
1. Đọc [FFMPEG_MIGRATION_GUIDE.md](./FFMPEG_MIGRATION_GUIDE.md) - Migration steps
2. Follow checklist migration
3. Test từng method một

#### Cần tra cứu nhanh
1. Mở [FFMPEG_QUICK_REFERENCE.md](./FFMPEG_QUICK_REFERENCE.md)
2. Copy code mẫu
3. Customize parameters

---

## 🎯 Common Tasks

### Task 1: Tạo audio cho chapter

```php
use App\Services\AudioBookStandardHelper;

$helper = app(AudioBookStandardHelper::class);

$result = $helper->generateChapterStandardAudio($chapter, [
    'quality' => 'high',           // premium | high | standard
    'remove_silence' => true       // Auto remove silence
]);

// Chapter tự động update với:
// - audio_file
// - audio_duration
// - audio_size
```

**Xem thêm:** [FFMPEG_QUICK_REFERENCE.md](./FFMPEG_QUICK_REFERENCE.md#-create-standard-mp3)

### Task 2: Tạo video cho chapter

```php
$result = $helper->generateChapterStandardVideo($chapter, [
    'resolution' => '1080p',       // 1080p | 720p | 480p
    'audio_quality' => 'premium',  // premium | high | standard
    'zoom_effect' => true          // Ken Burns effect
]);

// Chapter tự động update với:
// - video_file
// - video_duration
// - video_size
```

**Xem thêm:** [FFMPEG_QUICK_REFERENCE.md](./FFMPEG_QUICK_REFERENCE.md#-create-standard-mp4)

### Task 3: Batch generate cho nhiều chapters

```php
// Get selected chapter IDs
$chapterIds = [1, 2, 3, 4, 5];

// Generate all audios
$result = $helper->batchGenerateChapterAudios($audioBook, $chapterIds, [
    'quality' => 'high',
    'remove_silence' => true
]);

// Result:
// [
//     'total' => 5,
//     'success' => 5,
//     'failed' => 0,
//     'results' => [...]
// ]

// Generate all videos
$result = $helper->batchGenerateChapterVideos($audioBook, $chapterIds, [
    'resolution' => '1080p'
]);
```

**Xem thêm:** [FFMPEG_STANDARD_GUIDE.md](./FFMPEG_STANDARD_GUIDE.md#-integration-examples)

### Task 4: Merge full book audio

```php
// Include intro/outro music tự động
$result = $helper->mergeFullBookAudio($audioBook, [
    'quality' => 'high',
    'crossfade' => 0.5             // Smooth transition giữa chapters
]);

// AudioBook tự động update với:
// - full_audio_file
// - full_audio_duration
// - full_audio_size
```

**Xem thêm:** [FFMPEG_QUICK_REFERENCE.md](./FFMPEG_QUICK_REFERENCE.md#-merge-audio-files)

---

## 🎨 Features

### Audio Features
✅ **Auto Normalization** - Âm lượng đồng đều (-16 LUFS)
✅ **Remove Silence** - Cắt khoảng lặng đầu/cuối
✅ **Stereo Conversion** - Auto convert sang stereo
✅ **Prevent Clipping** - Tránh méo tiếng
✅ **Metadata Support** - Title, artist, album, etc.
✅ **Crossfade Merge** - Merge mượt mà giữa các đoạn

### Video Features
✅ **YouTube Optimized** - H.264, yuv420p, 30fps
✅ **Multiple Resolutions** - 1080p, 720p, 480p
✅ **Ken Burns Effect** - Zoom nhẹ cho ảnh tĩnh
✅ **Wave Visualization** - Hiển thị sóng âm
✅ **Fast Start** - Streaming-friendly
✅ **AAC Audio** - High quality audio

---

## 📊 Quality Reference

### Audio Quality

| Quality | Bitrate | Sample Rate | Size/min | Use Case |
|---------|---------|-------------|----------|----------|
| Premium | 192k | 48kHz | ~1.4MB | YouTube, Podcast Pro |
| High | 128k | 48kHz | ~1MB | ⭐ **Recommended** |
| Standard | 96k | 44.1kHz | ~750KB | Mobile, Tiết kiệm |

### Video Quality

| Resolution | Size | Bitrate | Size/min | Use Case |
|------------|------|---------|----------|----------|
| 1080p | 1920x1080 | 10Mbps | ~75MB | ⭐ **YouTube Standard** |
| 720p | 1280x720 | 6Mbps | ~45MB | HD Quality |
| 480p | 854x480 | 3Mbps | ~22MB | Mobile Friendly |

---

## 🏗️ Architecture

```
FFmpegStandardService (Core)
    ├── createStandardMP3()         - Tạo MP3 chuẩn
    ├── createStandardMP4()         - Tạo MP4 chuẩn
    ├── mergeAudioFiles()           - Merge với crossfade
    └── Helper methods

AudioBookStandardHelper (Wrapper)
    ├── generateChapterStandardAudio()      - Audio cho chapter
    ├── generateChapterStandardVideo()      - Video cho chapter
    ├── batchGenerateChapterAudios()        - Batch audio
    ├── batchGenerateChapterVideos()        - Batch video
    ├── mergeFullBookAudio()                - Merge full book
    └── generateDescriptionStandardAudio()  - Audio giới thiệu
```

---

## 🔧 Services

### FFmpegStandardService

**Purpose:** Core service xử lý ffmpeg commands

**Methods:**
- `createStandardMP3()` - Tạo MP3 chuẩn
- `createStandardMP4()` - Tạo MP4 chuẩn
- `mergeAudioFiles()` - Merge audio files

**Location:** `app/Services/FFmpegStandardService.php`

### AudioBookStandardHelper

**Purpose:** Wrapper service cho AudioBook system

**Methods:**
- `generateChapterStandardAudio()` - Generate & update chapter
- `generateChapterStandardVideo()` - Generate & update video
- `batchGenerateChapterAudios()` - Batch processing
- `mergeFullBookAudio()` - Merge with intro/outro

**Location:** `app/Services/AudioBookStandardHelper.php`

---

## 💡 Best Practices

### ✅ DO

1. **Luôn dùng `high` quality** cho audiobook
2. **Bật `remove_silence`** cho trải nghiệm tốt
3. **Dùng `1080p`** cho YouTube uploads
4. **Thêm metadata đầy đủ** cho SEO
5. **Enable wave effect** cho video đẹp hơn
6. **Crossfade 0.5s** cho merge mượt

### ❌ DON'T

1. ❌ Dùng `premium` khi không cần (file quá lớn)
2. ❌ Tắt `normalize_audio` khi merge nhiều file
3. ❌ Dùng 480p cho video chính (chỉ preview)
4. ❌ Skip error handling
5. ❌ Process quá nhiều files cùng lúc (OOM)

---

## 🧪 Testing

### Test single chapter

```php
Route::get('/test-chapter/{chapter}', function($chapterId) {
    $chapter = AudioBookChapter::find($chapterId);
    $helper = app(AudioBookStandardHelper::class);
    
    // Test audio
    $audioResult = $helper->generateChapterStandardAudio($chapter);
    
    // Test video
    $videoResult = $helper->generateChapterStandardVideo($chapter);
    
    return response()->json([
        'audio' => $audioResult,
        'video' => $videoResult
    ]);
});
```

### Test batch

```php
Route::get('/test-batch/{audiobookId}', function($audiobookId) {
    $audioBook = AudioBook::find($audiobookId);
    $helper = app(AudioBookStandardHelper::class);
    
    // Test first 3 chapters
    $chapters = $audioBook->chapters()->limit(3)->pluck('id');
    
    $result = $helper->batchGenerateChapterAudios($audioBook, $chapters->toArray());
    
    return response()->json($result);
});
```

---

## 📝 Logs

Service tự động log các operations:

```php
// Check logs
tail -f storage/logs/laravel.log

// Windows PowerShell
Get-Content storage/logs/laravel.log -Wait -Tail 50

// Filter FFmpeg logs
grep "FFmpeg" storage/logs/laravel.log
```

**Log examples:**
```
[2024-02-06 10:00:00] local.INFO: Creating standard MP3 {"input":"...", "quality":"high"}
[2024-02-06 10:00:05] local.INFO: MP3 created successfully {"duration":180.5, "size":"2.06 MB"}
```

---

## ⚠️ Troubleshooting

### FFmpeg not found

**Error:** `FFmpeg is not installed or not in system PATH`

**Solution:**
```env
# Set full path in .env
FFMPEG_PATH=C:\ffmpeg\bin\ffmpeg.exe
FFPROBE_PATH=C:\ffmpeg\bin\ffprobe.exe
```

### Permission denied

**Error:** `Permission denied when creating directory`

**Solution:**
```bash
# Linux/Mac
chmod -R 775 storage/app/public/audiobooks

# Windows: Check folder permissions in Properties
```

### Out of memory

**Error:** `Allowed memory size exhausted`

**Solution:**
```php
// Increase memory_limit
ini_set('memory_limit', '512M');

// Or use queue for heavy tasks
dispatch(new GenerateChapterAudioJob($chapter));
```

**Xem thêm:** [FFMPEG_MIGRATION_GUIDE.md](./FFMPEG_MIGRATION_GUIDE.md#-common-issues--solutions)

---

## 🔗 Related Documents

### Internal
- [FFMPEG_STANDARD_GUIDE.md](./FFMPEG_STANDARD_GUIDE.md) - Full documentation
- [FFMPEG_MIGRATION_GUIDE.md](./FFMPEG_MIGRATION_GUIDE.md) - Migration guide
- [FFMPEG_QUICK_REFERENCE.md](./FFMPEG_QUICK_REFERENCE.md) - Quick reference

### External
- [YouTube Upload Specs](https://support.google.com/youtube/answer/1722171)
- [FFmpeg Documentation](https://ffmpeg.org/documentation.html)
- [AAC Audio Codec](https://en.wikipedia.org/wiki/Advanced_Audio_Coding)
- [H.264 Video Codec](https://en.wikipedia.org/wiki/Advanced_Video_Coding)

---

## 📈 Performance

### File Size Examples

**10-minute audiobook chapter:**
- Audio (high): ~10 MB
- Video (1080p): ~750 MB

**60-chapter audiobook (180 minutes):**
- Full audio (high): ~180 MB
- All videos (1080p): ~13.5 GB

### Processing Time

**On average machine:**
- Audio generation: ~real-time (1 min audio = 1 min process)
- Video generation: ~2-3x real-time (1 min video = 2-3 min process)
- Batch 10 chapters: ~15-20 minutes

**Tips to speed up:**
- Use queue jobs
- Lower resolution for drafts
- Process in background

---

## 🎓 Learning Path

1. **Day 1:** Read [FFMPEG_STANDARD_GUIDE.md](./FFMPEG_STANDARD_GUIDE.md)
2. **Day 2:** Test single chapter audio/video
3. **Day 3:** Test batch processing
4. **Day 4:** Read [FFMPEG_MIGRATION_GUIDE.md](./FFMPEG_MIGRATION_GUIDE.md)
5. **Day 5:** Migrate production code

---

## 📞 Support

### Get Help

1. Check [FFMPEG_QUICK_REFERENCE.md](./FFMPEG_QUICK_REFERENCE.md) first
2. Review logs in `storage/logs/laravel.log`
3. Verify FFmpeg installation: `ffmpeg -version`
4. Check file permissions
5. Review error messages

### Common Commands

```bash
# Check FFmpeg version
ffmpeg -version

# Check available codecs
ffmpeg -codecs | grep aac
ffmpeg -codecs | grep h264

# Check file info
ffprobe video.mp4

# Test basic conversion
ffmpeg -i input.wav -c:a aac -b:a 128k output.mp3
```

---

## 🚀 Quick Links

| Task | Go to |
|------|-------|
| Learn basics | [FFMPEG_STANDARD_GUIDE.md](./FFMPEG_STANDARD_GUIDE.md) |
| Migrate code | [FFMPEG_MIGRATION_GUIDE.md](./FFMPEG_MIGRATION_GUIDE.md) |
| Copy code | [FFMPEG_QUICK_REFERENCE.md](./FFMPEG_QUICK_REFERENCE.md) |
| Generate audio | [Quick Ref - MP3](./FFMPEG_QUICK_REFERENCE.md#-create-standard-mp3) |
| Generate video | [Quick Ref - MP4](./FFMPEG_QUICK_REFERENCE.md#-create-standard-mp4) |
| Troubleshoot | [Migration - Issues](./FFMPEG_MIGRATION_GUIDE.md#-common-issues--solutions) |

---

## 📄 License

Internal use only - SumoTech Project

---

## 📌 Version

**Current Version:** 1.0.0  
**Last Updated:** February 2024  
**Supported FFmpeg:** 4.0+  
**Supported PHP:** 8.0+  
**Supported Laravel:** 10.x  

---

## 🎉 Công việc hoàn thành!

Bạn đã có đầy đủ:
✅ Core service (FFmpegStandardService)
✅ Helper service (AudioBookStandardHelper)
✅ Full documentation (3 files)
✅ Code examples  
✅ Migration guide
✅ Quick reference

**Bước tiếp theo:**
1. Test với 1 chapter
2. Review kết quả
3. Migrate production code

Happy coding! 🚀
