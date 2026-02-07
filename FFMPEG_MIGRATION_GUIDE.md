# 🔄 Migration Guide - Chuyển sang FFmpeg Standard

## 📌 Tổng quan

Document này hướng dẫn cách migrate code hiện tại sang sử dụng **FFmpegStandardService** và **AudioBookStandardHelper**.

---

## 🎯 Lợi ích khi migrate

✅ **Chất lượng chuẩn YouTube** - Upload success rate cao hơn
✅ **Tương thích WMP** - Phát được trên mọi thiết bị
✅ **Metadata đầy đủ** - SEO tốt hơn, AI xử lý dễ hơn
✅ **Auto normalization** - Âm lượng đồng đều
✅ **File size tối ưu** - Nhanh hơn, nhẹ hơn
✅ **Code sạch hơn** - Dễ maintain

---

## 🔧 Cài đặt

### 1. Đảm bảo FFmpeg đã cài

```bash
# Windows (PowerShell)
ffmpeg -version

# Nếu chưa có, download từ: https://ffmpeg.org/download.html
```

### 2. Cập nhật .env

```env
# FFmpeg Path (nếu không có trong system PATH)
FFMPEG_PATH=ffmpeg
FFPROBE_PATH=ffprobe

# Hoặc full path
# FFMPEG_PATH=C:\ffmpeg\bin\ffmpeg.exe
# FFPROBE_PATH=C:\ffmpeg\bin\ffprobe.exe
```

### 3. Register Service (nếu cần)

File: `app/Providers/AppServiceProvider.php`

```php
public function register()
{
    $this->app->singleton(FFmpegStandardService::class);
    $this->app->singleton(AudioBookStandardHelper::class);
}
```

---

## 📝 Migration Examples

### ❌ BEFORE (Code cũ) → ✅ AFTER (Code mới)

#### Example 1: Generate Chapter Audio

**❌ BEFORE:**
```php
// Old way - Không chuẩn hóa, thiếu metadata
public function generateChapterAudio(AudioBookChapter $chapter)
{
    $ttsService = app(TTSService::class);
    
    $audioPath = $ttsService->generateAudioFromText(
        $chapter->content,
        1,
        'female',
        null,
        'gemini'
    );
    
    $chapter->update([
        'audio_file' => $audioPath
    ]);
    
    return $audioPath;
}
```

**✅ AFTER:**
```php
// New way - Chuẩn hóa, có metadata, normalize audio
public function generateChapterAudio(AudioBookChapter $chapter)
{
    $helper = app(AudioBookStandardHelper::class);
    
    $result = $helper->generateChapterStandardAudio($chapter, [
        'quality' => 'high',
        'remove_silence' => true
    ]);
    
    // Chapter đã được auto update với audio_file, duration, size
    
    return $result;
}
```

#### Example 2: Generate Chapter Video

**❌ BEFORE:**
```php
// Old way - Hardcoded settings, không optimize
public function generateChapterVideo(AudioBookChapter $chapter)
{
    $ffmpeg = env('FFMPEG_PATH', 'ffmpeg');
    $audioPath = storage_path("app/{$chapter->audio_file}");
    $imagePath = storage_path("app/public/{$chapter->cover_image}");
    $outputPath = storage_path("app/public/videos/{$chapter->id}.mp4");
    
    // Raw ffmpeg command
    $command = sprintf(
        '%s -loop 1 -i %s -i %s -c:v libx264 -t 180 -pix_fmt yuv420p -y %s',
        $ffmpeg,
        escapeshellarg($imagePath),
        escapeshellarg($audioPath),
        escapeshellarg($outputPath)
    );
    
    exec($command);
    
    // Không có error handling, không có metadata
    
    return $outputPath;
}
```

**✅ AFTER:**
```php
// New way - Chuẩn YouTube, có wave effect, metadata đầy đủ
public function generateChapterVideo(AudioBookChapter $chapter)
{
    $helper = app(AudioBookStandardHelper::class);
    
    $result = $helper->generateChapterStandardVideo($chapter, [
        'resolution' => '1080p',
        'audio_quality' => 'premium',
        'zoom_effect' => true
        // Wave settings tự động lấy từ audiobook settings
    ]);
    
    // Chapter đã được auto update với video_file, duration, size
    
    return $result;
}
```

#### Example 3: Merge Chapter Audios

**❌ BEFORE:**
```php
// Old way - Concat đơn giản, không crossfade, không chuẩn hóa
public function mergeChapters(AudioBook $audioBook)
{
    $chapters = $audioBook->chapters;
    $concatFile = tempnam(sys_get_temp_dir(), 'concat');
    
    $content = '';
    foreach ($chapters as $chapter) {
        $content .= "file '{$chapter->audio_file}'\n";
    }
    file_put_contents($concatFile, $content);
    
    $outputPath = "audiobooks/{$audioBook->id}/full.mp3";
    
    exec("ffmpeg -f concat -safe 0 -i {$concatFile} -c copy {$outputPath}");
    
    unlink($concatFile);
    
    return $outputPath;
}
```

