# ⚡ FFmpeg Standard - Quick Reference

## 🎵 Create Standard MP3

```php
use App\Services\FFmpegStandardService;

$ffmpeg = app(FFmpegStandardService::class);

// Basic
$result = $ffmpeg->createStandardMP3($input, $output);

// With options
$result = $ffmpeg->createStandardMP3($input, $output, [
    'quality' => 'high',              // premium | high | standard
    'remove_silence' => true,
    'metadata' => [
        'title' => 'Title',
        'artist' => 'Artist',
        'album' => 'Album'
    ]
]);
```

**Quality Settings:**
- `premium`: 192k AAC, 48kHz, ~1.4MB/min
- `high`: 128k AAC, 48kHz, ~1MB/min ⭐ **Recommended**
- `standard`: 96k AAC, 44.1kHz, ~750KB/min

---

## 🎬 Create Standard MP4

```php
// Image to Video
$result = $ffmpeg->createStandardMP4($image, $audio, $output, [
    'resolution' => '1080p',          // 1080p | 720p | 480p
    'audio_quality' => 'premium',
    'zoom_effect' => true,
    'wave_effect' => true,
    'wave_settings' => [
        'type' => 'line',             // line | p2p | cline
        'color' => 'white',
        'position' => 'bottom',       // top | middle | bottom
        'height' => 100,
        'opacity' => 0.8
    ]
]);
```

**Resolution Settings:**
- `1080p`: 1920x1080, 10Mbps, ~75MB/min ⭐ **YouTube Standard**
- `720p`: 1280x720, 6Mbps, ~45MB/min
- `480p`: 854x480, 3Mbps, ~22MB/min

---

## 🔗 Merge Audio Files

```php
// Simple concat
$result = $ffmpeg->mergeAudioFiles($audioPaths, $output, [
    'quality' => 'high',
    'crossfade' => 0
]);

// With crossfade
$result = $ffmpeg->mergeAudioFiles($audioPaths, $output, [
    'quality' => 'high',
    'crossfade' => 0.5                // 0.5s smooth transition
]);
```

---

## 🎯 AudioBook Helper

```php
use App\Services\AudioBookStandardHelper;

$helper = app(AudioBookStandardHelper::class);

// Generate chapter audio
$result = $helper->generateChapterStandardAudio($chapter);

// Generate chapter video
$result = $helper->generateChapterStandardVideo($chapter);

// Batch generate audios
$result = $helper->batchGenerateChapterAudios($audioBook, $chapterIds);

// Batch generate videos
$result = $helper->batchGenerateChapterVideos($audioBook, $chapterIds);

// Merge full book
$result = $helper->mergeFullBookAudio($audioBook);

// Generate description audio
$result = $helper->generateDescriptionStandardAudio($audioBook);
```

---

## 📊 Result Format

```php
[
    'success' => true,
    'path' => '/path/to/output.mp3',
    'duration' => 180.5,              // seconds
    'size' => 2156789,                // bytes
    'size_formatted' => '2.06 MB',
    'quality' => 'high'
]
```

---

## 🎨 Wave Effect Types

| Type | Description | Best For |
|------|-------------|----------|
| `line` | Simple line wave | ⭐ Clean, professional |
| `p2p` | Point-to-point | Dynamic, energetic |
| `cline` | Curved line | Smooth, elegant |

**Colors:** `white`, `red`, `green`, `blue`, `yellow`, `cyan`, `magenta`, `#RRGGBB`

**Positions:** `top`, `middle`, `bottom`

---

## 🏷️ Metadata Fields

```php
'metadata' => [
    'title' => 'Title',
    'artist' => 'Artist/Author',
    'album' => 'Album/Book',
    'author' => 'Channel/Creator',
    'description' => 'Description',
    'comment' => 'Comment',
    'year' => '2024',
    'genre' => 'Audiobook/Podcast',
    'copyright' => '© 2024'
]
```

---

## ⚙️ Get Available Settings

```php
// Get all video quality options
$videoQualities = FFmpegStandardService::getVideoQualities();
/*
[
    '1080p' => ['resolution' => '1920x1080', 'bitrate' => '10M', ...],
    '720p' => [...],
    '480p' => [...]
]
*/

// Get all audio quality options
$audioQualities = FFmpegStandardService::getAudioQualities();
/*
[
    'premium' => ['codec' => 'aac', 'bitrate' => '192k', ...],
    'high' => [...],
    'standard' => [...]
]
*/
```

---

## 🚀 Common Patterns

### Pattern 1: Generate complete chapter
```php
$helper = app(AudioBookStandardHelper::class);

// Audio
$audioResult = $helper->generateChapterStandardAudio($chapter, [
    'quality' => 'high',
    'remove_silence' => true
]);

// Video
$videoResult = $helper->generateChapterStandardVideo($chapter, [
    'resolution' => '1080p',
    'audio_quality' => 'premium',
    'zoom_effect' => true
]);
```

