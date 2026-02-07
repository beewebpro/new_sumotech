# 🎬 FFmpeg Standard Service - Hướng Dẫn Sử Dụng

## 📋 Tổng quan

**FFmpegStandardService** là service chuyên dụng để chuẩn hóa việc tạo file MP3 và MP4 theo đúng chuẩn:
- ✅ **YouTube**: Đảm bảo upload thành công, xử lý nhanh
- ✅ **Windows Media Player**: Tương thích hoàn hảo
- ✅ **AI Processing**: Format chuẩn, metadata đầy đủ

---

## 🎵 Tạo MP3 Chuẩn

### Các mức chất lượng

| Quality | Codec | Bitrate | Sample Rate | Channels | Dung lượng | Khuyến nghị |
|---------|-------|---------|-------------|----------|------------|-------------|
| **premium** | AAC | 192k | 48000Hz | Stereo | ~1.4MB/phút | YouTube, podcast pro |
| **high** | AAC | 128k | 48000Hz | Stereo | ~1MB/phút | ✅ **Chuẩn** - đa số trường hợp |
| **standard** | AAC | 96k | 44100Hz | Stereo | ~750KB/phút | Audiobook, tiết kiệm dung lượng |

### Code mẫu

```php
use App\Services\FFmpegStandardService;

$ffmpegService = app(FFmpegStandardService::class);

// Cách 1: Chuẩn nhất - Chất lượng cao
$result = $ffmpegService->createStandardMP3(
    $inputPath,  // Input audio file
    $outputPath, // Output MP3 file
    [
        'quality' => 'high',           // premium | high | standard
        'remove_silence' => true,      // Tự động cắt khoảng lặng đầu/cuối
        'metadata' => [
            'title' => 'Tên bài hát/chương',
            'artist' => 'Tên tác giả',
            'album' => 'Tên sách/album',
            'author' => 'Người đọc',
            'description' => 'Mô tả',
            'year' => '2024',
            'genre' => 'Audiobook',
            'copyright' => 'Copyright info'
        ]
    ]
);

// Kết quả
/*
[
    'success' => true,
    'path' => '/path/to/output.mp3',
    'duration' => 180.5,              // seconds
    'size' => 2156789,                // bytes
    'size_formatted' => '2.06 MB',    // readable
    'quality' => 'high'
]
*/

// Cách 2: Đơn giản - Dùng mặc định
$result = $ffmpegService->createStandardMP3($inputPath, $outputPath);
```

### Tính năng tự động

✅ **Normalize Volume**: Tự động cân bằng âm lượng (loudness normalization)
✅ **Remove Silence**: Tự động cắt khoảng lặng đầu/cuối
✅ **Stereo Conversion**: Tự động convert sang stereo
✅ **Prevent Clipping**: Tránh méo tiếng khi âm lượng quá cao

---

## 🎬 Tạo MP4 Chuẩn

### Các độ phân giải

| Resolution | Kích thước | Bitrate | FPS | Dung lượng | Khuyến nghị |
|------------|-----------|---------|-----|------------|-------------|
| **1080p** | 1920x1080 | 10Mbps | 30 | ~75MB/phút | ✅ **YouTube chuẩn** - Full HD |
| **720p** | 1280x720 | 6Mbps | 30 | ~45MB/phút | HD - Tiết kiệm bandwidth |
| **480p** | 854x480 | 3Mbps | 30 | ~22MB/phút | SD - Mobile friendly |

### Code mẫu - Image to Video

```php
// Tạo video từ ảnh tĩnh + audio
$result = $ffmpegService->createStandardMP4(
    $imagePath,    // Ảnh bìa/background
    $audioPath,    // File audio
    $outputPath,   // Output MP4
    [
        'resolution' => '1080p',       // 1080p | 720p | 480p
        'audio_quality' => 'premium',  // premium | high | standard
        'zoom_effect' => true,         // Ken Burns effect (zoom nhẹ)
        'wave_effect' => false,        // Hiển thị sóng âm
        'wave_settings' => [
            'type' => 'line',          // line | p2p | cline
            'color' => 'white',        // Màu sóng
            'position' => 'bottom',    // top | middle | bottom
            'height' => 100,           // Chiều cao (px)
            'opacity' => 0.8           // Độ trong suốt (0-1)
        ],
        'metadata' => [
            'title' => 'Video Title',
            'description' => 'Video description',
            'author' => 'Channel Name'
        ]
    ]
);

// Kết quả
/*
[
    'success' => true,
    'path' => '/path/to/output.mp4',
    'duration' => 180.5,
    'size' => 135790000,
    'size_formatted' => '129.5 MB',
    'resolution' => '1080p',
    'video_quality' => [...],
    'audio_quality' => [...]
]
*/
```

### Code mẫu - Video to Video