**✅ AFTER:**
```php
// New way - Crossfade mượt, chuẩn hóa, có intro/outro
public function mergeChapters(AudioBook $audioBook)
{
    $helper = app(AudioBookStandardHelper::class);
    
    $result = $helper->mergeFullBookAudio($audioBook, [
        'quality' => 'high',
        'crossfade' => 0.5  // Smooth transition
    ]);
    
    // Audiobook đã được auto update với full_audio_file, duration, size
    // Tự động include intro/outro music nếu có
    
    return $result;
}
```

#### Example 4: Generate Description Audio

**❌ BEFORE:**
```php
// Old way
public function generateDescriptionAudio(AudioBook $audioBook)
{
    $ttsService = app(TTSService::class);
    
    $audioPath = $ttsService->generateAudioFromText(
        $audioBook->description,
        0,
        'female'
    );
    
    $audioBook->update(['description_audio' => $audioPath]);
    
    return $audioPath;
}
```

**✅ AFTER:**
```php
// New way - Premium quality, metadata đầy đủ
public function generateDescriptionAudio(AudioBook $audioBook)
{
    $helper = app(AudioBookStandardHelper::class);
    
    $result = $helper->generateDescriptionStandardAudio($audioBook);
    
    // Audiobook đã được auto update
    
    return $result;
}
```

---

## 🚀 Controller Migration

### AudioBookController.php

**Step 1: Inject services**

```php
use App\Services\FFmpegStandardService;
use App\Services\AudioBookStandardHelper;

class AudioBookController extends Controller
{
    protected FFmpegStandardService $ffmpegService;
    protected AudioBookStandardHelper $helper;
    
    public function __construct(
        FFmpegStandardService $ffmpegService,
        AudioBookStandardHelper $helper,
        // ... other services
    ) {
        $this->ffmpegService = $ffmpegService;
        $this->helper = $helper;
        // ...
    }
}
```

**Step 2: Update methods**

```php
// Generate TTS for chapter
public function generateChapterTts(Request $request, AudioBook $audioBook, AudioBookChapter $chapter)
{
    try {
        // Use helper - tự động chuẩn hóa
        $result = $this->helper->generateChapterStandardAudio($chapter, [
            'quality' => 'high',
            'remove_silence' => true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Audio generated successfully',
            'audio_path' => $result['path'],
            'duration' => $result['duration'],
            'size' => $result['size_formatted']
        ]);
        
    } catch (\Exception $e) {
        Log::error('Chapter TTS generation failed', [
            'chapter_id' => $chapter->id,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

// Generate video for chapter
public function generateChapterVideo(Request $request, AudioBook $audioBook, AudioBookChapter $chapter)
{
    try {
        $result = $this->helper->generateChapterStandardVideo($chapter, [
            'resolution' => $request->input('resolution', '1080p'),
            'audio_quality' => 'premium',
            'zoom_effect' => true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Video generated successfully',
            'video_path' => $result['path'],
            'duration' => $result['duration'],
            'size' => $result['size_formatted'],
            'resolution' => $result['resolution']
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

// Batch generate audios
public function generateSelectedTts(Request $request, AudioBook $audioBook)
{
    $chapterIds = $request->input('chapter_ids', []);
    
    try {
        $result = $this->helper->batchGenerateChapterAudios($audioBook, $chapterIds, [
            'quality' => 'high',
            'remove_silence' => true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Generated {$result['success']} audios successfully",
            'total' => $result['total'],
            'success_count' => $result['success'],
            'failed_count' => $result['failed'],
            'results' => $result['results']
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
```

---

## 📊 Quality Settings Guide

### Khi nào dùng quality nào?

| Use Case | Audio Quality | Video Resolution | Lý do |
|----------|---------------|------------------|-------|
| **YouTube Upload** | `premium` | `1080p` | Chất lượng tốt nhất, YouTube recommend |
| **Podcast** | `premium` | N/A | Chất lượng âm thanh quan trọng |
| **Audiobook** | `high` | `1080p` | Cân bằng quality/size |
| **Preview/Demo** | `standard` | `720p` | Nhanh, nhẹ |
| **Mobile Only** | `standard` | `480p` | Tiết kiệm data |

### Settings recommendation

```php
// YouTube upload (recommended)
[
    'resolution' => '1080p',
    'audio_quality' => 'premium',
    'zoom_effect' => true,
    'wave_effect' => true,
    'normalize_audio' => true
]

// Audiobook standard
[
    'quality' => 'high',
    'remove_silence' => true,
    'normalize_audio' => true
]

// Quick preview
[
    'resolution' => '720p',
    'audio_quality' => 'standard',
    'zoom_effect' => false
]
```