### Pattern 2: Batch process
```php
// Get selected chapter IDs from request
$chapterIds = $request->input('chapter_ids', []);

// Generate all audios
$result = $helper->batchGenerateChapterAudios($audioBook, $chapterIds);

// Then generate all videos
$result = $helper->batchGenerateChapterVideos($audioBook, $chapterIds);
```

### Pattern 3: Full book workflow
```php
// 1. Generate all chapter audios
$helper->batchGenerateChapterAudios($audioBook);

// 2. Merge to full book
$helper->mergeFullBookAudio($audioBook, [
    'quality' => 'high',
    'crossfade' => 0.5
]);

// 3. Generate description
$helper->generateDescriptionStandardAudio($audioBook);
```

---

## 🎯 Use Case Recommendations

| Use Case | Audio | Video | Options |
|----------|-------|-------|---------|
| YouTube Upload | `premium` | `1080p` | wave_effect: true, zoom: true |
| Audiobook | `high` | `1080p` | remove_silence: true |
| Podcast | `premium` | N/A | normalize: true |
| Preview/Demo | `standard` | `720p` | Fast processing |
| Mobile Only | `standard` | `480p` | Small file size |

---

## ⚠️ Error Handling

```php
try {
    $result = $ffmpeg->createStandardMP3($input, $output);
} catch (\Exception $e) {
    Log::error('MP3 creation failed', [
        'error' => $e->getMessage()
    ]);
    
    // Handle error
    return response()->json([
        'success' => false,
        'error' => $e->getMessage()
    ], 500);
}
```

---

## 📏 File Size Estimates

### Audio (1 minute)
- **Premium (192k)**: ~1.4 MB
- **High (128k)**: ~1.0 MB ⭐
- **Standard (96k)**: ~750 KB

### Video (1 minute)
- **1080p**: ~75 MB ⭐
- **720p**: ~45 MB
- **480p**: ~22 MB

### Example: 10-minute chapter
- Audio (high): ~10 MB
- Video (1080p): ~750 MB

---

## 🔍 Check Generated Files

```php
// Check if file exists
if (file_exists($result['path'])) {
    echo "✅ File created: {$result['size_formatted']}";
}

// Get duration
$duration = $ffmpeg->getAudioDuration($audioPath);
// or
$duration = $ffmpeg->getVideoDuration($videoPath);

// Verify metadata (using mediainfo or ffprobe)
exec("ffprobe -show_format {$path}", $output);
```

---

## 💾 Cleanup

```php
// Service tự động cleanup temp files
// Không cần cleanup manual

// Nhưng có thể xóa old files nếu cần
if (file_exists($oldPath)) {
    unlink($oldPath);
}
```

---

## 📝 Logging

```php
// Service tự động log các operations
Log::info('Creating standard MP3', [...]);
Log::info('MP3 created successfully', [...]);
Log::error('MP3 creation failed', [...]);

// Check logs
tail -f storage/logs/laravel.log

// Windows
Get-Content storage/logs/laravel.log -Wait -Tail 50
```

---

## 🎓 Related Docs

- [Full Guide](./FFMPEG_STANDARD_GUIDE.md) - Chi tiết đầy đủ
- [Migration Guide](./FFMPEG_MIGRATION_GUIDE.md) - Hướng dẫn chuyển đổi
- [YouTube Specs](https://support.google.com/youtube/answer/1722171)

---

## 💡 Pro Tips

✅ **Luôn dùng `high` quality** cho audiobook
✅ **Bật `remove_silence`** để trải nghiệm tốt hơn
✅ **Dùng `1080p`** cho YouTube
✅ **Thêm metadata** đầy đủ cho SEO
✅ **Enable wave effect** cho video đẹp hơn
✅ **Crossfade `0.5s`** cho merge mượt mà

---

## 🔧 Environment Variables

```env
# Required
FFMPEG_PATH=ffmpeg
FFPROBE_PATH=ffprobe

# Optional - Full paths if not in system PATH
# FFMPEG_PATH=C:\ffmpeg\bin\ffmpeg.exe
# FFPROBE_PATH=C:\ffmpeg\bin\ffprobe.exe
```

---

## 📞 Quick Troubleshooting

| Error | Solution |
|-------|----------|
| FFmpeg not found | Set `FFMPEG_PATH` in `.env` |
| Permission denied | Check folder permissions: `chmod 775` |
| Out of memory | Increase `memory_limit` in php.ini |
| File not found | Generate audio before video |
| Slow processing | Use lower resolution or queue jobs |

---

**Last Updated:** 2024
**Version:** 1.0.0
