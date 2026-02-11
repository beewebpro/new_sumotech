<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $type === 'audio' ? 'Audio' : 'Video' }} hoàn thành</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f7; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">
                                {{ $type === 'audio' ? '🎙️ Audio' : '🎬 Video' }} Hoàn Thành!
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                Xin chào,
                            </p>

                            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                Hệ thống đã xử lý xong <strong>{{ $type === 'audio' ? 'audio (giọng đọc)' : 'video' }}</strong>
                                cho audiobook:
                            </p>

                            <!-- Book Info Card -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h2 style="color: #1a1a2e; margin: 0 0 10px; font-size: 20px;">
                                            📚 {{ $audioBook->title }}
                                        </h2>
                                        @if($audioBook->author)
                                            <p style="color: #666; margin: 0 0 5px; font-size: 14px;">
                                                ✍️ Tác giả: {{ $audioBook->author }}
                                            </p>
                                        @endif
                                        @if(!empty($stats['channel_name']))
                                            <p style="color: #666; margin: 0 0 5px; font-size: 14px;">
                                                📺 Kênh: {{ $stats['channel_name'] }}
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Stats -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td width="50%" style="padding: 10px;">
                                        <table width="100%" style="background-color: #e8f5e9; border-radius: 8px;">
                                            <tr>
                                                <td style="padding: 15px; text-align: center;">
                                                    <div style="font-size: 28px; font-weight: bold; color: #2e7d32;">
                                                        {{ $stats['total_chapters'] ?? 0 }}
                                                    </div>
                                                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                                                        Chương
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%" style="padding: 10px;">
                                        <table width="100%" style="background-color: #e3f2fd; border-radius: 8px;">
                                            <tr>
                                                <td style="padding: 15px; text-align: center;">
                                                    <div style="font-size: 28px; font-weight: bold; color: #1565c0;">
                                                        @php
                                                            $totalSeconds = $stats['total_duration'] ?? 0;
                                                            $hours = floor($totalSeconds / 3600);
                                                            $minutes = floor(($totalSeconds % 3600) / 60);
                                                        @endphp
                                                        @if($hours > 0)
                                                            {{ $hours }}h{{ $minutes }}m
                                                        @else
                                                            {{ $minutes }}m
                                                        @endif
                                                    </div>
                                                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                                                        Tổng thời lượng
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #333; font-size: 14px; line-height: 1.6; margin: 0 0 20px;">
                                @if($type === 'audio')
                                    Tất cả các chương đã được tạo giọng đọc thành công.
                                    Bạn có thể kiểm tra và tiến hành tạo video hoặc upload lên YouTube.
                                @else
                                    Tất cả các chương đã được chuyển thành video thành công.
                                    Bạn có thể kiểm tra và upload lên YouTube.
                                @endif
                            </p>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <a href="{{ url('/audiobooks/' . $audioBook->id) }}"
                                           style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px;">
                                            Xem Audiobook
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="color: #999; font-size: 12px; margin: 0;">
                                Email tự động từ SumoTech Audiobook System<br>
                                {{ now()->format('d/m/Y H:i') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