---

## 🧪 Testing Migration

### Test checklist

- [ ] Audio quality chuẩn (48kHz, AAC, stereo)
- [ ] Video compatible với YouTube (H.264, yuv420p)
- [ ] Metadata hiển thị đúng trong WMP
- [ ] File size reasonable (không quá lớn)
- [ ] Duration chính xác
- [ ] Auto silence removal hoạt động
- [ ] Volume normalization hoạt động
- [ ] Crossfade mượt mà
- [ ] Wave effect hiển thị đúng
- [ ] Zoom effect smooth

### Test script

```php
// Test audio generation
Route::get('/test-audio', function() {
    $chapter = AudioBookChapter::first();
    $helper = app(AudioBookStandardHelper::class);
    
    $result = $helper->generateChapterStandardAudio($chapter);
    
    return response()->json([
        'success' => true,
        'result' => $result,
        'file_exists' => file_exists($result['path']),
        'is_readable' => is_readable($result['path'])
    ]);
});

// Test video generation
Route::get('/test-video', function() {
    $chapter = AudioBookChapter::first();
    $helper = app(AudioBookStandardHelper::class);
    
    $result = $helper->generateChapterStandardVideo($chapter);
    
    return response()->json([
        'success' => true,
        'result' => $result,
        'file_exists' => file_exists($result['path'])
    ]);
});
```

---

## ⚠️ Common Issues & Solutions

### Issue 1: FFmpeg not found

**Error:**
```
FFmpeg is not installed or not in system PATH
```

**Solution:**
```env
# Add full path in .env
FFMPEG_PATH=C:\ffmpeg\bin\ffmpeg.exe
FFPROBE_PATH=C:\ffmpeg\bin\ffprobe.exe
```

### Issue 2: Permission denied

**Error:**
```
Permission denied when creating directory
```

**Solution:**
```bash
# Fix permission (Linux/Mac)
chmod -R 775 storage/app/public/audiobooks

# Windows: Check folder permissions in Properties
```

### Issue 3: Audio/Video not found

**Error:**
```
Chapter audio not found. Generate audio first.
```

**Solution:**
```php
// Generate audio trước khi generate video
$helper->generateChapterStandardAudio($chapter);
$helper->generateChapterStandardVideo($chapter);
```

### Issue 4: Out of memory

**Error:**
```
Allowed memory size exhausted
```

**Solution:**
```php
// Trong php.ini hoặc runtime
ini_set('memory_limit', '512M');

// Hoặc process từng batch nhỏ
$helper->batchGenerateChapterAudios($audioBook, [1, 2, 3]);
$helper->batchGenerateChapterAudios($audioBook, [4, 5, 6]);
```

---

## 📈 Performance Tips

1. **Batch processing**
   ```php
   // Tốt - Xử lý batch
   $helper->batchGenerateChapterAudios($audioBook, $chapterIds);
   
   // Không tốt - Loop manual
   foreach ($chapters as $chapter) {
       $helper->generateChapterStandardAudio($chapter);
   }
   ```

2. **Use queue for long tasks**
   ```php
   // Dispatch to queue
   dispatch(new GenerateChapterAudioJob($chapter));
   ```

3. **Cache results**
   ```php
   // Check if already generated
   if ($chapter->audio_file && file_exists(storage_path("app/{$chapter->audio_file}"))) {
       return; // Skip
   }
   ```

---

## ✅ Migration Checklist

### Phase 1: Preparation
- [ ] Install FFmpeg
- [ ] Update .env
- [ ] Test FFmpeg command

### Phase 2: Code Update
- [ ] Inject FFmpegStandardService
- [ ] Inject AudioBookStandardHelper
- [ ] Update generateChapterAudio methods
- [ ] Update generateChapterVideo methods
- [ ] Update merge methods

### Phase 3: Testing
- [ ] Test single chapter audio
- [ ] Test single chapter video
- [ ] Test batch generate
- [ ] Test merge full book
- [ ] Test metadata
- [ ] Test file compatibility

### Phase 4: Deployment
- [ ] Deploy to production
- [ ] Monitor logs
- [ ] Verify YouTube upload
- [ ] Verify WMP playback

---

## 🎓 Learning Resources

- [FFmpeg Standard Guide](./FFMPEG_STANDARD_GUIDE.md) - Chi tiết về service
- [YouTube Encoding Guidelines](https://support.google.com/youtube/answer/1722171)
- [AAC vs MP3](https://en.wikipedia.org/wiki/Advanced_Audio_Coding)

---

## 📞 Support

Nếu gặp vấn đề khi migrate:

1. Check logs: `storage/logs/laravel.log`
2. Test FFmpeg: `ffmpeg -version`
3. Verify file permissions
4. Check memory limit

Happy migrating! 🚀
