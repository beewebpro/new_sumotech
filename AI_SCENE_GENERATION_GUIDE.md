# 🎬 AI Scene Generation Guide

## 📖 Tổng Quan

Hệ thống tạo cảnh minh họa (scene generation) sử dụng AI để phân tích nội dung sách và tạo ra các hình ảnh minh họa logic theo trình tự phù hợp.

## 🧠 Cách Hoạt Động

### 1. **Phân Tích Nội Dung Bằng AI**
Hệ thống sử dụng **Gemini AI** để phân tích phần "Giới thiệu sách":

```
Nội dung sách → Gemini AI Analysis → AI tự quyết định số lượng scenes phù hợp
```

**Input:** Nội dung giới thiệu sách
**Output:** JSON array chứa các phân cảnh với:
- `scene_number`: Số thứ tự
- `title`: Tiêu đề cảnh
- `description`: Mô tả chi tiết
- `visual_prompt`: Prompt để tạo hình ảnh

**Số lượng scenes:** AI tự động xác định dựa trên độ dài và độ phức tạp của nội dung (thường 4-8 scenes)

### 2. **Tạo Hình Ảnh Bằng Banana Pro**
Từ kết quả phân tích, hệ thống sử dụng **Gemini Nano Banana Pro** để tạo ảnh:

```
Visual Prompt → Banana Pro → Hình ảnh 16:9
```

### 3. **Lưu Trữ Metadata**
Mỗi cảnh được lưu với 2 files:
- **Image file:** `scene_1_timestamp.png`
- **Metadata file:** `scene_1_timestamp.json`

## 🔧 Cấu Trúc Code

### GeminiImageService.php

#### generateVideoScenes()
Method chính để tạo scenes:

```php
public function generateVideoScenes($bookId, $numScenes = null)
{
    // 1. Lấy thông tin sách
    $audiobook = AudioBook::findOrFail($bookId);
    
    // 2. Phân tích nội dung bằng AI (AI tự quyết định số lượng nếu numScenes = null)
    $scenes = $this->analyzeDescriptionForScenes($audiobook->description, $numScenes);
    
    // 3. Tạo hình ảnh cho từng cảnh
    foreach ($scenes as $index => $sceneData) {
        // Tạo prompt từ AI analysis
        $prompt = $this->buildScenePromptFromAnalysis($sceneData, $audiobook);
        
        // Generate image với Banana Pro
        $image = $this->generateImage($prompt);
        
        // Lưu image + metadata
        $this->saveSceneWithMetadata($image, $sceneData, $index);
    }
}
```

#### analyzeDescriptionForScenes()
Phân tích nội dung thành các cảnh:

```php
private function analyzeDescriptionForScenes($description, $numScenes = null)
{
    // Build comprehensive analysis prompt
    $prompt = $this->buildAnalysisPrompt($description, $numScenes);
    
    // Call Gemini AI
    $response = $this->callGeminiAPI($prompt);
    
    // Parse JSON response (AI tự quyết định số lượng scenes nếu numScenes = null)
    return $this->parseSceneAnalysisResponse($response);
}
```

#### buildAnalysisPrompt()
Tạo prompt cho Gemini để phân tích:

```php
private function buildAnalysisPrompt($description, $numScenes = null)
{
    if ($numScenes) {
        return "Phân tích nội dung sau và tạo khoảng {$numScenes} phân cảnh minh họa logic.
        
    } else {
        return "Phân tích nội dung sau và TỰ ĐỘNG xác định số lượng phân cảnh PHÙ HỢP (4-8 scenes).
    
    - Nội dung ngắn/đơn giản: 3-5 scenes
    - Nội dung trung bình: 5-7 scenes  
    - Nội dung dài/phức tạp: 7-10 scenes
    
Yêu cầu output JSON:
[
  {
    \"scene_number\": 1,
    \"title\": \"Tiêu đề ngắn gọn\",
    \"description\": \"Mô tả chi tiết cảnh này\",
    \"visual_prompt\": \"Prompt để tạo hình ảnh\"
  }
]

Nội dung sách:
{$description}";
    }
}
```

#### parseSceneAnalysisResponse()
Parse và validate JSON response:

```php
private function parseSceneAnalysisResponse($response, $expectedCount)
{
    $scenes = json_decode($response, true);
    
    // Validate structure
    foreach ($scenes as $scene) {
        if (!isset($scene['scene_number'], $scene['title'], 
                   $scene['description'], $scene['visual_prompt'])) {
            throw new Exception('Invalid scene structure');
        }
    }
    
    return $scenes;
}
```

#### buildScenePromptFromAnalysis()
Chuyển đổi AI analysis thành image prompt:

```php
private function buildScenePromptFromAnalysis($sceneData, $audiobook)
{
    $style = "cinematic, high quality, detailed, 16:9 aspect ratio";
    
    return sprintf(
        "%s. Style: %s. Book: %s by %s",
        $sceneData['visual_prompt'],
        $style,
        $audiobook->title,
        $audiobook->author
    );
}
```

### Metadata Structure

File JSON lưu kèm mỗi scene:

```json
{
  "scene_number": 1,
  "title": "Buổi sáng ở làng quê",
  "description": "Cảnh mở đầu với một buổi sáng yên bình ở làng quê...",
  "visual_prompt": "A peaceful rural village at dawn, golden sunlight...",
  "book_id": 123,
  "book_title": "Tên sách",
  "generated_at": "2024-01-15T10:30:00Z",
  "model": "gemini-nano-banana-pro"
}
```

## 🎨 Frontend Integration

### Scene Gallery

File: `resources/views/audiobooks/show.blade.php`

#### Hiển thị Scene với Metadata

```javascript
function renderSceneGallery(scenes) {
    gallery.innerHTML = scenes.map((scene, idx) => `
        <div class="relative group">
            <!-- Scene Number Badge -->
            <div class="badge">Phân cảnh ${idx + 1}</div>
            
            <!-- Scene Image -->
            <img src="${scene.url}" alt="${scene.title || 'Scene'}">
            
            <!-- Info Overlay (shows on hover) -->
            ${scene.title || scene.description ? `
                <div class="info-overlay">
                    <h4>${scene.title}</h4>
                    <p>${scene.description}</p>
                </div>
            ` : ''}
        </div>
    `);
}
```

#### Progress Indicators

```javascript
'🤖 <strong>AI đang phân tích nội dung sách...</strong><br>' +
'📝 Xác định các điểm quan trọng trong nội dung<br>' +
'🎬 Tạo phân cảnh minh họa theo logic câu chuyện<br>' +
'🎨 Generating hình ảnh với Banana Pro model...'
```

## 📊 API Endpoints

### Generate Scenes

```http
POST /api/audiobooks/{id}/generate-scenes
Content-Type: application/json

{
  "style": "cinematic"
}
```

**Optional:** Thêm `"count": 5` nếu muốn chỉ định số lượng. Nếu không có, AI tự quyết định.

**Response:**
```json
{
  "success": true,
  "message": "Generated 5 scenes",
  "media": {
    "scenes": [
      {
        "filename": "scene_1_1234567890.png",
        "url": "https://domain.com/storage/books/123/scenes/scene_1_1234567890.png",
        "title": "Buổi sáng ở làng quê",
        "description": "Cảnh mở đầu...",
        "scene_number": 1
      }
    ]
  }
}
```

## 🎯 Best Practices

### 1. Viết Giới Thiệu Sách Tốt

Để AI phân tích chính xác, giới thiệu sách nên:

✅ **Rõ ràng, có cấu trúc:**
```
Mở đầu: Giới thiệu nhân vật chính
Phát triển: Các sự kiện quan trọng
Đỉnh điểm: Xung đột chính
Kết thúc: Thông điệp
```

✅ **Chi tiết cụ thể:**
- Mô tả môi trường, bối cảnh
- Giới thiệu nhân vật với đặc điểm rõ ràng
- Nêu các sự kiện theo trình tự thời gian

❌ **Tránh:**
- Giới thiệu quá ngắn, chung chung
- Chỉ liệt kê từ khóa
- Thiếu thông tin về bối cảnh

### 2. AI Tự Động Quyết Định Số Lượng

