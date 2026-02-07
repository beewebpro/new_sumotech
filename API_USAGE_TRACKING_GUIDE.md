# API Usage Tracking System - Hướng Dẫn Sử Dụng

## 📊 Tổng Quan

Hệ thống ghi log tất cả API calls tự động vào bảng `api_usages`. Dữ liệu bao gồm:
- Loại API (OpenAI, Google, ElevenLabs, v.v.)
- Mục đích gọi (translate, generate TTS, fix segments, v.v.)
- Chi phí ước tính (dựa trên usage metrics)
- Metrics: tokens, characters, duration
- Status: success hay failed
- Error details nếu có

## 🎯 Các API Được Theo Dõi

### 1. **OpenAI** - Fix Segments
- **Service**: `SegmentFixService::fixSegments()`
- **Mục đích**: `fix_segments`
- **Metrics**: `tokens_used`
- **Chi phí**: ~$0.0005/1K tokens (gpt-3.5-turbo)

### 2. **OpenAI** - Translate
- **Service**: `TranslationService::translateWithOpenAI()`
- **Mục đích**: `translate_transcript`
- **Metrics**: `tokens_used`
- **Chi phí**: ~$0.0005/1K tokens

### 3. **Google Translate**
- **Service**: `TranslationService::translateWithGoogle()`
- **Mục đích**: `translate_transcript`
- **Metrics**: `characters_used`
- **Chi phí**: $15/1M characters

### 4. **Google Cloud TTS**
- **Service**: `TTSService::generateWithGoogleTTS()`
- **Mục đích**: `generate_audio`
- **Metrics**: `characters_used`
- **Chi phí**: $16/1M characters

### 5. **OpenAI TTS** (RapidAPI)
- **Service**: `TTSService::generateWithOpenAITTS()`
- **Mục đích**: `generate_audio`
- **Metrics**: `characters_used`
- **Chi phí**: $15/1M characters

### 6. **FFmpeg** - Audio Processing
- **Service**: `DubSyncController` (align, merge, v.v.)
- **Mục đích**: `align_audio`, `merge_audio`
- **Metrics**: `duration_seconds`
- **Chi phí**: $0 (local processing)

## 💻 Cách Sử Dụng ApiUsageService

### Log OpenAI Call
```php
use App\Services\ApiUsageService;

ApiUsageService::logOpenAI(
    purpose: 'translate_transcript',
    tokens: 1523,
    cost: null,  // tự tính nếu null
    model: 'gpt-3.5-turbo',
    projectId: 105,
    additionalData: ['source_lang' => 'en', 'target_lang' => 'vi']
);
```

### Log TTS Call
```php
ApiUsageService::logTTS(
    service: 'Google Cloud TTS',
    purpose: 'generate_audio',
    characters: 250,
    cost: null,  // tự tính nếu null
    projectId: 105,
    additionalData: ['voice' => 'vi-VN-Standard-A']
);
```

### Log Google Translate
```php
ApiUsageService::logGoogleTranslate(
    characters: 500,
    cost: null,
    projectId: 105,
    additionalData: ['source' => 'en', 'target' => 'vi']
);
```

### Log Failure
```php
ApiUsageService::logFailure(
    apiType: 'OpenAI',
    purpose: 'translate_transcript',
    error: 'Rate limit exceeded',
    projectId: 105
);
```

### Log FFmpeg Processing
```php
ApiUsageService::logFFmpeg(
    purpose: 'align_audio',
    durationSeconds: 125.5,
    projectId: 105,
    additionalData: ['tempo_ratio' => 1.3674]
);
```

## 📍 Vị Trí Ghi Log Hiện Tại

### ✅ Đã Thêm:
1. **SegmentFixService** - OpenAI fix segments
2. **TranslationService** - OpenAI & Google Translate
3. **TTSService** - Google Cloud TTS & OpenAI TTS
4. **DubSyncController** - Import ApiUsageService (sẵn sàng dùng)

