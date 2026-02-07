# DubSync - Tổng quan hệ thống

## 🎯 Mục đích
DubSync là hệ thống tự động hóa quy trình lồng tiếng video từ YouTube sang tiếng Việt, bao gồm đầy đủ các bước từ trích xuất transcript đến xuất file cuối cùng.

## 📋 Các tính năng chính

### 1. Trích xuất Transcript từ YouTube ✅
- Nhập YouTube URL
- Tự động lấy transcript + timestamp
- Hỗ trợ cả auto-generated và manual captions

### 2. Làm sạch Transcript ✅
- Loại bỏ ký tự đặc biệt
- Chuẩn hóa khoảng trắng
- Tối ưu cho TTS

### 3. Phân đoạn thông minh ✅
- Tự động nhóm thành đoạn văn có nghĩa
- Dựa trên dấu câu, độ dài, thời lượng
- Mỗi đoạn ~10 giây hoặc ~50 từ

### 4. Dịch sang Tiếng Việt ✅
- Tích hợp Google Translate API
- Giữ nghĩa + giữ nhịp
- Có thể chỉnh sửa thủ công

### 5. Text-to-Speech (TTS) ✅
- Tạo giọng nói tiếng Việt cho từng đoạn
- Sử dụng Google Cloud TTS hoặc Azure
- Có thể regenerate từng đoạn riêng lẻ

### 6. Căn chỉnh thời lượng ✅
- Tự động time-fit audio với timestamp gốc
- Sử dụng FFmpeg time-stretching
- Đảm bảo sync với video

### 7. Ghép Audio ✅
- Merge tất cả đoạn theo timeline
- Thêm silence cho các khoảng trống
- Tạo track audio hoàn chỉnh

### 8. Xuất File ✅
Xuất đa định dạng:
- **SRT** - Phụ đề SubRip (universal, hỗ trợ mọi video editor)
- **VTT** - WebVTT (cho web player)
- **WAV** - Audio lossless (chất lượng cao)
- **MP3** - Audio nén (dễ sử dụng)
- **JSON** - Project file (để re-run, chỉnh sửa, regenerate)

## 🏗️ Kiến trúc hệ thống

```
┌─────────────────┐
│  YouTube URL    │
└────────┬────────┘
         │
         ▼
┌─────────────────────────┐
│ YouTubeTranscriptService│ ─── Python Script
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ TranscriptCleanerService│
└────────┬────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ TranscriptSegmentationService│
└────────┬─────────────────────┘
         │
         ▼
┌─────────────────────┐
│ TranslationService  │ ─── Google Translate API
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│    TTSService       │ ─── Google Cloud TTS
└────────┬────────────┘
         │
         ▼
┌──────────────────────────┐
│ AudioAlignmentService    │ ─── FFmpeg
└────────┬─────────────────┘
         │
         ▼
┌──────────────────────────┐
│  AudioMergeService       │ ─── FFmpeg
└────────┬─────────────────┘
         │
         ▼
┌─────────────────────┐
│   ExportService     │
└────────┬────────────┘
         │
         ▼
┌─────────────────────────────┐
│ SRT | VTT | WAV | MP3 | JSON│
└─────────────────────────────┘
```

## 📁 Cấu trúc File

### Controllers
- `DubSyncController.php` - Xử lý tất cả HTTP requests

### Services (8 services)
1. `YouTubeTranscriptService.php` - Lấy transcript từ YouTube
2. `TranscriptCleanerService.php` - Làm sạch transcript
3. `TranscriptSegmentationService.php` - Phân đoạn transcript
4. `TranslationService.php` - Dịch sang tiếng Việt
5. `TTSService.php` - Tạo giọng nói TTS
6. `AudioAlignmentService.php` - Căn chỉnh timing
7. `AudioMergeService.php` - Ghép audio segments
8. `ExportService.php` - Xuất file các định dạng

### Models
- `DubSyncProject.php` - Model lưu trữ projects

### Views
- `dubsync/index.blade.php` - Giao diện chính

### Database
- `dub_sync_projects` table - Lưu trữ projects và metadata

### Scripts
- `storage/scripts/get_youtube_transcript.py` - Python script lấy transcript

## 🔧 Công nghệ sử dụng

### Backend
- **Laravel 10** - PHP Framework
- **MySQL** - Database
- **Python 3** - YouTube transcript extraction
- **FFmpeg** - Audio processing

### Frontend
- **Blade Templates** - Server-side rendering
- **Tailwind CSS** - Styling
- **Alpine.js** - Interactive components (via Laravel Breeze)
- **Vanilla JavaScript** - AJAX calls