```php
// Replace audio trong video có sẵn
$result = $ffmpegService->createStandardMP4(
    $videoPath,    // Video gốc
    $audioPath,    // Audio mới
    $outputPath,   // Output MP4
    [
        'resolution' => '1080p',
        'audio_quality' => 'premium',
        'normalize_audio' => true,  // Chuẩn hóa âm lượng
        'zoom_effect' => false      // Không zoom (vì đã là video)
    ]
);
```

---

## 🔗 Merge Audio Files

### Simple Merge (Nối đơn giản)

```php
$audioPaths = [
    'audio/intro.mp3',
    'audio/chapter1.mp3',
    'audio/chapter2.mp3',
    'audio/outro.mp3'
];

$result = $ffmpegService->mergeAudioFiles(
    $audioPaths,
    'output/merged.mp3',
    [
        'quality' => 'high',
        'crossfade' => 0  // Không crossfade
    ]
);
```

### Merge với Crossfade (Chuyển cảnh mượt)

```php
$result = $ffmpegService->mergeAudioFiles(
    $audioPaths,
    'output/merged_smooth.mp3',
    [
        'quality' => 'high',
        'crossfade' => 0.5  // 0.5 giây crossfade giữa các đoạn
    ]
);

// Kết quả
/*
[
    'success' => true,
    'path' => 'output/merged_smooth.mp3',
    'duration' => 720.5,
    'count' => 4,
    'crossfade' => 0.5
]
*/
```

---

## 🎨 Wave Effect (Hiệu ứng sóng âm)

### Các loại sóng

```php
// Line wave - Sóng đường thẳng (đơn giản, đẹp)
'wave_settings' => [
    'type' => 'line',
    'color' => 'white',
    'position' => 'bottom',
    'height' => 100,
    'opacity' => 0.8
]

// P2P wave - Sóng điểm nối điểm (dynamic)
'wave_settings' => [
    'type' => 'p2p',
    'color' => '#00ff00',
    'position' => 'middle',
    'height' => 150,
    'opacity' => 0.6
]

// CLine wave - Sóng đường cong (smooth)
'wave_settings' => [
    'type' => 'cline',
    'color' => 'cyan',
    'position' => 'top',
    'height' => 120,
    'opacity' => 0.7
]
```

---

## 📊 Metadata Standards

### Metadata đầy đủ cho AI & YouTube

```php
'metadata' => [
    // Basic Info
    'title' => 'Tiêu đề video/audio',
    'artist' => 'Tên nghệ sĩ/tác giả',
    'album' => 'Tên album/series',
    'author' => 'Người sáng tạo',
    
    // Description
    'description' => 'Mô tả chi tiết nội dung',
    'comment' => 'Ghi chú thêm',
    
    // Additional
    'year' => '2024',
    'genre' => 'Audiobook/Podcast/Music',
    'copyright' => '© 2024 Your Channel'
]
```

---

## ⚙️ Technical Specifications

### Video Codec Settings (YouTube Optimized)

```
- Codec: H.264 (libx264)
- Profile: High
- Level: 4.2
- Preset: slow (chất lượng cao nhất)
- Pixel Format: yuv420p (tương thích tốt nhất)
- GOP Size: 2x FPS (keyframe mỗi 2 giây)
- Flags: +faststart (streaming-friendly)
```

### Audio Codec Settings

```
- Codec: AAC (tương thích tốt nhất)
- Sample Rate: 48000Hz (chuẩn video)
- Channels: 2 (stereo)
- Loudness: -16 LUFS (YouTube standard)
- True Peak: -1.5 dB
- LRA: 11 LU
```

---

## 💡 Best Practices

### ✅ Nên làm

1. **Dùng quality `high` cho hầu hết trường hợp**
   - Cân bằng tốt giữa chất lượng và dung lượng
   - Phù hợp với YouTube, podcast

2. **Bật `remove_silence` cho audiobook**
   - Tự động cắt khoảng lặng
   - Trải nghiệm nghe tốt hơn

3. **Dùng `normalize_audio`**
   - Âm lượng đồng đều
   - Không bị chênh lệch giữa các đoạn

4. **Thêm metadata đầy đủ**
   - Tốt cho SEO
   - Chuyên nghiệp
   - AI xử lý tốt hơn

5. **Resolution 1080p cho YouTube**
   - Hiển thị đẹp trên mọi thiết bị
   - YouTube ưu tiên content HD

### ❌ Không nên

1. ❌ Dùng `premium` khi không cần thiết
   - File quá nặng
   - Upload lâu

2. ❌ Tắt `normalize_audio` khi merge nhiều file
   - Âm lượng không đồng đều
   - Trải nghiệm xấu

3. ❌ Dùng 480p cho video chính
   - Chỉ dùng cho preview/mobile
   - YouTube không ưu tiên

---

## 🔧 Integration Examples

### Trong AudioBookController

