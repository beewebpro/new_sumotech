# Hướng Dẫn Chọn Giọng Nói Cho Từng Segment

## 📋 Tổng Quan

DubSync hiện hỗ trợ chọn giọng nam hoặc nữ riêng biệt cho mỗi segment, cho phép tạo narration audio với đa dạng giọng nói.

## 🎯 Tính Năng

### 1. **Chọn Giọng Nam/Nữ**
- Mỗi segment có dropdown để chọn giọng: **Nam (Male)** hoặc **Nữ (Female)**
- Giọng được lưu cùng với text của segment

### 2. **Chọn Giọng Nói Cụ Thể**
Tùy thuộc vào lựa chọn giọng Nam hay Nữ, có thể chọn các giọng cụ thể:

#### **Giọng Nữ (Female)**
- `vi-VN-Standard-A` - Nữ A (Standard)
- `vi-VN-Standard-B` - Nữ B (Standard)
- `vi-VN-Studio-A` - Nữ A (Studio) *
- `vi-VN-Studio-B` - Nữ B (Studio) *

#### **Giọng Nam (Male)**
- `vi-VN-Standard-C` - Nam C (Standard)
- `vi-VN-Standard-D` - Nam D (Standard)
- `vi-VN-Studio-C` - Nam C (Studio) *
- `vi-VN-Studio-D` - Nam D (Studio) *

*Studio voices có chất lượng cao hơn nhưng có thể yêu cầu setup Google Cloud riêng

### 3. **Tự động Cập nhật**
- Khi chọn giọng Nam hoặc Nữ, danh sách giọng nói sẽ tự động cập nhật
- Lựa chọn được lưu tự động khi click "Lưu đoạn" hoặc "Tạo TTS"

## 🚀 Cách Sử Dụng

### Bước 1: Mở Dự Án
1. Đi tới trang Chỉnh Sửa Dự Án (Edit Project)
2. Xem danh sách các segment

### Bước 2: Chọn Giọng Cho Mỗi Segment
```
Mỗi segment sẽ có:
┌─────────────────────────────┐
│ Đoạn 1 (0.00s - 5.50s)     │
├─────────────────────────────┤
│ [Textarea với nội dung]     │
├─────────────────────────────┤
│ Giọng: [ Nữ ▼ ]            │
│ Giọng nói: [ Chọn... ▼ ]  │
└─────────────────────────────┘
```

### Bước 3: Thay Đổi Giọng
1. Click dropdown "Giọng" để chọn **Nam** hoặc **Nữ**
2. Danh sách "Chọn giọng nói" sẽ cập nhật tự động
3. Chọn giọng nói cụ thể từ dropdown thứ hai

### Bước 4: Lưu Lựa Chọn
- Click nút **"Lưu đoạn"** để lưu giọng đã chọn
- Hoặc click **"Tạo TTS"** để tạo audio với giọng đã chọn

## 🔊 Ví Dụ Sử Dụng

### Scenario: Tạo Narration Với Nhiều Giọng

```
Segment 1: "Chào mừng bạn đến..." 
→ Chọn: Nữ - vi-VN-Standard-A

Segment 2: "Hôm nay chúng ta sẽ..."
→ Chọn: Nam - vi-VN-Standard-C

Segment 3: "Đây là một bài học..."
→ Chọn: Nữ - vi-VN-Studio-B
```

Kết quả: Audio narration sẽ lồng tiếng với 2 giọng nữ khác nhau và 1 giọng nam

## 💾 Cơ Cấu Dữ Liệu

### Segment Data Structure
```json
{
  "index": 0,
  "text": "Nội dung đoạn",
  "voice_gender": "female",
  "voice_name": "vi-VN-Standard-A",
  "start_time": 0.00,
  "end_time": 5.50,
  "duration": 5.50
}
```