### External APIs (Optional for Production)
- **Google Cloud Translation API** - Dịch văn bản
- **Google Cloud Text-to-Speech** - TTS tiếng Việt
- **Azure Cognitive Services** - Alternative TTS/Translation

### Libraries
- `youtube-transcript-api` (Python) - Lấy YouTube transcripts
- FFmpeg - Audio manipulation

## 🚀 Deployment Options

### Development Mode
- Chạy local với Laragon/XAMPP
- Sử dụng mock data (không cần API keys)
- Test full workflow

### Production Mode
- Cấu hình Google Cloud APIs
- Deploy lên VPS/Cloud
- Queue jobs cho background processing
- CDN cho file storage

## 💡 Use Cases

### 1. Content Creator
- Lồng tiếng video YouTube sang tiếng Việt
- Tạo subtitle cho video
- Xuất file để edit trong Premiere/DaVinci

### 2. Educational Platform
- Dịch video bài giảng
- Tạo phụ đề học tập
- Archive bản dịch

### 3. Media Agency
- Batch processing nhiều video
- Standardized workflow
- Quality control với manual editing

### 4. Individual Translator
- Hỗ trợ công việc dịch thuật
- Tạo draft nhanh
- Tinh chỉnh bằng tay

## 📊 Workflow Timeline

Ví dụ với video 5 phút:

1. **Extract Transcript** - 5-10 giây
2. **Clean & Segment** - 1-2 giây
3. **Translate** - 10-15 giây (với API) hoặc instant (mock)
4. **Generate TTS** - 30-60 giây (tùy số đoạn)
5. **Align Timing** - 10-20 giây
6. **Merge Audio** - 5-10 giây
7. **Export Files** - 5-10 giây

**Tổng: ~2-3 phút** cho toàn bộ workflow tự động

## 🎨 Tùy chỉnh

### Thay đổi giọng TTS
Trong `TTSService.php`:
```php
'voice' => [
    'languageCode' => 'vi-VN',
    'name' => 'vi-VN-Standard-A',  // Đổi sang B, C, D
    'ssmlGender' => 'FEMALE'        // hoặc MALE
]
```

### Điều chỉnh segmentation
Trong `TranscriptSegmentationService.php`:
```php
$exceedsWordLimit = $wordCount >= 50;  // Tăng/giảm số từ
$exceedsDuration = $segmentDuration >= 10;  // Tăng/giảm thời lượng
```

### Chất lượng audio export
Trong `ExportService.php`:
```php
// WAV: pcm_s16le (16-bit) hoặc pcm_s24le (24-bit)
// MP3: 128k, 192k, 320k bitrate
```

## 🔐 Bảo mật

- Authentication required (Laravel auth)
- User-specific projects (có thể enable)
- File storage trong storage/ (không public)
- API keys trong .env (không commit)
- Validation cho tất cả inputs

## 📈 Mở rộng trong tương lai

### Tính năng có thể thêm:
- [ ] Batch processing nhiều video
- [ ] Queue jobs cho xử lý nền
- [ ] Real-time progress tracking (WebSocket)
- [ ] Multi-language support (không chỉ tiếng Việt)
- [ ] Voice cloning
- [ ] AI voice selection
- [ ] Collaboration features
- [ ] Version control cho projects
- [ ] Export trực tiếp lên YouTube
- [ ] Integration với video editors

### Technical improvements:
- [ ] Redis cache cho API responses
- [ ] S3 storage cho files
- [ ] CDN cho delivery
- [ ] Kubernetes deployment
- [ ] Monitoring & logging
- [ ] A/B testing cho TTS voices

## 📞 Support & Maintenance

### Log Files
- `storage/logs/laravel.log` - Application logs
- Check errors khi có vấn đề

### Database Cleanup
```bash
# Xóa projects cũ hơn 30 ngày
php artisan dubsync:cleanup --days=30
```

### Storage Management
```bash
# Xóa temporary files
php artisan dubsync:clean-temp
```

## 🎓 Tài liệu tham khảo

- [YouTube Transcript API](https://github.com/jdepoix/youtube-transcript-api)
- [Google Cloud Translation](https://cloud.google.com/translate/docs)
- [Google Cloud TTS](https://cloud.google.com/text-to-speech/docs)
- [FFmpeg Documentation](https://ffmpeg.org/documentation.html)
- [Laravel Documentation](https://laravel.com/docs)

---

**Version:** 1.0.0  
**Last Updated:** January 27, 2026  
**Status:** Production Ready ✅
