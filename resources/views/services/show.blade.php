<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $service->service_name ?? 'อาบน้ำ + ตัดขน Full Course' }} | Pluffy House Grooming</title>
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
            <a href="#booking">จองคิว</a>
            <a href="{{ route('dashboard') }}">การจองของฉัน</a>
        </nav>

        <div class="account">
            <a class="login" href="{{ route('login') }}">เข้าสู่ระบบ</a>
            <a class="book-top" href="#booking">จองคิวเลย ▣</a>
        </div>
    </header>

    <main id="service">
        <a class="back" href="{{ route('home') }}">← ย้อนกลับไปหน้าบริการทั้งหมด</a>

        <div class="layout">
            <section class="gallery">
                <img class="hero-photo" src="{{ asset('images/course-main.jpg') }}" alt="ช่างกำลังเป่าขนแมวสีขาวที่ร้าน grooming">
                <div class="thumbnails">
                    <img src="{{ asset('images/course-1.jpg') }}" alt="บริการอาบน้ำสัตว์เลี้ยง">
                    <img src="{{ asset('images/course-2.jpg') }}" alt="ช่างดูแลสุนัข">
                    <img src="{{ asset('images/course-3.jpg') }}" alt="สุนัขหลังแต่งขน">
                    <img src="{{ asset('images/course-4.jpg') }}" alt="ช่างอุ้มสัตว์เลี้ยงหลังใช้บริการ">
                </div>
            </section>

            <article class="details">
                <div class="meta">
                    <span class="badge">RECOMMENDED</span>
                    <span class="duration">◴ ระยะเวลา: {{ $durationLabel }}</span>
                </div>

                <h1>{{ $service->service_name ?? 'อาบน้ำ + ตัดขน Full Course (หมาและแมว)' }}</h1>

                <div class="price-row">
                    <span class="price">เริ่มต้น {{ $service->service_price ?? '700' }}฿</span>
                    <span class="price-note">*(ราคาปรับเปลี่ยนตามน้ำหนักของสัตว์เลี้ยง)</span>
                </div>

                <p class="description">{{ $service->service_description ?? 'ดีไซน์ความน่ารักให้น้องเต็มร้อยด้วยแพ็กเกจพรีเมียมที่รวบรวมทั้งการบำรุงดูแลสุขอนามัยแบบล้ำลึก และสไตล์แต่งขนสุดแสนเก๋ให้น่ารัก ช่างของเรารู้วิธีสื่อสารและสร้างความคุ้นเคยกับน้องๆ เพื่อดูแลน้องตลอดชั่วโมงบริการ' }}</p>

                <hr>

                <h2>สิ่งที่คุณพ่อคุณแม่และน้องๆ จะได้รับ:</h2>
                <ul class="benefits">
                    <li><span class="check">✓</span><span>ตัดแต่งเส้นขนทั่วร่างกายโดยช่างตัดแต่งขนสัตว์เลี้ยงมืออาชีพ</span></li>
                    <li><span class="check">✓</span><span>อาบน้ำสปาบำรุงออร์แกนิกและลงคอนดิชันเนอร์บำรุงเส้นขนให้อ่อนนุ่ม</span></li>
                    <li><span class="check">✓</span><span>ตัดเล็บ ตะไบแต่งขนมุม เพื่อป้องกันผิวหนังอักเสบและการขีดข่วน</span></li>
                    <li><span class="check">✓</span><span>เช็ดหู ทำความสะอาดช่องหูอย่างอ่อนโยน ป้องกันกลิ่นอับและยีสต์</span></li>
                    <li><span class="check">✓</span><span>นวดบำบัดเพื่อระบายของเหลวสะสม ช่วยลดกลิ่นเหม็นอับไม่พึงประสงค์</span></li>
                    <li><span class="check">✓</span><span>เป่าไดร์ขนสัตว์จนแห้ง นุ่มร่วง และนวดผิวหนังด้วยการกระตุ้นการไหลเวียนโลหิต</span></li>
                </ul>

                <div class="actions" id="booking">
                    <a class="book-main" href="{{ route('booking.create', $serviceSlug) }}">จองบริการนี้ทันที ✨</a>
                    <a class="line-button" href="#booking">สอบถามข้อมูลผ่าน LINE</a>
                </div>
            </article>
        </div>
    </main>
    </div>
</body>
</html>
