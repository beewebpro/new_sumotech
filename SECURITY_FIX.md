# Security Fix - API Keys Management

## Vấn đề bảo mật đã sửa

### ❌ Trước đây (KHÔNG AN TOÀN)
API keys được hardcode trực tiếp trong source code:
```python
RAPIDAPI_KEY = "aaad395d92msh12325bd75be1d39p1454f5jsndf32bb95e511"
```

**Rủi ro:**
- Lộ API key nếu push lên GitHub
- Git history lưu vĩnh viễn
- Kẻ xấu có thể lạm dụng API
- Khó quản lý khi thay đổi key

### ✅ Giải pháp (AN TOÀN)
Sử dụng **environment variables** qua file `.env`

## Các file đã sửa

### 1. `.env` - Credentials được lưu ở đây
```dotenv
RAPIDAPI_HOST=youtube-transcript3.p.rapidapi.com
RAPIDAPI_KEY=aaad395d92msh12325bd75be1d39p1454f5jsndf32bb95e511
```

**⚠️ Lưu ý:** File `.env` nằm trong `.gitignore` - sẽ NOT được push lên GitHub

### 2. `storage/scripts/get_youtube_transcript.py` - Đọc từ .env
```python
from pathlib import Path

def load_env():
    """Load environment variables from .env file"""
    env_file = Path(__file__).parent.parent.parent / '.env'
    if env_file.exists():
        with open(env_file, 'r') as f:
            for line in f:
                # Parse and load variables
                ...

load_env()
RAPIDAPI_KEY = os.getenv('RAPIDAPI_KEY')
```

### 3. `app/Services/YouTubeTranscriptService.php` - Verify credentials
```php
$rapidApiKey = env('RAPIDAPI_KEY');
if (!$rapidApiKey) {
    throw new Exception('RAPIDAPI_KEY not configured in .env');
}
```

### 4. `.env.example` - Mẫu cho developers
```dotenv
RAPIDAPI_HOST=youtube-transcript3.p.rapidapi.com
RAPIDAPI_KEY=your_rapidapi_key_here
```

## Hướng dẫn sử dụng

### Setup ban đầu
1. Copy `.env.example` → `.env`
   ```bash
   cp .env.example .env
   ```

2. Cập nhật giá trị API key trong `.env`:
   ```dotenv
   RAPIDAPI_KEY=your_actual_api_key_here
   ```

3. Commit `.env.example` vào Git (không commit `.env`)
   ```bash
   git add .env.example
   git commit -m "Add .env.example template"
   # .env được ignore tự động
   ```

### Tại sao cách này an toàn?

| Aspect | Trước | Sau |
|--------|-------|-----|
| Lưu trữ | Source code | Environment variable |
| Git tracking | Tracked (lộ) | Ignored (.gitignore) |
| Thay đổi key | Code + Deploy | Chỉ sửa .env |
| Developers | Cùng key | Key riêng |
| Production | Hardcode | Server env vars |

## Kiểm tra

Sau khi setup, kiểm tra xem credentials được load đúng:

```bash
# Test Python script
python storage/scripts/get_youtube_transcript.py ZacjOVVgoLY
```

Nếu thành công, sẽ in ra JSON transcript. Nếu lỗi "RAPIDAPI_KEY not set", kiểm tra `.env` file.

## Best Practices

1. ✅ **Luôn dùng environment variables cho secrets**
2. ✅ **Thêm `.env` vào `.gitignore`**
3. ✅ **Commit `.env.example` với placeholder values**
4. ✅ **Rotate API keys định kỳ**
5. ✅ **Không share API keys qua email/chat**

## Production Deployment

Trên production server, set environment variables qua:

**Option 1: `.env` file (tương tự local)**
```bash
# SSH vào server
nano /var/www/app/.env
# Thêm credentials
```

**Option 2: System environment (recommended)**
```bash
# Set system env vars
export RAPIDAPI_KEY="your_key_here"
export RAPIDAPI_HOST="youtube-transcript3.p.rapidapi.com"
```

**Option 3: Docker (recommended for containers)**
```dockerfile
ENV RAPIDAPI_KEY="your_key_here"
ENV RAPIDAPI_HOST="youtube-transcript3.p.rapidapi.com"
```

**Option 4: Heroku / Cloud Platforms**
```bash
heroku config:set RAPIDAPI_KEY="your_key_here"
```

## Tài liệu tham khảo

- Laravel env: https://laravel.com/docs/configuration#environment-configuration
- Environment variables: https://12factor.net/config
- RapidAPI: https://rapidapi.com/SerpApi/api/youtube-transcript3

---

**Ngày fix:** Jan 28, 2026
**Status:** ✅ Hoàn tất
