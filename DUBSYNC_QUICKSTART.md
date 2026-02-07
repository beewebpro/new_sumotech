# DubSync - Quick Start Guide

## Khởi động nhanh DubSync

### Bước 1: Cài đặt Dependencies

```bash
# Cài đặt PHP dependencies
composer install

# Cài đặt Python dependencies
pip install -r requirements.txt

# Cài đặt Node.js dependencies
npm install
```

### Bước 2: Cấu hình Database

```bash
# Chạy migrations
php artisan migrate

# (Tùy chọn) Seed dữ liệu mẫu
php artisan db:seed
```

### Bước 3: Cài đặt FFmpeg

#### Windows (Laragon):
- Tải FFmpeg từ: https://ffmpeg.org/download.html
- Giải nén và thêm vào System PATH
- Hoặc đặt trong `C:\laragon\bin\ffmpeg`

#### Kiểm tra cài đặt:
```bash
ffmpeg -version
```

### Bước 4: Chạy ứng dụng

```bash
# Build frontend assets
npm run build

# Khởi động server (nếu dùng Laragon, bỏ qua bước này)
php artisan serve
```

### Bước 5: Truy cập DubSync

1. Đăng nhập vào ứng dụng
2. Nhấp vào "DubSync" trên thanh navigation
3. Nhập YouTube URL và bắt đầu!

## Quy trình sử dụng

### 1. Nhập YouTube URL
```
https://www.youtube.com/watch?v=VIDEO_ID
```

### 2. Xử lý tự động
- Hệ thống tự động lấy transcript
- Phân đoạn thành các câu có nghĩa
- Sẵn sàng để dịch

### 3. Dịch sang tiếng Việt
- Nhấp "Dịch sang tiếng Việt"
- Chỉnh sửa bản dịch nếu cần
- Kiểm tra và điều chỉnh

### 4. Tạo giọng nói
- Nhấp "Tạo giọng nói TTS"
- Hệ thống tạo audio cho từng đoạn
- Có thể tạo lại cho đoạn cụ thể

### 5. Căn chỉnh thời lượng
- Nhấp "Căn chỉnh thời lượng"
- Tự động điều chỉnh tốc độ audio
- Đảm bảo khớp với video gốc

### 6. Ghép audio
- Nhấp "Ghép audio"
- Tạo track audio hoàn chỉnh
- Chuẩn bị cho xuất file

### 7. Xuất file
Chọn định dạng cần thiết:
- ✅ SRT - Phụ đề SubRip (universal)
- ✅ VTT - WebVTT (cho web)
- ✅ WAV - Audio chất lượng cao
- ✅ MP3 - Audio nén
- ✅ JSON - File project để chỉnh sửa lại

## Chế độ Development vs Production

### Development (Mặc định)
- Sử dụng mock translation
- Sử dụng placeholder audio
- Không cần API keys
- Phù hợp để test workflow

### Production
Cần cấu hình API keys trong `.env`:

```env
GOOGLE_TRANSLATE_API_KEY=your_key_here
GOOGLE_TTS_API_KEY=your_key_here
```

## Tính năng nâng cao

### Chỉnh sửa đoạn cụ thể
- Nhấp vào textarea của bất kỳ đoạn nào
- Chỉnh sửa text
- Nhấp "Tạo lại giọng nói cho đoạn này"

### Xuất lại project
- File JSON chứa toàn bộ thông tin
- Có thể import lại để chỉnh sửa
- Regenerate TTS cho các đoạn đã sửa

### Quản lý dự án
- Xem danh sách dự án trong "Dự án gần đây"
- Tải lại file đã export
- Xóa dự án không cần thiết

## Xử lý lỗi thường gặp

### Lỗi: "Failed to get transcript"
- Kiểm tra YouTube URL có đúng không
- Video có bật phụ đề không
- Thử video khác để test

### Lỗi: FFmpeg not found
```bash
# Kiểm tra FFmpeg
ffmpeg -version

# Nếu không có, cài đặt lại
# Windows: Tải và thêm vào PATH
# Linux: sudo apt-get install ffmpeg
```

### Lỗi: Python script failed
```bash
# Kiểm tra Python
python --version

# Cài đặt lại dependencies
pip install -r requirements.txt
```

## Tips & Tricks

### Tối ưu chất lượng dịch
- Chỉnh sửa bản dịch thủ công cho chính xác
- Giữ độ dài câu tương đương với bản gốc
- Sử dụng từ ngữ tự nhiên, dễ đọc cho TTS

### Tối ưu timing
- Hệ thống tự động căn chỉnh tốc độ
- Nếu quá nhanh/chậm, sửa lại text cho ngắn/dài hơn
- Regenerate TTS sau khi sửa

### Làm việc với video dài
- Hệ thống tự động phân đoạn
- Mỗi đoạn ~10 giây hoặc ~50 từ
- Có thể xử lý video hàng giờ

## Workflow chuyên nghiệp

1. **Chuẩn bị**
   - Chọn video có transcript tốt
   - Kiểm tra chất lượng âm thanh gốc

2. **Xử lý**
   - Chạy pipeline tự động
   - Review từng đoạn sau khi dịch

3. **Tinh chỉnh**
   - Sửa lại bản dịch không tự nhiên
   - Regenerate TTS cho các đoạn quan trọng
   - Test timing với video gốc

4. **Xuất file**
   - Export tất cả định dạng
   - Backup file JSON
   - Test với video editor (Premiere, DaVinci, etc.)

5. **Lồng tiếng**
   - Import SRT vào video editor
   - Import audio track
   - Sync và render video final

## Hỗ trợ

Nếu gặp vấn đề, kiểm tra:
1. Log files trong `storage/logs/`
2. Database có dữ liệu không
3. Quyền ghi file trong `storage/`
4. Network khi call API

## Next Steps

- Cấu hình API keys cho production
- Tùy chỉnh giọng đọc TTS
- Tích hợp với workflow hiện tại
- Automation với Queue jobs

Chúc bạn sử dụng DubSync hiệu quả! 🎬🎙️
