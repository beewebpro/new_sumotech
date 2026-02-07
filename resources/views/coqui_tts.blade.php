@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">Edge TTS (Tiếng Việt)</h2>
                <a href="{{ route('projects.index') }}"
                    class="bg-black hover:bg-gray-800 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Back to Projects
                </a>
            </div>
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                    <div class="text-sm text-gray-600">
                        ✨ Edge TTS - Miễn phí không giới hạn, giọng đọc tự nhiên. Lần đầu có thể hơi lâu.
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chọn giọng đọc</label>
                        <select id="coquiModel" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="vi-VN-HoaiMyNeural">🇻🇳 Hoài My (Nữ - Miền Bắc)</option>
                            <option value="vi-VN-NamMinhNeural">🇻🇳 Nam Minh (Nam - Miền Nam)</option>
                            <option value="en-US-AriaNeural">🇺🇸 Aria (Female - US)</option>
                            <option value="en-US-GuyNeural">🇺🇸 Guy (Male - US)</option>
                            <option value="en-GB-SoniaNeural">🇬🇧 Sonia (Female - UK)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Văn bản</label>
                        <textarea id="coquiText" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            placeholder="Nhập văn bản cần chuyển thành giọng nói...">Xin chào! Đây là bản demo Edge TTS chạy local với giọng đọc tiếng Việt tự nhiên.</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <button id="coquiGenerateBtn"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            🎙️ Generate TTS
                        </button>
                        <span id="coquiStatus" class="text-sm text-gray-500"></span>
                    </div>

                    <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kết quả</label>
                        <audio id="coquiAudio" controls class="w-full hidden"></audio>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const btn = document.getElementById('coquiGenerateBtn');
                    const textEl = document.getElementById('coquiText');
                    const modelEl = document.getElementById('coquiModel');
                    const statusEl = document.getElementById('coquiStatus');
                    const audioEl = document.getElementById('coquiAudio');

                    btn.addEventListener('click', async function() {
                        const text = textEl.value.trim();
                        const voice = modelEl.value.trim();

                        if (!text) {
                            alert('Vui lòng nhập văn bản');
                            return;
                        }

                        btn.disabled = true;
                        const originalText = btn.innerHTML;
                        btn.innerHTML = '⏳ Generating...';
                        statusEl.textContent = 'Đang tạo audio, vui lòng chờ...';

                        try {
                            const response = await fetch('{{ route('coqui.tts.generate') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content
                                },
                                body: JSON.stringify({
                                    text,
                                    voice
                                })
                            });

                            const data = await response.json();
                            if (!response.ok || !data.success) {
                                throw new Error(data.error || 'Không thể tạo TTS');
                            }

                            audioEl.src = data.audio_url;
                            audioEl.classList.remove('hidden');
                            audioEl.load();
                            statusEl.textContent = '✅ Hoàn tất';
                        } catch (error) {
                            console.error('Coqui TTS error:', error);
                            statusEl.textContent = '';
                            alert('❌ Lỗi: ' + error.message);
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    });
                });
            </script>
        </div>
    @endsection