### ⏳ Cần Thêm:
1. **DubSyncController::alignFullTranscriptDuration()** - FFmpeg operations
2. **DubSyncController::mergeFullTranscriptAudio()** - FFmpeg operations
3. **DubSyncController::downloadYoutubeVideo()** - yt-dlp (free)
4. Các YouTube API calls (free tier)
5. Gemini TTS nếu có sử dụng

## 📊 Xem Dữ Liệu

### Dashboard
```
URL: /api-usage
- Danh sách tất cả API calls
- Filters: api_type, purpose, status, date_range
- Summary: Total cost, calls, success rate
```

### Thống kê Chi Tiết
```
URL: /api-usage/statistics
- Biểu đồ chi phí theo ngày
- Phân bổ chi phí theo API type
- Top projects có chi phí cao nhất
- Chi phí trung bình mỗi loại API
```

### Chi Tiết 1 Call
```
URL: /api-usage/{id}
- Tất cả thông tin của 1 API call
- Request & response data (JSON)
- Error message nếu failed
```

## 🔧 Cost Calculations

### OpenAI
```php
// gpt-3.5-turbo: $0.0005/1K tokens
// gpt-4: $0.03/1K tokens
$cost = ($tokens / 1000) * $rate;
```

### Google Translate
```php
// $15/1M characters
$cost = ($characters / 1000000) * 15;
```

### Google TTS
```php
// $16/1M characters
$cost = $characters * 0.000016;
```

### ElevenLabs
```php
// $0.30/1K characters
$cost = ($characters / 1000) * 0.30;
```

## 🚀 Thêm Logging Vào Mã Mới

### Template đơn giản:
```php
try {
    // API call
    $response = $this->callAPI(...);
    
    // Log success
    ApiUsageService::log([
        'api_type' => 'MyAPI',
        'purpose' => 'do_something',
        'status' => 'success',
        'estimated_cost' => 0.05,
        'project_id' => $projectId
    ]);
    
} catch (Exception $e) {
    // Log failure
    ApiUsageService::logFailure(
        'MyAPI',
        'do_something',
        $e->getMessage(),
        $projectId
    );
    throw $e;
}
```

## 📈 Query Examples

### Tổng chi phí hôm nay
```php
use App\Models\ApiUsage;

ApiUsage::whereDate('created_at', today())
    ->sum('estimated_cost');
```

### Chi phí OpenAI tuần này
```php
ApiUsage::where('api_type', 'OpenAI')
    ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
    ->sum('estimated_cost');
```

### Projects có chi phí cao nhất
```php
ApiUsage::whereNotNull('project_id')
    ->selectRaw('project_id, SUM(estimated_cost) as total')
    ->groupBy('project_id')
    ->orderByDesc('total')
    ->limit(10)
    ->get();
```

### Tỷ lệ thành công
```php
$total = ApiUsage::count();
$success = ApiUsage::where('status', 'success')->count();
$successRate = ($success / $total) * 100;
```

## 🔍 Debugging

### Xem logs trong terminal
```bash
tail -f storage/logs/laravel.log
```

### Kiểm tra dữ liệu trong database
```bash
php artisan tinker

# Xem API calls hôm nay
ApiUsage::whereDate('created_at', today())->get();

# Xem API calls thất bại
ApiUsage::where('status', 'failed')->get();

# Tổng chi phí
ApiUsage::sum('estimated_cost');
```

## 📝 Lưu Ý

1. **Cost Calculation**: Các cost được tính ước lượng dựa trên từ API providers. Số tiền chính xác tùy thuộc vào tier của bạn.

2. **Token Usage**: OpenAI sẽ trả về `usage.total_tokens` chính xác. Nếu không có, hệ thống ước lượng dựa trên text length.

3. **Character Count**: Được tính từ độ dài text gốc (trước khi gọi API).

4. **Project Attribution**: Chỉ các API calls trong workflow DubSync mới có `project_id`. Calls khác sẽ có `project_id` = null.

5. **Performance**: Có indexes trên các columns hay query: `api_type`, `purpose`, `status`, `project_id`, `created_at`.

---

**Created**: Feb 3, 2026
**Status**: ✅ Production Ready
