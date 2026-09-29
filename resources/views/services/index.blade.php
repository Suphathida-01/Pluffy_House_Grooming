<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>บริการทั้งหมด | Pluffy House Grooming</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark">🐾</span>
            <span class="brand-name">Pluffy House <span>Grooming</span></span>
        </a>

        <nav class="nav">
            <a href="{{ route('home') }}">Home</a>
            <a class="active" href="{{ route('home') }}">บริการ</a>
            <a href="#services">จองคิว</a>
            <a href="{{ route('dashboard') }}">การจองของฉัน</a>
        </nav>

        <div class="account">
            <a class="login" href="{{ route('login') }}">เข้าสู่ระบบ</a>
            <a class="book-top" href="#services">จองคิวเลย ▣</a>
        </div>
    </header>

    <main>
        <section class="intro" id="services">
            <h1>บริการทั้งหมดของเรา 🫧</h1>
            <p>เรามีบริการดูแลสัตว์เลี้ยงครอบคลุมตั้งแต่สุขอนามัยพื้นฐานไปจนถึงสปาและแฟชั่นระดับมืออาชีพ<br>เลือกแพ็กเกจที่ต้องการด้านล่าง</p>
        </section>

        <section class="service-list" aria-label="รายการบริการ">
            <article class="service-card">
                <img src="{{ asset('images/service-bath.jpg') }}" alt="บริการอาบน้ำสปาถนอมผิว">
                <div class="card-content">
                    <div class="card-heading"><h2>อาบน้ำสปาถนอมผิว</h2><strong>300฿</strong></div>
                    <p class="duration">◴ ระยะเวลา: 1 ชม.</p>
                    <p class="summary">ตัดเล็บ, ตะไบเล็บ, เช็ดหู, ขับสิ่งสกปรก, อาบน้ำแชมพูสูตรถนอมผิว นุ่มชุ่มชื้น</p>
                    <a class="detail-button" href="{{ route('services.show', 'spa-bath') }}">ดูรายละเอียดบริการนี้ ✂</a>
                </div>
            </article>

            <article class="service-card">
                <img src="{{ asset('images/service-trim.jpg') }}" alt="บริการตัดขนสุนัขและแมว">
                <div class="card-content">
                    <div class="card-heading"><h2>ตัดขนสุนัข/แมวมาตรฐาน</h2><strong>500฿</strong></div>
                    <p class="duration">◴ ระยะเวลา: 1 ชม.</p>
                    <p class="summary">ตัดแต่งทรงขน ปรับเส้นขนตามความต้องการ ด้วยมาตรฐานมืออาชีพและประสบการณ์ดูแลสัตว์เลี้ยง</p>
                    <a class="detail-button" href="{{ route('services.show', 'standard-trim') }}">ดูรายละเอียดบริการนี้ ✂</a>
                </div>
            </article>

            <article class="service-card">
                <img src="{{ asset('images/service-full-course.jpg') }}" alt="บริการอาบน้ำและตัดขน Full Course สำหรับสุนัขและแมว">
                <div class="card-content">
                    <div class="card-heading"><h2>อาบน้ำ + ตัดขน Full Course</h2><strong>700฿</strong></div>
                    <p class="duration">◴ ระยะเวลา: 2 ชม.</p>
                    <p class="summary">แพ็กเกจครบวงจรแบบพรีเมียม ทั้งทำความสะอาดขนและเส้นใยครบวงจร พร้อมดูแลสุขอนามัย</p>
                    <a class="detail-button" href="{{ route('services.show', 'full-course') }}">ดูรายละเอียดบริการนี้ ✂</a>
                </div>
            </article>

        </section>
    </main>
</body>
</html>
