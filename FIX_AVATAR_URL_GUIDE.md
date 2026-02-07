# Hướng dẫn Fix Avatar URL cho Lip-sync Video

## Vấn đề

D-ID API yêu cầu avatar URL phải:
1. ✅ Sử dụng HTTPS (không phải HTTP)
2. ✅ Là URL công khai có thể truy cập từ internet
3. ✅ Kết thúc bằng phần mở rộng `.jpg`, `.jpeg`, hoặc `.png`

**Lỗi hiện tại**: Avatar URL của bạn là `http://sumotech.test/storage/speakers/...` - đây là URL local không thể truy cập từ internet.

## Giải pháp

### Option 1: Sử dụng Ngrok (Nhanh nhất cho development)

#### Bước 1: Cài đặt Ngrok
```bash
# Download từ https://ngrok.com/download
# Hoặc dùng Chocolatey (Windows)
choco install ngrok
```

#### Bước 2: Đăng ký tài khoản Ngrok (miễn phí)
- Truy cập: https://dashboard.ngrok.com/signup
- Lấy authtoken từ dashboard

#### Bước 3: Cấu hình Ngrok
```bash
ngrok config add-authtoken YOUR_AUTH_TOKEN
```

#### Bước 4: Chạy Ngrok tunnel
```bash
# Mở terminal mới và chạy
ngrok http 80

# Hoặc nếu dùng port khác (ví dụ: 8000)
ngrok http 8000
```

Ngrok sẽ tạo URL như: `https://abc123.ngrok-free.app`

#### Bước 5: Cập nhật APP_URL trong `.env`
```env
# Thay đổi từ
APP_URL=http://sumotech.test

# Thành (sử dụng URL từ ngrok)
APP_URL=https://abc123.ngrok-free.app
```

#### Bước 6: Clear cache
```bash
php artisan config:clear
php artisan cache:clear
```

#### Bước 7: Upload lại avatar cho MC
1. Vào trang quản lý Speakers/MC
2. Chọn MC cần cập nhật
3. Upload lại avatar image
4. URL mới sẽ có dạng: `https://abc123.ngrok-free.app/storage/speakers/2/...jpg`

---

### Option 2: Upload lên Cloud Storage (Khuyến nghị cho production)

#### A. Sử dụng Cloudinary (Dễ nhất)

1. **Đăng ký tài khoản miễn phí**: https://cloudinary.com/users/register/free

2. **Cài đặt Cloudinary SDK**:
```bash
composer require cloudinary/cloudinary_php
```

3. **Cấu hình trong `.env`**:
```env
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
```

4. **Upload avatar**:
- Vào Cloudinary Dashboard → Media Library
- Upload ảnh avatar
- Copy Public URL (dạng: `https://res.cloudinary.com/your-cloud/image/upload/v123/avatar.jpg`)

5. **Cập nhật avatar URL trong database**:
```php
// Trong tinker hoặc script
php artisan tinker

$speaker = App\Models\ChannelSpeaker::find(2);
$speaker->avatar_url = 'https://res.cloudinary.com/your-cloud/image/upload/v123/avatar.jpg';
$speaker->save();
```

#### B. Sử dụng AWS S3

1. **Cài đặt AWS SDK**:
```bash
composer require league/flysystem-aws-s3-v3
```

2. **Cấu hình trong `config/filesystems.php`**:
```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
],
```

3. **Upload và lấy public URL**

---

### Option 3: Sử dụng Public Image URL

Nếu chỉ test nhanh:

1. Upload ảnh lên dịch vụ miễn phí:
   - **Imgur**: https://imgur.com/upload
   - **ImgBB**: https://imgbb.com/
   - **Postimages**: https://postimages.org/

2. Copy direct link (phải kết thúc bằng `.jpg`, `.jpeg`, hoặc `.png`)

3. Cập nhật trong database:
```bash
php artisan tinker

$speaker = App\Models\ChannelSpeaker::find(2);
$speaker->avatar_url = 'https://i.imgur.com/ABC123.jpg';
$speaker->save();
```

---

## Kiểm tra sau khi fix

1. **Verify avatar URL**:
```bash
# Kiểm tra trong browser hoặc curl
curl -I https://your-avatar-url.jpg
# Phải trả về 200 OK
```

2. **Test tạo video**:
   - Vào trang audiobook
   - Tick checkbox "Tạo video Lip-sync cho giới thiệu"
   - Click "🎙️ Tạo Audio"
   - Chờ xử lý
   - Video player sẽ xuất hiện bên dưới

3. **Kiểm tra logs nếu vẫn lỗi**:
```bash
tail -f storage/logs/laravel.log
```

---

## Troubleshooting

### Lỗi: "Avatar URL must be a publicly accessible HTTPS URL"
- ✅ Đảm bảo URL bắt đầu bằng `https://` (không phải `http://`)
- ✅ URL không chứa `localhost`, `.test`, `.local`, hoặc IP nội bộ

### Lỗi: "must be a valid image URL (ending with jpg|jpeg|png)"
- ✅ URL phải kết thúc bằng `.jpg`, `.jpeg`, hoặc `.png`
- ✅ Không dùng URL rút gọn hoặc redirect

### Ngrok tunnel bị disconnect
- Tunnel miễn phí của Ngrok sẽ ngừng sau 2 giờ
- Chạy lại `ngrok http 80` để tạo tunnel mới
- Cập nhật lại `APP_URL` với URL mới

### Avatar hiển thị bị vỡ sau khi đổi URL
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan storage:link
```

---

## Khuyến nghị

- **Development**: Dùng Ngrok (nhanh, dễ setup)
- **Production**: Dùng Cloudinary hoặc S3 (ổn định, bảo mật)
- **Testing nhanh**: Dùng Imgur (không cần setup)

---

## Code Changes đã thực hiện

### 1. AudioBookController.php
- ✅ Thêm validation `isPublicImageUrl()` kiểm tra avatar URL
- ✅ Throw exception rõ ràng khi URL không hợp lệ
- ✅ Return error message trong response JSON khi video generation fail

### 2. show.blade.php
- ✅ Hiển thị error message màu cam khi có `video_error`
- ✅ User sẽ thấy thông báo cụ thể về lỗi avatar URL

### 3. Error message mẫu
```
⚠️ Audio đã được tạo thành công, nhưng video lip-sync thất bại: 
Avatar URL must be a publicly accessible HTTPS URL. 
Current URL: http://sumotech.test/storage/speakers/2/avatar.jpg. 
Please upload the avatar to a public hosting service (S3, Cloudinary, etc.) 
or use ngrok to expose your local server.
```