AI sẽ phân tích và quyết định số scenes phù hợp:

| Độ Dài Nội Dung | AI Gợi Ý Scenes |
|-----------|-------------------|
| Ngắn (đơn giản) | 3-5 scenes |
| Trung bình | 5-7 scenes |
| Dài (phức tạp) | 7-10 scenes |

**Lưu ý:** Bạn cũng có thể chỉ định số lượng muốn, AI sẽ cố gắng tạo đúng số đó.

### 3. Review và Tinh Chỉnh

Sau khi tạo scenes:
1. ✅ Kiểm tra tính logic của trình tự
2. ✅ Verify chất lượng hình ảnh
3. ✅ Đọc metadata để hiểu AI đã phân tích như thế nào
4. ✅ Tạo lại nếu kết quả không tốt

## 🔍 Troubleshooting

### Scene không logic

**Nguyên nhân:** Giới thiệu sách thiếu thông tin
**Giải pháp:** 
- Bổ sung chi tiết vào phần giới thiệu
- Tăng số scenes để AI phân tích chi tiết hơn

### Hình ảnh không đúng với nội dung

**Nguyên nhân:** Visual prompt không rõ ràng
**Giải pháo:**
- Kiểm tra metadata JSON
- Xem visual_prompt mà AI tạo ra
- Điều chỉnh buildScenePromptFromAnalysis() nếu cần

### Lỗi JSON parsing

**Nguyên nhân:** Gemini trả về format không chuẩn
**Giải pháp:**
- Check parseSceneAnalysisResponse() có validate đủ không
- Log raw response để debug
- Có thể cần retry request

## 📈 Performance Tips

### Tối Ưu Hóa

1. **Batch Processing:** Tạo nhiều scenes song song (cẩn thận với rate limit)
2. **Caching:** Cache metadata để không phải đọc file nhiều lần
3. **Lazy Loading:** Load scenes theo pagination ở frontend
4. **Image Optimization:** Compress images sau khi generate

### Monitoring

Track metrics:
- Scene generation time
- AI analysis accuracy
- Image generation success rate
- User satisfaction với scenes

## 🚀 Future Enhancements

### Planned Features

1. **Scene Editing:** Cho phép user edit title/description
2. **Scene Reordering:** Drag-and-drop để sắp xếp lại
3. **Custom Prompts:** Override AI prompts
4. **Scene Variations:** Tạo nhiều versions của 1 scene
5. **Animation Presets:** Áp dụng animation cho toàn bộ scenes
6. **Storyboard Export:** Export scenes thành PDF storyboard

## 📚 Related Documentation

- [FFmpeg Standard Guide](FFMPEG_STANDARD_GUIDE.md)
- [AI Segmentation Guide](AI_SEGMENTATION_GUIDE.md)
- [YouTube Media Guide](DUBSYNC_README.md)

## 💡 Examples

### Example 1: Fairy Tale

**Input (Book Description):**
```
Ngày xửa ngày xưa, có một cô gái tên Lọ Lem sống với mẹ kế và hai chị kế độc ác...
[Full fairy tale story]
```

**AI Analysis Output:**
```json
[
  {
    "scene_number": 1,
    "title": "Lọ Lem và công việc nhà",
    "description": "Cô gái trẻ làm việc vất vả trong căn bếp tối tăm",
    "visual_prompt": "A young girl in tattered clothes cleaning a dim kitchen..."
  },
  {
    "scene_number": 2,
    "title": "Bà tiên xuất hiện",
    "description": "Ma thuật biến bí ngô thành xe ngựa lộng lẫy",
    "visual_prompt": "Magical transformation of pumpkin into golden carriage..."
  }
]
```

### Example 2: Science Fiction

**Input:**
```
Năm 2157, nhân loại đã thuộc địa hóa Sao Hỏa. Kỹ sư Alex phát hiện dấu hiệu...
```

**Scenes Generated:**
- Scene 1: Mars colony dome city at sunset
- Scene 2: Underground ancient alien artifact
- Scene 3: Space chase sequence
- Scene 4: Final confrontation in zero gravity

---

**Last Updated:** 2024-01-15
**Version:** 1.0
**Author:** AI Development Team
