<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จองบริการ | Pluffy House Grooming</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
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
            <a class="active" href="{{ route('booking.create', $serviceSlug) }}">จองคิว</a>
            <a href="{{ route('dashboard') }}">การจองของฉัน</a>
        </nav>
        <div class="account">
            <a class="login" href="{{ route('login') }}">เข้าสู่ระบบ</a>
            <a class="book-top" href="{{ route('booking.create', $serviceSlug) }}">จองคิวเลย ▣</a>
        </div>
    </header>

    <main class="booking-page">
        <section class="steps" aria-label="ขั้นตอนการจอง">
            <div class="step active"><span>1</span><strong>สัตว์เลี้ยง</strong></div>
            <i></i>
            <div class="step"><span>2</span><strong>วันเวลา</strong></div>
            <i></i>
            <div class="step"><span>3</span><strong>ช่างแต่งขน</strong></div>
            <i></i>
            <div class="step"><span>4</span><strong>ยืนยัน</strong></div>
        </section>

        <div class="booking-layout">
            <div class="booking-main">
                <section class="panel pets-panel">
                    <h1>เลือกสัตว์เลี้ยงของคุณ <span>🐾</span></h1>
                    <p class="demo-note">ข้อมูลสัตว์เลี้ยงด้านล่างเป็นตัวอย่างหน้าจอ ยังไม่ได้บันทึกลงฐานข้อมูล</p>
                    <div class="pet-list">
                        @foreach ($pets as $pet)
                            <label class="pet-option{{ $loop->first ? ' selected' : '' }}">
                                <input type="radio" name="pet" value="{{ $pet->id }}" {{ $loop->first ? 'checked' : '' }}>
                                <img src="{{ asset($pet->image === 'images/pet-dog.jpg' ? 'images/service-bath.jpg' : 'images/service-full-course.jpg') }}" alt="{{ $pet->name }}">
                                <span class="pet-info">
                                    <strong>{{ $pet->name }}</strong>
                                    <small>{{ $pet->type }} · {{ $pet->breed }} · {{ $pet->age }} · น้ำหนัก {{ $pet->weight }}</small>
                                </span>
                                <span class="radio-mark" aria-hidden="true">✓</span>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="panel add-pet-panel">
                    <h2>＋ ลงทะเบียนสัตว์เลี้ยงใหม่</h2>
                    <div class="pet-type-label">ประเภทสัตว์เลี้ยง</div>
                    <div class="type-options">
                        <label class="type-option chosen"><input type="radio" name="pet_type" checked> สุนัข 🐶</label>
                        <label class="type-option"><input type="radio" name="pet_type"> แมว 🐱</label>
                    </div>
                    <div class="pet-fields">
                        <label>ชื่อสัตว์เลี้ยง<input type="text" placeholder="เช่น น้องบู้เบ๊บ"></label>
                        <label>สายพันธุ์<input type="text" placeholder="เช่น ปอมเมอเรเนียน"></label>
                        <label>อายุ (ปี/เดือน)<input type="text" placeholder="เช่น 2 ปี"></label>
                        <label>น้ำหนัก (กก.)<input type="number" step="0.1" placeholder="เช่น 4.5"></label>
                    </div>
                    <button class="outline-button" type="button">บันทึกและเลือกสัตว์เลี้ยงใหม่ 🐾</button>
                </section>
            </div>

            <aside class="panel summary-panel">
                <h2>สรุปรายการจองคิว</h2>
                <div class="summary-pet">
                    <img src="{{ asset('images/service-bath.jpg') }}" alt="สัตว์เลี้ยงที่เลือก">
                    <div><small>สัตว์เลี้ยง</small><strong>น้องพลัฟฟี่ · มอลทีส</strong></div>
                </div>
                <div class="summary-row"><small>บริการ</small><strong>{{ $service->service_name }}</strong></div>
                <div class="summary-row"><small>วันเวลา</small><span>ยังไม่ได้เลือกวันเวลา</span></div>
                <div class="summary-price"><strong>ยอดชำระโดยประมาณ</strong><b>{{ $service->service_price }}฿</b></div>
                <button class="primary-button" type="button">ขั้นตอนถัดไป (เลือกวันเวลา) ➡️</button>
                <p class="summary-note">หน้านี้เป็นขั้นตอนเลือกสัตว์เลี้ยงและแสดงบริการที่เลือกจากหน้าก่อนหน้า</p>
            </aside>
        </div>
    </main>
</body>
</html>
