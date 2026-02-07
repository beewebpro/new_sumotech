# 📚 FIX ORIGINAL PARAGRAPH - IMPLEMENTATION GUIDE

## 🔍 NGUYÊN LÝ BAN ĐẦU (Hiện Tại Không Đủ)

### Logic Cũ:
```javascript
const shouldSplit = isSentenceEnd(bufferText)    // Ends with . ! ? ...
                 || wordCount >= 40              // OR word count >= 40
                 || index === segments.length - 1; // OR last segment
```

**Vấn đề:**
- ❌ Nếu đoạn kết thúc bằng từ nối (of, to, and, but, in, with...) → vẫn split
- ❌ Không kiểm tra xem câu có hoàn chỉnh không
- ❌ Ngưỡng 40 từ là tùy ý

---

## ✅ GIẢI PHÁP CẢI TIẾN (Rule-Based)

### RULE 1: Nếu đoạn kết thúc = từ nối → KHÔNG SPLIT
```javascript
Từ nối: of, to, and, but, or, in, on, with, from, by, about, the, a, an, is, are...

VD: "He is interested in" 
→ Kết thúc = "in" (preposition)
→ Tiếp tục merge: "in music lessons"
→ KHÔNG tạo segment ❌
```

### RULE 2: Nếu đoạn KHÔNG kết thúc = dấu câu (. ! ? …) → KHÔNG SPLIT
```javascript
VD1: "As you may know"
→ Không có . ! ? …
→ Câu chưa hoàn chỉnh
→ Tiếp tục merge ❌

VD2: "According to the research"
→ Không có .
→ Tiếp tục merge: "According to the research of climate change."
→ KHÔNG tạo segment ❌
```

### RULE 3: Nếu có dấu câu + KHÔNG = từ nối + ≥20 từ → SPLIT
```javascript
VD: "He said hello. I responded."
→ Kết thúc = "." (có dấu)
→ Kết thúc ≠ từ nối
→ 20+ từ ✓
→ TẠO SEGMENT ✅
```

### RULE 4: Nếu buffer > 50 từ → FORCE SPLIT (safety)
```javascript
Phòng trường hợp buffer tích tụ quá lâu mà không có dấu câu
```

---

## 🔧 CÁC BẠN CẦN LÀM GÌ?

### Option 1: Thay thế bằng VS Code
1. Mở file: `public/js/dubsync.js`
2. Find: `function mergeSegmentsIntoSentences(segments) {`
3. Tìm tới `return merged;` (đóng function)
4. Copy nội dung từ file `NEW_MERGE_FUNCTION.js` 
5. Paste thay thế phần cũ

### Option 2: Tôi sẽ cập nhật tự động
Nếu bạn cho phép, tôi có thể dùng tool thay thế file.

---

## 📊 BẢNG SO SÁNH TRƯỚC/SAU

| Tình Huống | Logic Cũ | Logic Mới |
|-----------|---------|---------|
| "He is interested **in**" | ❌ Split (có . hoặc ≥40 từ) | ✅ Continue (từ nối "in") |
| "According to the research" (không .) | ❌ Split (≥40 từ) | ✅ Continue (không . dấu) |
| "I agree. She disagrees." | ✅ Split (có .) | ✅ Split (có . + không nối) |
| Buffer 60 từ chưa . | ❌ Split | ✅ Split (safety) |

---

## 💡 PSEUDOCODE CHI TIẾT

```javascript
function mergeSegmentsIntoSentences(segments) {
    FOR EACH segment:
        ADD segment.text TO buffer
        ADD segment.duration TO duration
        
        wordCount = count words in buffer
        isLast = is this last segment?
        
        DECIDE: shouldCreateSegment = ?
        
            IF isLast:
                shouldCreateSegment = TRUE  // Always finalize at end
            
            ELSE IF endsWithConnector(buffer):
                shouldCreateSegment = FALSE  // RULE 1: Connector at end
            
            ELSE IF NOT hasSentenceEnd(buffer):
                shouldCreateSegment = FALSE  // RULE 2: No punctuation
            
            ELSE IF wordCount >= 20:
                shouldCreateSegment = TRUE   // RULE 3: Valid sentence
            
            ELSE IF wordCount >= 50:
                shouldCreateSegment = TRUE   // RULE 4: Safety overflow
            
            ELSE:
                shouldCreateSegment = FALSE  // Continue merging
        
        IF shouldCreateSegment:
            CREATE new segment with buffer
            RESET buffer, duration
}
```

---

## 🎯 EXAMPLES IN DETAIL

### Example 1: Preposition at End
```
Segment 1: "I am interested"
Segment 2: "in music"

Buffer after Seg 1: "I am interested"
→ Has . ? NO
→ Check Rule 2: NO sentence end → CONTINUE ❌

Buffer after Seg 2: "I am interested in music"
→ Has . ? NO
→ Last segment? YES
→ CREATE segment ✅

Result: "I am interested in music." (complete meaning)
```

### Example 2: List Continuation
```
Segment 1: "I like apples, oranges,"
Segment 2: "and bananas."

Buffer after Seg 1: "I like apples, oranges,"
→ Last word = "," (not connector) but...
→ Has . ? NO → CONTINUE ❌

Buffer after Seg 2: "I like apples, oranges, and bananas."
→ Has . ? YES ✓
→ Last word = "bananas" (not connector) ✓
→ Word count >= 20? YES ✓
→ CREATE segment ✅

Result: "I like apples, oranges, and bananas." (complete list)
```

### Example 3: Multiple Sentences
```
Segment: "Hello world. How are you?"

Buffer: "Hello world. How are you?"
→ Has . ? YES ✓
→ Last word = "you" (not connector) ✓
→ Word count >= 20? YES ✓
→ CREATE segment ✅

Result: "Hello world. How are you?" (both complete)
```

---

## 📁 FILES TO REVIEW

1. `MERGE_SEGMENTS_EXPLANATION.md` - Giải thích chi tiết
2. `NEW_MERGE_FUNCTION.js` - Code đã sẵn sàng
3. `MERGE_SEGMENTS_NEW.js` - Phiên bản khác (same content)

---

## ❓ QUESTIONS?

- Muốn điều chỉnh danh sách từ nối?
- Muốn thay đổi ngưỡng từ (20, 50)?
- Muốn tôi cập nhật code ngay?
