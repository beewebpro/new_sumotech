# Test Plan: Generate Description Video Độc Lập

## ✅ Prerequisites
- [x] Audiobook đã có `description_audio`
- [x] Audiobook có speaker với `lip_sync_enabled = true`
- [x] Speaker có `avatar_url` hợp lệ (Cloudinary URL)

## 🧪 Test Cases

### Test 1: Tạo video từ audio có sẵn
**Steps:**
1. Vào trang audiobook có audio nhưng chưa có video
2. Click button **🎬 Tạo Video**
3. Confirm dialog

**Expected:**
- ✅ Button hiển thị "⏳ Đang tạo video..."
- ✅ Status: "🎬 Đang tạo video lip-sync..."
- ✅ Video được tạo và hiển thị trong player
- ✅ Checkbox lip-sync tự động được tick
- ✅ Success: "✅ Đã tạo video lip-sync thành công!"

### Test 2: Tạo lại video (avatar mới)
**Steps:**
1. Update avatar của MC sang URL Cloudinary mới
2. Vào audiobook đã có cả audio và video cũ
3. Click **🎬 Tạo Video** để tạo lại

**Expected:**
- ✅ Video cũ bị xóa
- ✅ Video mới được tạo với avatar mới
- ✅ Duration match với audio duration

### Test 3: Validation - Chưa có audio
**Steps:**
1. Vào audiobook chưa có audio
2. Button **🎬 Tạo Video** không hiển thị

**Expected:**
- ✅ Button hidden (vì điều kiện `@if ($audioBook->description_audio)`)

### Test 4: Validation - MC chưa có lip-sync
**Steps:**
1. Tạo MC mới với `lip_sync_enabled = false`
2. Assign cho audiobook
3. Button **🎬 Tạo Video** không hiển thị

**Expected:**
- ✅ Button hidden (vì điều kiện `$audioBook->speaker->lip_sync_enabled`)

### Test 5: API Endpoint trực tiếp
**cURL:**
```bash
curl -X POST http://sumotech.test/audiobooks/3/generate-description-video \
  -H "X-CSRF-TOKEN: your-token" \
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "video_url": "http://sumotech.test/storage/books/3/description_composite_xxx.mp4",
  "video_duration": 181.608,
  "message": "Video lip-sync đã được tạo thành công!"
}
```

---

## 🐛 Error Cases

### Error 1: Audio file bị xóa
**Response:**
```json
{
  "success": false,
  "error": "File audio không tồn tại."
}
```

### Error 2: Chưa có speaker
**Response:**
```json
{
  "success": false,
  "error": "Chưa có MC/Speaker. Vui lòng chọn MC trước."
}
```

### Error 3: D-ID API error
**Response:**
```json
{
  "success": false,
  "error": "Failed to concatenate segments"
}
```
→ Đã fix bằng cách scale tất cả videos về 1920x1080

---

## 📝 Notes

- **Performance**: ~2-3 phút cho video 3 phút (tùy số segments)
- **Cost**: Chỉ tính tiền D-ID (không tính TTS vì reuse audio)
- **File Size**: Video composite ~20-50MB tùy độ dài
- **Cleanup**: Video cũ tự động bị xóa khi tạo mới
