# 🎬 Hướng Dẫn Tạo Video Lip-sync Composite

## 📋 Tổng Quan

Hệ thống này tạo video giới thiệu sách tổng hợp với:
- ✅ **Lip-sync segments** (tối đa 60s D-ID budget)
- ✅ **Media xen kẽ** (thumbnails/scenes/animations)
- ✅ **Intro/Outro music** với fade effects
- ✅ **Background music** nhẹ xuyên suốt
- ✅ **FFmpeg transitions** động (fade, slide, wipe, dissolve)
- ✅ **Avatar overlay** thu nhỏ trên media segments

## 🎯 Workflow Hoàn Chỉnh

### 1️⃣ **Audio Generation**
```
User nhập description → TTS Service → Full Audio MP3
```

### 2️⃣ **Segment Planning** (LipsyncSegmentManager)
```php
Duration ≤60s  → Full lip-sync
60s-120s       → Intro (15s) + Middle (10s) + Outro (15s) + 2 Media gaps
>120s          → Intro (20s) + N Middle checkpoints + Outro (20s) + Media gaps
```

**Ví dụ với audio 180s:**
- Segment 1: **Lip-sync** 0-20s (intro)
- Segment 2: **Media** 20-50s  
- Segment 3: **Lip-sync** 50-60s (middle_1)
- Segment 4: **Media** 60-90s
- Segment 5: **Lip-sync** 90-100s (middle_2)
- Segment 6: **Media** 100-130s
- Segment 7: **Lip-sync** 160-180s (outro)

**Tổng D-ID usage: 20+10+10+20 = 60s** ✅

### 3️⃣ **Lip-sync Generation** (DIDLipsyncService)
```
Segment audio → D-ID API → MP4 lip-sync video
```
- Upload audio to D-ID
- Upload speaker avatar
- Create talk with driver `bank://lively`
- Poll cho đến khi video ready
- Download về local storage

### 4️⃣ **Media Segment Creation** (VideoCompositionService)
```
Random media + Audio segment → FFmpeg → Video with avatar overlay
```

**Image media:**
- Scale to 1920x1080
- Apply ken burns zoom effect
- Circular avatar overlay (200x200px) ở góc phải trên
- Sync với audio segment

**Video media:**
- Loop if needed
- Avatar overlay
- Sync với audio segment

### 5️⃣ **Video Composition** (VideoCompositionService)
```
All segments → FFmpeg xfade → Transitions → Music → Final MP4
```

**Quy trình:**
1. Concatenate segments với random transitions (0.5s)
2. Add background music (15% volume)
3. Add intro music với fade out
4. Add outro music
5. Output final composite video

## 🔧 Cấu Hình

### Environment Variables (.env)
```env
DID_API_KEY=your_d_id_api_key_here
FFMPEG_PATH=ffmpeg
FFPROBE_PATH=ffprobe
```

### Database Schema
```sql
ALTER TABLE audio_books ADD COLUMN description_lipsync_video VARCHAR(255) NULL;
ALTER TABLE audio_books ADD COLUMN description_lipsync_duration INT NULL;
```

### File Structure
```
storage/app/public/
├── books/{book_id}/
│   ├── description_{timestamp}.mp3        # Audio full
│   ├── description_composite_{timestamp}.mp4  # Video final
│   ├── media/
│   │   ├── thumbnails/
│   │   │   └── *.jpg
│   │   ├── scenes/
│   │   │   └── *.jpg
│   │   └── animations/
│   │       └── *.mp4
│   └── ...
└── music/
    └── bg_music_default.mp3  # Optional background music
```

## 🎵 Music Settings

### Intro Music
- Upload file MP3/WAV/M4A
- Fade out duration: 1-30s (default 3s)

### Outro Music
- Option 1: Use same as intro music
- Option 2: Upload riêng
- Fade in duration: 1-30s (default 3s)

### Background Music (Nhạc Nền)
- Tự động loop xuyên suốt video
- Volume: 15% (nhẹ, không át giọng nói)
- File: `storage/app/public/music/bg_music_default.mp3`

## 🎨 Media Gallery

### Cách Tạo Media
1. **Thumbnails**: Generate từ AI với style (realistic, anime, illustration, etc.)
2. **Scenes**: Generate hình minh họa cho video
3. **Animations**: Dùng Kling AI để animate từ static images

### Random Selection
System tự động chọn random từ tất cả media có sẵn:
- Ưu tiên: thumbnails > scenes > animations
- Fallback: cover image nếu không có media

## 🔄 Transitions

### Loại Transitions Available
- `fade` - Nhòa dần
- `wipeleft` / `wiperight` - Lau ngang
- `wipeup` / `wipedown` - Lau dọc
- `slideleft` / `slideright` - Trượt ngang
- `dissolve` - Hòa tan

Duration: 0.5s per transition

## 💰 Cost Estimate

### D-ID Pricing
- **$0.30 USD** per 60 seconds of video
- System tự động limit ≤60s → **Max $0.30/video**

### Ví Dụ
- Audio 30s → 1 video (30s) = **$0.15**
- Audio 90s → 3 segments (15+10+15 = 40s) = **$0.20**
- Audio 300s → 6 segments (total 60s) = **$0.30**

## 📊 Performance

### Processing Time
- Audio generation: ~5-10s
- Lip-sync per segment: ~30-60s
- Video composition: ~20-40s
- **Total**: ~2-5 minutes tùy độ dài audio

### Optimization Tips
1. Generate media trước khi tạo video (1-time cost)
2. Reuse segments nếu re-generate
3. Use background tasks cho long videos

## 🐛 Troubleshooting

### Video không tạo được
```
Check logs: storage/logs/laravel.log
Common issues:
- FFmpeg not installed
- D-ID API key invalid
- Speaker không có avatar
- Insufficient disk space
```

### Audio/Video không sync
```
→ Check audio extraction (ffprobe)
→ Verify segment timings
→ Check FFmpeg command output
```

### Quality Issues
```
→ CRF 23 (good balance)
→ Preset: fast (faster encoding)
→ Resolution: 1920x1080 (Full HD)
```

## 🚀 Usage Example

### Frontend (Blade)
```html
<input type="checkbox" id="descLipSyncEnabled">
<label>🎬 Tạo video Lip-sync cho giới thiệu</label>
```

### JavaScript
```javascript
const enableLipsync = document.getElementById('descLipSyncEnabled').checked;

fetch('/audiobooks/{id}/generate-description-audio', {
    method: 'POST',
    body: JSON.stringify({
        description: description,
        provider: 'gemini',
        voice_name: 'vi-VN-Wavenet-A',
        enable_lipsync: enableLipsync
    })
});
```

### Response
```json
{
  "success": true,
  "audio_url": "/storage/books/123/description_1234567890.mp3",
  "duration": 180.5,
  "video_url": "/storage/books/123/description_composite_1234567890.mp4",
  "video_duration": 185.2
}
```

## 📈 Future Enhancements

- [ ] Real-time progress tracking (WebSocket)
- [ ] Multiple avatar positions
- [ ] Custom transition speeds
- [ ] Subtitle overlay
- [ ] Adjustable bg music volume per segment
- [ ] Wav2Lip local alternative

## 📝 Notes

- System tự cleanup temp files sau khi xong
- Video được cache - xóa cũ khi generate mới
- Log details đầy đủ trong `storage/logs/laravel.log`
- Compatible với tất cả speakers có `lip_sync_enabled = true`

---

**Created by**: Sumotech Development Team
**Version**: 1.0.0
**Last Updated**: February 6, 2026