```php
use App\Services\FFmpegStandardService;

class AudioBookController extends Controller
{
    protected FFmpegStandardService $ffmpegService;
    
    public function __construct(FFmpegStandardService $ffmpegService)
    {
        $this->ffmpegService = $ffmpegService;
    }
    
    public function generateChapterAudio(Chapter $chapter)
    {
        // Tạo TTS audio (raw)
        $rawAudioPath = $this->ttsService->generateAudio($chapter->content);
        
        // Chuẩn hóa thành MP3 standard
        $standardPath = storage_path("audiobooks/{$chapter->id}/standard.mp3");
        
        $result = $this->ffmpegService->createStandardMP3(
            $rawAudioPath,
            $standardPath,
            [
                'quality' => 'high',
                'remove_silence' => true,
                'metadata' => [
                    'title' => $chapter->title,
                    'artist' => $chapter->audioBook->author,
                    'album' => $chapter->audioBook->title,
                    'author' => $chapter->audioBook->speaker->name ?? 'AI',
                    'genre' => 'Audiobook'
                ]
            ]
        );
        
        $chapter->update([
            'audio_path' => $result['path'],
            'audio_duration' => $result['duration'],
            'audio_size' => $result['size']
        ]);
        
        return $result;
    }
    
    public function generateChapterVideo(Chapter $chapter)
    {
        $audioPath = $chapter->audio_path;
        $imagePath = $chapter->cover_image ?? $chapter->audioBook->cover_image;
        $videoPath = storage_path("audiobooks/{$chapter->id}/video.mp4");
        
        $result = $this->ffmpegService->createStandardMP4(
            $imagePath,
            $audioPath,
            $videoPath,
            [
                'resolution' => '1080p',
                'audio_quality' => 'premium',
                'zoom_effect' => true,
                'wave_effect' => true,
                'wave_settings' => [
                    'type' => 'line',
                    'color' => 'white',
                    'position' => 'bottom',
                    'height' => 100,
                    'opacity' => 0.8
                ],
                'metadata' => [
                    'title' => $chapter->title,
                    'description' => $chapter->audioBook->description,
                    'author' => $chapter->audioBook->youtubeChannel->title
                ]
            ]
        );
        
        $chapter->update([
            'video_path' => $result['path'],
            'video_duration' => $result['duration'],
            'video_size' => $result['size']
        ]);
        
        return $result;
    }
}
```

---

## 🎯 Use Cases

### 1. Audiobook Chapter Audio
```php
$ffmpegService->createStandardMP3($rawTTS, $output, [
    'quality' => 'high',
    'remove_silence' => true,
    'metadata' => ['title' => 'Chapter 1']
]);
```

### 2. YouTube Video (Image + Audio)
```php
$ffmpegService->createStandardMP4($coverImage, $audio, $output, [
    'resolution' => '1080p',
    'zoom_effect' => true,
    'wave_effect' => true
]);
```

### 3. Merge Full Book Audio
```php
$ffmpegService->mergeAudioFiles($allChapters, $fullBook, [
    'quality' => 'high',
    'crossfade' => 0.5
]);
```

### 4. Podcast Episode
```php
$ffmpegService->createStandardMP3($recording, $output, [
    'quality' => 'premium',
    'metadata' => [
        'title' => 'Episode 1',
        'album' => 'Season 1',
        'genre' => 'Podcast'
    ]
]);
```

---

## 🚀 Performance Tips

1. **Preset `slow` vs `fast`**
   - `slow`: Chất lượng cao, file nhỏ hơn, encode lâu hơn
   - `fast`: Encode nhanh, file lớn hơn
   - Mặc định: `slow` (đáng để đợi)

2. **Batch Processing**
   ```php
   // Xử lý song song nhiều file
   foreach ($chapters as $chapter) {
       dispatch(new GenerateStandardAudioJob($chapter));
   }
   ```

3. **Cleanup Temp Files**
   - Service tự động cleanup
   - Không cần quản lý manual

---

## 📝 Changelog

### v1.0.0 (2024)
- ✅ MP3 standard với normalize, remove silence
- ✅ MP4 standard cho YouTube (1080p/720p/480p)
- ✅ Merge audio với crossfade
- ✅ Wave effect cho video
- ✅ Metadata đầy đủ
- ✅ Ken Burns effect cho image
- ✅ Auto quality settings

---

## 🔗 Related Documents

- [YouTube Upload Guidelines](https://support.google.com/youtube/answer/1722171)
- [FFmpeg Documentation](https://ffmpeg.org/documentation.html)
- [AAC Codec Specs](https://en.wikipedia.org/wiki/Advanced_Audio_Coding)

---

## 💬 Support

Nếu có vấn đề, check logs:
```php
Log::info('FFmpeg operation', ['service' => 'FFmpegStandardService']);
```

Hoặc xem error output trong exception message.
