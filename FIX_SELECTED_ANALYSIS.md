# Phân Tích Nút "Fix Selected (AI)"

## 📋 Tổng Quan
Nút "Fix Selected (AI)" cho phép sửa các đoạn transcript đã chọn bằng OpenAI API (ChatGPT).

---

## 🎯 Flow Hoạt Động

### 1️⃣ **Frontend - JavaScript** 
**File:** [public/js/dubsync.js](public/js/dubsync.js#L63-L230)

#### Khởi tạo:
```javascript
function initBulkFixSelectedSegments() {
    const fixBtn = document.getElementById('fixSelectedSegmentsBtn');
    fixBtn.addEventListener('click', async (e) => { ... });
}
```

#### Quy trình khi click nút:
1. **Kiểm tra Project ID** - Nếu không có → báo lỗi
2. **Thu thập segments được chọn**:
   - Lấy tất cả checkbox `.segment-select` đã check
   - Chuyển đổi thành array object gồm `index` và `text`
3. **Xác nhận với user** - "Hệ thống sẽ gửi {N} đoạn để AI sửa"
4. **Gửi POST request** đến `/dubsync/projects/{projectId}/fix-segments`
5. **Cập nhật DOM** - Thay thế text trong textarea và `currentSegments` object

#### Key Variables:
- `currentProjectId` - ID của project hiện tại
- `currentSegments` - Array chứa tất cả segments
- `.segment-select` - Checkbox để chọn segment
- `.segment-text` - Textarea chứa nội dung segment

---

### 2️⃣ **Backend - Controller**
**File:** [app/Http/Controllers/DubSyncController.php](app/Http/Controllers/DubSyncController.php#L141-L171)

#### Method: `fixSelectedSegments()`
```php
public function fixSelectedSegments(Request $request, $projectId)
{
    // 1. Validate request
    $request->validate([
        'segments' => 'required|array',
        'segments.*.index' => 'required|integer',
        'segments.*.text' => 'required|string'
    ]);

    // 2. Lưu input/output vào file JSON
    $timestamp = now()->format('Ymd_His');
    $inputPath = "dubsync/segment-fix/{$projectId}_input_{$timestamp}.json";
    Storage::disk('local')->put($inputPath, json_encode($segments));

    // 3. Gọi Service để xử lý
    $fixService = new SegmentFixService();
    $fixedSegments = $fixService->fixSegments($segments);

    // 4. Lưu output
    $outputPath = "dubsync/segment-fix/{$projectId}_output_{$timestamp}.json";
    Storage::disk('local')->put($outputPath, json_encode($fixedSegments));

    // 5. Return result
    return response()->json([
        'success' => true,
        'fixed_segments' => $fixedSegments
    ]);
}
```

---

### 3️⃣ **Service - AI Processing**
**File:** [app/Services/SegmentFixService.php](app/Services/SegmentFixService.php)

#### API Sử Dụng: **OpenAI GPT-3.5-Turbo**

#### Chi tiết Request:
```php
Http::post('https://api.openai.com/v1/chat/completions', [
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        [
            'role' => 'system',
            'content' => 'You are a transcript cleaner. Only fix broken sentence joins and light punctuation. Do NOT add, remove, or invent content. Keep the original language. Remove stage directions like [applause], [music], (laughter), (cheering). Return JSON only.'
        ],
        [
            'role' => 'user',
            'content' => "Fix the following transcript segments. Return a JSON array of objects with keys: index, text. Keep the same indices. Do not add extra keys. Input JSON:\n\n" . json_encode($cleanedSegments)
        ]
    ],
    'temperature' => 0,           // Deterministic output
    'max_tokens' => 2000          // Max length response
])
```

#### Tiền xử lý:
- **Xóa stage directions** - [applause], [music], (laughter), (cheering)
- **Làm sạch text** - Xóa khoảng trắng thừa

#### Xử lý Response:
1. Parse JSON từ response
2. Xóa stage directions lần nữa
3. Return array with `index` và `text` đã fix

#### Fallback:
Nếu API fail → trả về segments đã được làm sạch nhưng không fix

---

## 🔗 API Endpoint

### Route
```
POST /dubsync/projects/{projectId}/fix-segments
```

### Headers
```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
```

### Request Body
```json
{
    "segments": [
        {
            "index": 0,
            "text": "Dòng text segment 1"
        },
        {
            "index": 1,
            "text": "Dòng text segment 2"
        }
    ]
}
```

### Response
```json
{
    "success": true,
    "fixed_segments": [
        {
            "index": 0,
            "text": "Dòng text segment 1 (đã fix)"
        },
        {
            "index": 1,
            "text": "Dòng text segment 2 (đã fix)"
        }
    ]
}
```

---

## ⚙️ Cấu Hình

### Yêu cầu:
- **OPENAI_API_KEY** trong `.env` - API key của OpenAI
- **Model:** gpt-3.5-turbo
- **Temperature:** 0 (deterministc)
- **Max tokens:** 2000

### Lưu trữ:
- Input/Output được lưu vào `storage/app/dubsync/segment-fix/`
- Naming: `{projectId}_input_{timestamp}.json` và `{projectId}_output_{timestamp}.json`

---

## 📊 Tính Năng Chi Tiết

| Tính Năng | Chi Tiết |
|-----------|---------|
| **Select All** | Checkbox "Chọn tất cả" → tích/bỏ tích tất cả segments |
| **Validation** | Yêu cầu chọn ít nhất 1 segment |
| **Confirmation** | Xác nhận trước khi gửi AI |
| **Status Message** | Hiển thị "Đang xử lý..." trong floating bar |
| **DOM Update** | Tự động update textarea và data object |
| **Error Handling** | Try-catch với user alert và console log |
| **Button State** | Disable button trong khi xử lý, restore sau |

---

## 🐛 Debugging

### Console Logs:
```javascript
[initBulkFixSelectedSegments] Initializing
[fixBtn.click] Button clicked, projectId: {id}
[fixBtn.click] Selected indices: [...], Total checkboxes: N
[fixBtn.click] Segments to send: N items
[fixBtn.click] Sending POST to: /dubsync/projects/{id}/fix-segments
[fixBtn.click] Response status: 200 OK
[fixBtn.click] Response data: {...}
[fixBtn.click] Fixed segments count: N
[fixBtn.click] Updated textarea at index {i}
```

### Lỗi Có Thể Gặp:
1. **Không tìm thấy Project ID** → Reload page
2. **Không chọn segment** → Alert "Vui lòng chọn ít nhất 1 đoạn"
3. **OPENAI_API_KEY missing** → Backend log error, return fallback
4. **OpenAI API error** → Backend log error, return fallback
5. **Invalid JSON response** → Regex parse, fallback nếu fail

---

## 💡 Cách Cải Thiện

1. **Batch Processing** - Xử lý theo nhóm nếu quá 50 segments
2. **Progress Bar** - Hiển thị progress khi xử lý từng segment
3. **Timeout Handling** - Add retry logic nếu timeout
4. **Cost Control** - Hiển thị estimated cost trước khi process
5. **History** - Lưu lịch fix segments để rollback nếu cần
6. **Custom Prompt** - Cho phép user tùy chỉnh prompt cho AI

---

## 📝 Kết Luận

**Fix Selected (AI)** là một tính năng mạnh mẽ:
- ✅ Sử dụng OpenAI GPT-3.5-turbo
- ✅ Xóa stage directions & làm sạch text
- ✅ Fallback graceful nếu API fail
- ✅ Logging input/output để audit
- ✅ Real-time DOM update
- ✅ User-friendly confirmation

Hoạt động tốt cho việc làm sạch và sửa transcript từ các video YouTube.