### Saved Fields
- `voice_gender`: "male" | "female" (Mặc định: "female")
- `voice_name`: Voice code (Mặc định: null - sẽ dùng giọng mặc định)

## 🔧 Backend Implementation

### TTSService (app/Services/TTSService.php)

```php
// Tạo audio với giọng cụ thể
$ttsService->generateAudio(
    text: "Nội dung",
    index: 0,
    voiceGender: "female",
    voiceName: "vi-VN-Standard-A"
);

// Lấy danh sách giọng nói
$voices = TTSService::getAvailableVoices('female');
$allVoices = TTSService::getAllVoices();
```

### API Endpoints

#### Get Available Voices
```
GET /get-available-voices?gender=female
GET /get-available-voices?gender=male
GET /get-available-voices?gender=all
```

Response:
```json
{
  "success": true,
  "voices": {
    "female": {
      "vi-VN-Standard-A": "Nữ A (Standard)",
      "vi-VN-Standard-B": "Nữ B (Standard)"
    }
  }
}
```

#### Save Segments with Voices
```
POST /dubsync/projects/{projectId}/save-segments

Body:
{
  "segments": [
    {
      "index": 0,
      "text": "...",
      "voice_gender": "female",
      "voice_name": "vi-VN-Standard-A"
    }
  ]
}
```

#### Generate TTS with Voices
```
POST /dubsync/projects/{projectId}/generate-tts
```

System sẽ tự động sử dụng `voice_gender` và `voice_name` từ mỗi segment

## 📝 JavaScript Functions

### Hàm Chính

```javascript
// Fetch danh sách giọng nói
async function fetchAvailableVoices(gender)

// Cập nhật dropdown giọng nói
async function updateVoiceOptions(segmentIndex, gender, selectedVoice)

// Lưu tất cả lựa chọn giọng
function saveVoiceSelections()
```

### Event Listeners

- **Gender Change**: Tự động cập nhật danh sách giọng nói khi thay đổi Nam/Nữ
- **Auto Save**: Lưu giọng đã chọn trước khi gửi request đến server

## 🐛 Troubleshooting

### Vấn Đề: Dropdown giọng nói trống
**Giải pháp**: 
1. Kiểm tra Google Cloud TTS API key trong `.env`
2. Xem browser console cho lỗi
3. Đảm bảo chọn giọng Nam hoặc Nữ trước

### Vấn Đề: Giọng không thay đổi khi tạo TTS
**Giải pháp**:
1. Đảm bảo đã lưu segment trước khi tạo TTS
2. Kiểm tra trong database rằng `voice_gender` và `voice_name` đã được lưu
3. Xem logs trong `storage/logs/laravel.log`

### Vấn Đề: Lỗi khi tạo TTS
**Giải pháp**:
1. Kiểm tra Google Cloud API key
2. Đảm bảo đã thay đổi segment text và giọng
3. Thử regenerate segment

## 🔐 Security Notes

- Voice settings được validate ở backend
- API key không được expose ở frontend
- Tất cả requests phải authenticated (nếu cấu hình)

## 📚 Tham Khảo

- [Google Cloud Text-to-Speech API](https://cloud.google.com/text-to-speech/docs)
- [Vietnamese Voices Documentation](https://cloud.google.com/text-to-speech/docs/voices)
- TTSService: `app/Services/TTSService.php`
- DubSyncController: `app/Http/Controllers/DubSyncController.php`

## 🎓 Mẹo Sử Dụng

1. **Luân phiên giọng**: Dùng 2 giọng nữ hoặc 2 giọng nam khác nhau để tránh nghe nhàm
2. **Nhấn mạnh**: Dùng giọng nam cho những đoạn cần nhấn mạnh
3. **Câu hỏi**: Có thể dùng intonation khác nhau cho câu hỏi
4. **Test**: Tạo TTS cho 1-2 segment đầu tiên để test trước khi tạo tất cả

---

**Version**: 1.0  
**Last Updated**: 2026-01-29
