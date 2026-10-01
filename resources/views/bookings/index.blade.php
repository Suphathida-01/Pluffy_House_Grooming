<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>การจองของฉัน | Pluffy House Grooming</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}?v={{ filemtime(public_path('css/booking.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/my-bookings.css') }}?v={{ filemtime(public_path('css/my-bookings.css')) }}">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark">🐾</span>
            <span class="brand-name">Pluffy House <span>Grooming</span></span>
        </a>
        <nav class="nav">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('home') }}">บริการ</a>
            <a href="{{ route('booking.create', 'full-course') }}">จองคิว</a>
            <a class="active" href="{{ route('bookings.index') }}">การจองของฉัน</a>
        </nav>
        <div class="account">
            <a class="login" href="{{ route('login') }}">เข้าสู่ระบบ</a>
            <a class="book-top" href="{{ route('home') }}">เลือกบริการ ▣</a>
        </div>
    </header>

    <main class="my-bookings-page">
        <div class="my-bookings-heading">
            <div>
                <span class="eyebrow">PLUFFY HOUSE GROOMING</span>
                <h1>การจองของฉัน <span>🐾</span></h1>
                <p>ดูนัดหมายที่กำลังจะมาถึงและประวัติการใช้บริการของน้องๆ</p>
            </div>
            <a class="primary-button new-booking-link" href="{{ route('home') }}">＋ จองบริการใหม่</a>
        </div>

        <div class="booking-demo-note"><span>i</span> หน้านี้แสดงรายการตัวอย่าง ยังไม่ได้เชื่อมฐานข้อมูลหรือบันทึกการจองจริง</div>

        <section class="booking-stats" aria-label="สรุปการจอง">
            <article><span class="stat-icon upcoming-icon">🗓️</span><div><small>กำลังจะมาถึง</small><strong>{{ count(array_filter($bookings, fn ($booking) => $booking['status'] === 'upcoming')) }}</strong></div></article>
            <article><span class="stat-icon completed-icon">✓</span><div><small>ใช้บริการแล้ว</small><strong>{{ count(array_filter($bookings, fn ($booking) => $booking['status'] === 'completed')) }}</strong></div></article>
            <article><span class="stat-icon total-icon">🐶</span><div><small>รายการทั้งหมด</small><strong>{{ count($bookings) }}</strong></div></article>
        </section>

        <section class="bookings-section">
            <div class="bookings-section-heading">
                <h2>รายการนัดหมาย</h2>
                <span>{{ count($bookings) }} รายการ</span>
            </div>

            <div class="booking-filters" role="group" aria-label="กรองรายการจอง">
                <button class="booking-filter active" type="button" data-filter="all" aria-pressed="true">ทั้งหมด</button>
                <button class="booking-filter" type="button" data-filter="upcoming" aria-pressed="false">กำลังจะมาถึง</button>
                <button class="booking-filter" type="button" data-filter="completed" aria-pressed="false">เสร็จสิ้น</button>
                <button class="booking-filter" type="button" data-filter="cancelled" aria-pressed="false">ยกเลิก</button>
            </div>

            <div class="booking-cards" id="booking-cards">
                @foreach ($bookings as $booking)
                    <article class="my-booking-card" data-status="{{ $booking['status'] }}">
                        <div class="booking-card-top">
                            <div class="booking-card-id"><small>หมายเลขการจอง</small><strong>{{ $booking['id'] }}</strong></div>
                            <span class="booking-status {{ $booking['status'] }}"><i></i>{{ $booking['status_label'] }}</span>
                        </div>
                        <div class="booking-card-body">
                            <div class="booking-pet-avatar {{ $booking['pet_type'] === 'แมว' ? 'cat' : 'dog' }}" aria-hidden="true">{{ $booking['pet_type'] === 'แมว' ? '🐱' : '🐶' }}</div>
                            <div class="booking-main-info">
                                <h3>{{ $booking['service_name'] }}</h3>
                                <p class="booking-pet-name">{{ $booking['pet_name'] }} <span>· {{ $booking['pet_breed'] }}</span></p>
                                <div class="booking-meta">
                                    <span>🗓️ {{ $booking['date'] }}</span>
                                    <span>◷ {{ $booking['time'] }}</span>
                                    <span>⏱️ {{ $booking['duration'] }}</span>
                                </div>
                            </div>
                            <div class="booking-card-price"><small>ยอดชำระ</small><strong>{{ number_format($booking['price']) }}฿</strong></div>
                        </div>
                        <div class="booking-card-bottom">
                            <span class="sample-label">ข้อมูลตัวอย่าง</span>
                            <a class="booking-card-link" href="{{ route('services.show', $booking['service_slug']) }}">ดูรายละเอียดบริการ <span>→</span></a>
                        </div>
                    </article>
                @endforeach
                <div class="booking-empty" id="booking-empty" hidden>
                    <span>🐾</span>
                    <strong>ยังไม่มีรายการในหมวดนี้</strong>
                    <p>ลองเลือกหมวดอื่น หรือจองบริการให้น้องๆ ได้เลยค่ะ</p>
                    <a class="primary-button" href="{{ route('home') }}">เลือกบริการ</a>
                </div>
            </div>
        </section>
    </main>

    <script src="{{ asset('js/my-bookings.js') }}?v={{ filemtime(public_path('js/my-bookings.js')) }}"></script>
</body>
</html>
