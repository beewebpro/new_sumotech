<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sumotech - AI Technology Hub</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo/sumo_favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }
    </style>
</head>

<body class="bg-white">
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <img src="{{ asset('img/logo/Sumotech_round.png') }}" alt="Sumotech" class="h-12 w-auto" />
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="#"
                            class="text-gray-900 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">Trang
                            chủ</a>
                        <a href="#tools"
                            class="text-gray-600 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">AI
                            Tools</a>
                        <a href="#content"
                            class="text-gray-600 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">Nội
                            dung</a>
                        <a href="#use-cases"
                            class="text-gray-600 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">Ứng
                            dụng</a>
                        <a href="{{ route('login') }}"
                            class="text-gray-600 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">Đăng
                            nhập</a>
                    </div>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-600 hover:text-primary p-2">
                        <div class="w-6 h-6 flex items-center justify-center">
                            <i class="ri-menu-line ri-lg"></i>
                        </div>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="#" class="text-gray-900 block px-3 py-2 text-base font-medium">Trang chủ</a>
                <a href="#tools" class="text-gray-600 hover:text-primary block px-3 py-2 text-base font-medium">AI
                    Tools</a>
                <a href="#content" class="text-gray-600 hover:text-primary block px-3 py-2 text-base font-medium">Nội
                    dung</a>
                <a href="#use-cases" class="text-gray-600 hover:text-primary block px-3 py-2 text-base font-medium">Ứng
                    dụng</a>
                <a href="{{ route('login') }}"
                    class="text-gray-600 hover:text-primary block px-3 py-2 text-base font-medium">Đăng nhập</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-16 min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-white via-white/95 to-transparent"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-gray-900 leading-tight mb-6">
                    <span
                        class="bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">SUMOTECH</span><br>
                    <span class="text-4xl md:text-5xl lg:text-6xl">AI Technology Hub</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-700 mb-4 font-semibold max-w-4xl mx-auto">
                    Khám phá thế giới công nghệ AI tiên tiến
                </p>
                <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-3xl mx-auto">
                    Nền tảng tập hợp các công cụ AI mới nhất, bài viết chuyên sâu, video hướng dẫn và ứng dụng thực tế
                    về Generative AI, Image & Video Generation, AI Vision, Automation, Data Analytics và nhiều hơn nữa.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#tools"
                        class="bg-primary text-white px-8 py-4 !rounded-button text-lg font-semibold hover:bg-primary/90 transition-colors whitespace-nowrap">
                        Khám phá AI Tools
                    </a>
                    <a href="#content"
                        class="border-2 border-primary text-primary px-8 py-4 !rounded-button text-lg font-semibold hover:bg-primary hover:text-white transition-colors whitespace-nowrap">
                        Xem nội dung
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- AI Tools Section -->
    <section id="tools" class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Công cụ AI của Sumotech
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Bộ công cụ AI toàn diện được xây dựng và phát triển bởi Sumotech
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Generative AI -->
                <div
                    class="bg-gradient-to-br from-purple-50 to-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="ri-quill-pen-line ri-2x text-white"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">Generative AI</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Tạo văn bản thông minh với GPT, viết nội dung marketing, code generation, chatbot AI và content
                        creation tự động.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Content Writer AI
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            AI Chatbot Builder
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Code Generator
                        </li>
                    </ul>
                </div>

                <!-- Image Generation -->
                <div
                    class="bg-gradient-to-br from-pink-50 to-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="ri-image-ai-line ri-2x text-white"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">Image Generation</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Tạo hình ảnh từ text prompts, chỉnh sửa ảnh AI, background removal, image upscaling và style
                        transfer.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Text-to-Image AI
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            AI Photo Editor
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Image Enhancement
                        </li>
                    </ul>
                </div>

                <!-- Video Generation -->
                <div
                    class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="ri-video-ai-line ri-2x text-white"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">Video Generation</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Tạo video từ text, AI video editing, deepfake technology, video summarization và automated video
                        production.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Text-to-Video AI
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            AI Video Editor
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Video Analytics
                        </li>
                    </ul>
                </div>

                <!-- AI Vision -->
                <div
                    class="bg-gradient-to-br from-green-50 to-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="ri-eye-ai-line ri-2x text-white"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">AI Vision</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Object detection, image recognition, facial recognition, OCR, medical imaging analysis và visual
                        search.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Object Detection
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            OCR & Document AI
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Visual Search
                        </li>
                    </ul>
                </div>

                <!-- Automation -->
                <div
                    class="bg-gradient-to-br from-orange-50 to-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="ri-robot-2-line ri-2x text-white"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">AI Automation</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Workflow automation, RPA, intelligent process automation, email automation và task scheduling
                        AI.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Workflow Builder
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            RPA Platform
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Smart Scheduling
                        </li>
                    </ul>
                </div>

                <!-- Data Analytics -->
                <div
                    class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="ri-bar-chart-ai-line ri-2x text-white"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">Data Analytics AI</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Predictive analytics, anomaly detection, business intelligence AI, data visualization và
                        automated insights.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Predictive Models
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            BI Dashboard AI
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-primary"></i>
                            Data Mining
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Hub Section -->
    <section id="content" class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Content Hub - Bài viết & Video
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Khám phá kiến thức chuyên sâu về AI qua bài viết và video hướng dẫn
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <!-- Articles -->
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <i class="ri-article-line ri-xl text-primary"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Bài viết mới nhất</h3>
                    </div>
                    <div class="space-y-6">
                        <div class="border-l-4 border-primary pl-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Hướng dẫn sử dụng GPT-4 cho doanh nghiệp</h4>
                            <p class="text-sm text-gray-600 mb-2">Tìm hiểu cách triển khai GPT-4 để tối ưu hóa quy
                                trình làm việc và tăng năng suất...</p>
                            <span class="text-xs text-primary font-medium">Đọc thêm →</span>
                        </div>
                        <div class="border-l-4 border-purple-500 pl-4">
                            <h4 class="font-semibold text-gray-900 mb-2">AI Image Generation: Từ Stable Diffusion đến
                                DALL-E 3</h4>
                            <p class="text-sm text-gray-600 mb-2">So sánh các công cụ tạo ảnh AI hàng đầu và cách sử
                                dụng hiệu quả...</p>
                            <span class="text-xs text-purple-500 font-medium">Đọc thêm →</span>
                        </div>
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Computer Vision trong thực tế: 10 ứng dụng
                                hàng đầu</h4>
                            <p class="text-sm text-gray-600 mb-2">Khám phá cách AI Vision đang thay đổi ngành công
                                nghiệp...</p>
                            <span class="text-xs text-blue-500 font-medium">Đọc thêm →</span>
                        </div>
                    </div>
                </div>

                <!-- Videos -->
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center">
                            <i class="ri-video-line ri-xl text-purple-500"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Video hướng dẫn</h3>
                    </div>
                    <div class="space-y-6">
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="ri-play-circle-line ri-lg text-purple-600"></i>
                                <h4 class="font-semibold text-gray-900">Xây dựng Chatbot AI từ A-Z</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Video series 5 phần hướng dẫn chi tiết</p>
                            <span class="text-xs text-purple-600 font-medium">⏱ 45 phút</span>
                        </div>
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="ri-play-circle-line ri-lg text-blue-600"></i>
                                <h4 class="font-semibold text-gray-900">Automation với Python & AI</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Tự động hóa công việc với AI</p>
                            <span class="text-xs text-blue-600 font-medium">⏱ 30 phút</span>
                        </div>
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="ri-play-circle-line ri-lg text-green-600"></i>
                                <h4 class="font-semibold text-gray-900">Data Analytics với Machine Learning</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Phân tích dữ liệu nâng cao</p>
                            <span class="text-xs text-green-600 font-medium">⏱ 1 giờ</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="bg-white rounded-2xl p-8 shadow-sm">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Chủ đề phổ biến</h3>
                <div class="flex flex-wrap gap-3">
                    <span
                        class="px-4 py-2 bg-purple-100 text-purple-700 rounded-full text-sm font-medium hover:bg-purple-200 cursor-pointer">
                        #GenerativeAI
                    </span>
                    <span
                        class="px-4 py-2 bg-pink-100 text-pink-700 rounded-full text-sm font-medium hover:bg-pink-200 cursor-pointer">
                        #ImageGeneration
                    </span>
                    <span
                        class="px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-medium hover:bg-blue-200 cursor-pointer">
                        #VideoAI
                    </span>
                    <span
                        class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-medium hover:bg-green-200 cursor-pointer">
                        #ComputerVision
                    </span>
                    <span
                        class="px-4 py-2 bg-orange-100 text-orange-700 rounded-full text-sm font-medium hover:bg-orange-200 cursor-pointer">
                        #Automation
                    </span>
                    <span
                        class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium hover:bg-indigo-200 cursor-pointer">
                        #DataScience
                    </span>
                    <span
                        class="px-4 py-2 bg-red-100 text-red-700 rounded-full text-sm font-medium hover:bg-red-200 cursor-pointer">
                        #MachineLearning
                    </span>
                    <span
                        class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium hover:bg-yellow-200 cursor-pointer">
                        #DeepLearning
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Use Cases Section -->
    <section id="use-cases" class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Ứng dụng thực tế
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Khám phá cách AI đang thay đổi các ngành công nghiệp
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl p-8 text-white shadow-xl">
                    <i class="ri-shopping-cart-line ri-3x mb-4"></i>
                    <h3 class="text-2xl font-bold mb-4">E-Commerce & Retail</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Product recommendation AI với personalization</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Visual search cho sản phẩm</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Chatbot hỗ trợ khách hàng 24/7</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Demand forecasting & inventory optimization</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl p-8 text-white shadow-xl">
                    <i class="ri-hospital-line ri-3x mb-4"></i>
                    <h3 class="text-2xl font-bold mb-4">Healthcare & Medical</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Medical imaging analysis & diagnostics</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Drug discovery với AI</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Patient monitoring & predictive care</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Electronic health records automation</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl p-8 text-white shadow-xl">
                    <i class="ri-bank-line ri-3x mb-4"></i>
                    <h3 class="text-2xl font-bold mb-4">Finance & Banking</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Fraud detection & prevention</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Algorithmic trading với AI</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Credit scoring & risk assessment</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Customer service automation</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl p-8 text-white shadow-xl">
                    <i class="ri-megaphone-line ri-3x mb-4"></i>
                    <h3 class="text-2xl font-bold mb-4">Marketing & Advertising</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Content generation tự động</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Ad targeting & optimization</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Sentiment analysis & social listening</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-1"></i>
                            <span>Marketing campaign automation</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 lg:py-24 bg-gradient-to-br from-primary to-purple-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">
                Sẵn sàng khám phá<br>thế giới AI?
            </h2>
            <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto leading-relaxed">
                Tham gia cộng đồng Sumotech để cập nhật những công nghệ AI mới nhất, chia sẻ kiến thức và cùng nhau phát
                triển.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}"
                    class="bg-white text-primary px-8 py-4 !rounded-button text-lg font-semibold hover:bg-gray-50 transition-colors whitespace-nowrap">
                    Đăng ký miễn phí
                </a>
                <a href="#tools"
                    class="border-2 border-white text-white px-8 py-4 !rounded-button text-lg font-semibold hover:bg-white hover:text-primary transition-colors whitespace-nowrap">
                    Khám phá ngay
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-2">
                    <img src="{{ asset('img/logo/Sumotech_round.png') }}" alt="Sumotech" class="h-12 w-auto mb-6" />
                    <p class="text-gray-300 mb-6 max-w-md leading-relaxed">
                        Sumotech - Nền tảng công nghệ AI tiên tiến, cung cấp các công cụ, kiến thức và giải pháp AI toàn
                        diện cho doanh nghiệp và cá nhân.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="ri-facebook-fill"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="ri-linkedin-fill"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="ri-youtube-fill"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="ri-github-fill"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-6">AI Tools</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-300 hover:text-primary transition-colors">Generative
                                AI</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary transition-colors">Image
                                Generation</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary transition-colors">Video AI</a>
                        </li>
                        <li><a href="#" class="text-gray-300 hover:text-primary transition-colors">AI Vision</a>
                        </li>
                        <li><a href="#"
                                class="text-gray-300 hover:text-primary transition-colors">Automation</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-6">Liên hệ</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <i class="ri-mail-line text-primary"></i>
                            <span class="text-gray-300">info@sumotech.ai</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="ri-phone-line text-primary"></i>
                            <span class="text-gray-300">+84 xxx xxx xxx</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ri-map-pin-line text-primary mt-1"></i>
                            <span class="text-gray-300">Việt Nam</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                <p class="text-gray-400">
                    © 2025 Sumotech. Tất cả quyền được bảo lưu.
                </p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Smooth scroll for anchor links only
            const links = document.querySelectorAll('a[href^="#"]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;

                    e.preventDefault();
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        const offsetTop = targetElement.offsetTop - 80;
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                        // Close mobile menu if open
                        if (mobileMenu) {
                            mobileMenu.classList.add('hidden');
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
