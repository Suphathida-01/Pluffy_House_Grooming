<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $service->service_name }} | Pluffy House Grooming</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/servis.css') }}">
    
</head>
<body class="service-page">

    <div class="service-card">
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark">🐾</span>
            <span class="brand-name">Pluffy House <span>Grooming</span></span>
        </a>

        <nav class="nav">
            <a href="{{ route('home') }}">Home</a>
            <a class="active" href="{{ route('home') }}">บริการ</a>
            <a href="{{ route('booking.create', $serviceSlug) }}">จองคิว</a>
            <a href="{{ route('bookings.index') }}">การจองของฉัน</a>
        </nav>

        <div class="account">
            <a class="login" href="{{ route('login') }}">เข้าสู่ระบบ</a>
            <a class="book-top" href="{{ route('booking.create', $serviceSlug) }}">จองคิวเลย ▣</a>
        </div>
    </header>

    <main id="service">
        <a class="back" href="{{ route('home') }}">← ย้อนกลับไปหน้าบริการทั้งหมด</a>

        <div class="layout">
            <section class="gallery">
                <img class="hero-photo" src="{{ asset('images/' . $service->hero_image) }}" alt="{{ $service->service_name }} ที่ Pluffy House Grooming">
                <div class="thumbnails">
                    @foreach ($service->gallery_images as $image)
                        <img src="{{ asset('images/' . $image) }}" alt="ภาพตัวอย่างบริการ{{ $service->service_name }}">
                    @endforeach
                </div>
            </section>

            <article class="details">
                <div class="meta">
                    <span class="badge">{{ $service->badge }}</span>
                    <span class="duration">◴ ระยะเวลา: {{ $durationLabel }}</span>
                </div>

                <h1>{{ $service->service_name }}</h1>

                <div class="price-row">
                    <span class="price">เริ่มต้น {{ number_format($service->service_price) }}฿</span>
                    <span class="price-note">*{{ $service->price_note }}</span>
                </div>

                <div class="weight-prices">
                    <h2>ราคาแยกตามน้ำหนัก</h2>
                    @foreach ($priceTiers as $tier)
                        <div class="weight-price-row">
                            <span>{{ $tier['label'] }}</span>
                            <strong>{{ number_format($tier['price'], 0) }}฿</strong>
                        </div>
                    @endforeach
                </div>

                <p class="description">{{ $service->service_description }}</p>

                <hr>

                <h2>สิ่งที่คุณพ่อคุณแม่และน้องๆ จะได้รับ:</h2>
                <ul class="benefits">
                    @foreach ($service->benefits as $benefit)
                        <li><span class="check">✓</span><span>{{ $benefit }}</span></li>
                    @endforeach
                </ul>

                <div class="actions" id="booking">
                    <a class="book-main" href="{{ route('booking.create', $serviceSlug) }}">จองบริการนี้ทันที ✨</a>
                </div>
            </article>
        </div>
    </main>
    </div>
</body>
</html>
