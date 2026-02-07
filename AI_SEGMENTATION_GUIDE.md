# AI-Powered Transcript Segmentation

## Tổng Quan

Hệ thống DubSync hiện nay sử dụng **OpenAI GPT-3.5-turbo** để tự động chia nhỏ transcript thành các đoạn có nghĩa đầy đủ (semantic segments) thay vì chỉ dựa vào word count hay duration.

## Cách Hoạt Động

### 1. Quy trình Xử Lý (Processing Flow)

```
YouTube Video
    ↓
1. Lấy Metadata (Title, Description, Duration, Thumbnail)
    ↓
2. Extract Transcript (với timestamps)
    ↓
3. Clean Transcript (loại bỏ ký tự đặc biệt, whitespace)
    ↓
4. AI Segmentation (Sử dụng GPT để chia segment có nghĩa)
    ↓
5. Reconstruct Timing (Gán timing chính xác từ original transcript)
    ↓
Segments với Meaningful Content + Chính Xác Timing
```

### 2. AI Segmentation Details

**Service:** `AISegmentationService.php`

**Input:**
- Array các entry transcript đã clean
- Mỗi entry có: text, start time, duration

**Output:**
- Array segments với:
  - `text`: Câu/đoạn hoàn chỉnh (không bị cut ngang)
  - `start_time`: Thời gian bắt đầu (từ original transcript)
  - `end_time`: Thời gian kết thúc
  - `duration`: Độ dài segment
  - `entries`: Các entry gốc tạo thành segment này

### 3. Prompts Cho OpenAI

**System Prompt:**
```
"You are an expert at segmenting transcripts into meaningful, complete thoughts. 
Each segment should be a complete idea or sentence, not a fragment."
```

**User Prompt:**
- Transcript text với timing markers `[0.0s]`, `[2.5s]`, etc.
- Yêu cầu chia thành segments hoàn chỉnh
- Mỗi segment nên 2-4 câu (15-50 từ)
- Không được cắt ngang câu hoặc mệnh đề

**Constraint:**
- Temperature: 0.3 (ít sáng tạo, tập trung vào accuracy)
- Max tokens: 2000

### 4. Lưu ý Về Timing

**Cách preserve timing:**
1. AI chia text dựa trên semantic meaning
2. Service tìm segment text trong full transcript
3. Map lại đến original entries để lấy timing chính xác
4. Kết quả: Segments có nội dung đầy đủ + timing chính xác từ source

### 5. Fallback Mechanism

Nếu AI segmentation thất bại (API error, timeout, etc.):
```
AI Segmentation Error 
    ↓
Fall back to Traditional Segmentation
    ↓
Use TranscriptSegmentationService (word count + duration based)
```

Người dùng sẽ vẫn có segments, chỉ là không tối ưu về semantic.

## Thay Đổi Trong Code

### Files Modified:

1. **`app/Services/AISegmentationService.php`** (NEW)
   - Xử lý AI segmentation logic
   - Call OpenAI API
   - Reconstruct timing từ original entries

2. **`app/Http/Controllers/DubSyncController.php`**
   - Line ~126: Đổi từ `TranscriptSegmentationService` sang `AISegmentationService`
   - Thêm comment về AI segmentation

3. **`resources/views/projects/create.blade.php`**
   - Step 1 label: "Extract Transcript & AI Segmentation"
   - Thêm subtitle: "Using GPT to create meaningful segments..."

4. **`public/js/dubsync.js`**
   - Timeout tăng từ 5 → 10 phút (300s → 600s)
   - Timeout message cập nhật để nhắc đến AI processing

## Performance Impact

- **Thêm time:** ~2-5 giây cho AI API call (phụ thuộc vào transcript length)
- **Total time per video:** 
  - Short (< 5 min): 8-15 giây
  - Medium (5-15 min): 15-30 giây
  - Long (> 15 min): 30-60 giây

## Testing Recommendations

1. Test với video ngắn (< 5 phút) trước
2. Kiểm tra segments có:
   ✓ Complete sentences (không bị cut ngang)
   ✓ Meaningful grouping
   ✓ Timing chính xác

3. Test fallback: Tạm disable OPENAI_API_KEY để test traditional segmentation

## Configuration

Không cần setup thêm - service sử dụng:
- `OPENAI_API_KEY` (đã có)
- Model: gpt-3.5-turbo (cost-effective)
- Temperature: 0.3 (fixed)

## Future Improvements

1. Caching: Lưu AI segmentation result để tái sử dụng
2. Custom prompt: Cho phép user customize segmentation rules
3. Model upgrade: Thử gpt-4 cho accuracy cao hơn
4. Batch processing: Xử lý nhiều videos cùng lúc
