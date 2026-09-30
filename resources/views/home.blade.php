<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pluffy House Grooming</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>

    {{-- ===== Navbar ===== --}}
    <header class="navbar">
        <div class="container">
            <a href="{{ url('/') }}" class="logo">
                <div class="logo-icon">🐾</div>
                Pluffy House <span>Grooming</span>
            </a>

            <ul class="nav-menu">
                <li><a href="#home" class="active">Home</a></li>
                <li><a href="{{ route('services.index') }}">บริการ</a></li>
                <li><a href="#booking">จองคิว</a></li>
                <li><a href="#my-bookings">การจองของฉัน</a></li>
            </ul>

            <div class="nav-right">
                @auth
                    <span>สวัสดี, {{ Auth::user()->user_name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="link-btn">ออกจากระบบ</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">เข้าสู่ระบบ</a>
                @endauth
                <a href="#booking" class="btn btn-primary">จองคิวเลย 📅</a>
            </div>
        </div>
    </header>

    {{-- ===== Hero ===== --}}
    <section class="hero" id="home">
        <div class="container">
            <div>
                <span class="badge">👑 ที่ปรึกษาด้านการดูแลสัตว์เลี้ยงที่คุณไว้วางใจ</span>
                <h1>ดูแลน้องให้สะอาด น่ารัก<br>และมีความสุข 🐶🐱</h1>
                <p>
                    สัมผัสประสบการณ์การตัดแต่งขนสุดพรีเมียม สะอาด ปลอดภัย
                    ด้วยสไตลิสต์ผู้เชี่ยวชาญ คัดสรรผลิตภัณฑ์ที่อ่อนโยนที่สุด
                    เพื่อดูแลน้องๆ สมาชิกคนสำคัญในบ้านของคุณ
                </p>
                <div class="hero-buttons">
                    <a href="#booking" class="btn btn-primary">จองคิวบริการเลย 📅</a>
                </div>
                <div class="stats">
                    <div class="stat"><strong>10,000+</strong><span>น้องๆ สมาชิกที่ไว้วางใจ</span></div>
                    <div class="stat"><strong>5.0 ★</strong><span>รีวิวจากผู้ใช้บริการจริง</span></div>
                    <div class="stat"><strong>100%</strong><span>แชมพูอ่อนโยนปลอดภัย</span></div>
                </div>
            </div>

            <div class="hero-image">
                <img src="{{ asset('images/ภาพhome.jpg') }}" alt="Pluffy House Grooming">
            </div>
        </div>
    </section>

    {{-- ===== Why choose us ===== --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <small>ทำไมต้องเลือกเรา</small>
                <h2>ดูแลด้วยหัวใจและมาตรฐานระดับพรีเมียม</h2>
            </div>
            <div class="features">
                <div class="feature-card">
                    <div class="icon">🧼</div>
                    <h3>สะอาด ปลอดภัย</h3>
                    <p>อุปกรณ์และสถานที่ผ่านการฆ่าเชื้อ ใช้ผลิตภัณฑ์อ่อนโยนต่อผิวน้อง</p>
                </div>
                <div class="feature-card">
                    <div class="icon">✂️</div>
                    <h3>ช่างตัดขนมืออาชีพ</h3>
                    <p>สไตลิสต์ผู้ชำนาญ ตัดแต่งได้ตามสายพันธุ์และความต้องการของเจ้าของ</p>
                </div>
                <div class="feature-card">
                    <div class="icon">💖</div>
                    <h3>ใส่ใจทุกรายละเอียด</h3>
                    <p>เอาใจใส่น้องๆ ตลอดการให้บริการ พร้อมแจ้งอัปเดตสถานะให้เจ้าของทราบ</p>
                </div>
                <div class="feature-card">
                    <div class="icon">🏡</div>
                    <h3>บริการประทับใจ</h3>
                    <p>บรรยากาศอบอุ่นเหมือนบ้าน ให้น้องรู้สึกผ่อนคลายทุกครั้งที่มา</p>
                </div>
            </div>
        </div>
    </section>


</body>
</html>