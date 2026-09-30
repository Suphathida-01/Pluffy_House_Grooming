<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรกแอดมิน | Pluffy House</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8f7fb; color: #242338; font-family: Arial, Tahoma, sans-serif; }
        .dashboard { min-height: 100vh; }
        .sidebar { position: fixed; inset: 0 auto 0 0; z-index: 2; display: flex; flex-direction: column; width: 260px; padding: 24px 16px 18px; background: #11111f; color: #fff; }
        .brand { display: flex; align-items: center; gap: 12px; padding: 0 4px 22px; border-bottom: 1px solid #302f40; }
        .brand-icon { display: grid; width: 40px; height: 40px; place-items: center; border-radius: 12px; background: #8955f5; font-size: 21px; }
        .brand-name { margin: 0; font-size: 16px; }
        .brand-caption { margin: 4px 0 0; color: #aaa9b9; font-size: 10px; letter-spacing: .08em; }
        .menu { display: grid; gap: 7px; margin-top: 20px; }
        .menu a { display: flex; align-items: center; gap: 12px; min-height: 44px; padding: 0 12px; border: 1px solid transparent; border-radius: 8px; color: #aaa9b9; font-size: 13px; text-decoration: none; }
        .menu a.active { border-color: #8955f5; background: #21183a; color: #fff; }
        .menu-icon { width: 22px; text-align: center; font-size: 16px; }
        .admin { display: flex; align-items: center; gap: 10px; margin-top: auto; padding-top: 16px; border-top: 1px solid #302f40; }
        .avatar { display: grid; width: 36px; height: 36px; flex: 0 0 36px; place-items: center; border-radius: 50%; background: #f1ebff; color: #8955f5; font-size: 11px; font-weight: 700; }
        .admin-name { font-size: 12px; font-weight: 700; }
        .admin-role { margin-top: 3px; color: #aaa9b9; font-size: 10px; }
        .main { width: calc(100% - 260px); max-width: 1600px; margin-left: 260px; padding: 30px; }
        .topbar { display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; margin-bottom: 22px; }
        h1 { margin: 0; font-size: 24px; }
        .subtitle { margin: 7px 0 0; color: #858394; font-size: 13px; }
        .updated { padding: 9px 12px; border: 1px solid #e8e3f1; border-radius: 8px; background: #fff; color: #777585; font-size: 11px; white-space: nowrap; }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 16px; }
        .card { border: 1px solid #e9e4f1; border-radius: 12px; background: #fff; box-shadow: 0 3px 12px rgba(52, 35, 82, .035); }
        .stat-card { min-height: 112px; padding: 17px; }
        .stat-heading { display: flex; align-items: center; justify-content: space-between; color: #777585; font-size: 12px; }
        .stat-icon { display: grid; width: 30px; height: 30px; place-items: center; border-radius: 9px; background: #f1eaff; color: #8955f5; font-size: 15px; }
        .stat-value { margin-top: 11px; font-size: 23px; font-weight: 700; }
        .stat-unit { color: #858394; font-size: 12px; font-weight: 400; }
        .chart-card { margin-bottom: 16px; padding: 20px 22px 14px; }
        .section-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; }
        h2 { margin: 0; font-size: 15px; }
        .section-note { margin: 5px 0 0; color: #9694a2; font-size: 11px; }
        .chart { display: flex; align-items: flex-end; justify-content: space-around; gap: 12px; height: 225px; margin-top: 14px; padding: 10px 10px 0; border-bottom: 1px solid #ece9f1; background: repeating-linear-gradient(to bottom, transparent 0, transparent 51px, #f0edf4 52px); }
        .bar-column { display: flex; width: 70px; height: 100%; flex-direction: column; align-items: center; justify-content: flex-end; }
        .bar-value { margin-bottom: 6px; color: #777585; font-size: 10px; }
        .bar { width: 28px; min-height: 3px; border-radius: 6px 6px 0 0; background: #8955f5; }
        .bar-label { padding: 8px 0 10px; color: #777585; font-size: 10px; }
        .bottom-grid { display: grid; grid-template-columns: 1.2fr .8fr; gap: 16px; }
        .detail-card { min-height: 250px; padding: 19px; }
        .service-list { display: grid; gap: 17px; margin-top: 21px; }
        .service-top { display: flex; justify-content: space-between; gap: 10px; font-size: 12px; }
        .service-name { font-weight: 600; }
        .service-count { color: #777585; white-space: nowrap; }
        .progress-track { height: 7px; margin-top: 8px; overflow: hidden; border-radius: 7px; background: #f0edf4; }
        .progress { height: 100%; border-radius: inherit; background: #8955f5; }
        .empty { margin: 24px 0 0; color: #92909f; font-size: 12px; text-align: center; }
        .status-layout { display: flex; align-items: center; justify-content: center; gap: 24px; margin-top: 22px; }
        .donut { display: grid; width: 122px; height: 122px; flex: 0 0 122px; place-items: center; border-radius: 50%; background: conic-gradient(#19a974 0 0%, #f2a93b 0 0%, #e46b73 0 0%, #eeedf2 0 100%); }
        .donut-inner { display: flex; width: 82px; height: 82px; flex-direction: column; align-items: center; justify-content: center; border-radius: 50%; background: #fff; }
        .donut-total { font-size: 20px; font-weight: 700; }
        .donut-caption { margin-top: 3px; color: #8a8897; font-size: 9px; }
        .legend { display: grid; gap: 13px; min-width: 125px; }
        .legend-item { display: flex; align-items: center; justify-content: space-between; gap: 16px; color: #5f5d6b; font-size: 11px; }
        .legend-label { display: flex; align-items: center; gap: 7px; }
        .dot { width: 8px; height: 8px; border-radius: 50%; }
        .dot.pending { background: #f2a93b; }
        .dot.confirmed { background: #e46b73; }
        .dot.completed { background: #19a974; }
        .footer-note { margin: 15px 2px 0; color: #93919f; font-size: 10px; }
        @media (max-width: 1050px) {
            .sidebar { width: 220px; }
            .main { width: calc(100% - 220px); margin-left: 220px; padding: 24px; }
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 720px) {
            .sidebar { position: static; width: auto; min-height: auto; padding: 16px; }
            .brand { padding-bottom: 14px; }
            .menu { grid-template-columns: repeat(2, minmax(0, 1fr)); margin-top: 12px; }
            .menu a { min-height: 40px; padding: 0 8px; font-size: 11px; }
            .admin { display: none; }
            .main { width: 100%; margin: 0; padding: 20px 15px; }
            .bottom-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 460px) {
            .topbar { flex-direction: column; }
            h1 { font-size: 21px; }
            .stats { gap: 9px; }
            .stat-card { min-height: 100px; padding: 12px; }
            .stat-heading { font-size: 10px; }
            .stat-value { font-size: 19px; }
            .chart-card, .detail-card { padding: 15px; }
            .chart { gap: 2px; height: 195px; padding-right: 0; padding-left: 0; }
            .bar-column { width: 48px; }
            .bar { width: 22px; }
            .bar-value, .bar-label { font-size: 9px; }
            .status-layout { gap: 12px; }
            .donut { width: 100px; height: 100px; flex-basis: 100px; }
            .donut-inner { width: 68px; height: 68px; }
            .legend { min-width: 110px; }
        }
    </style>
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">🐾</div>
            <div>
                <h2 class="brand-name">Pluffy House</h2>
                <p class="brand-caption">BACK-OFFICE</p>
            </div>
        </div>
        <nav class="menu" aria-label="เมนูหลัก">
            <a href="{{ route('admin.home') }}" class="active" aria-current="page"><span class="menu-icon">⌂</span>หน้าแรก</a>
            <a href="{{ route('bookings.index') }}"><span class="menu-icon">▣</span>การจอง</a>
            <a href="{{ route('customers.index') }}"><span class="menu-icon">♙</span>จัดการลูกค้า</a>
            <a href="#services"><span class="menu-icon">✂</span>บริการ &amp; แพ็กเกจ</a>
            <a href="#staff"><span class="menu-icon">♧</span>จัดการช่าง</a>
            <a href="#"><span class="menu-icon">▥</span>รายงานยอดขาย</a>
        </nav>
        <div class="admin">
            <div class="avatar">ADM</div>
            <div><div class="admin-name">แอดมิน</div><div class="admin-role">ผู้ดูแลระบบ</div></div>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h1>ภาพรวมร้าน</h1>
                <p class="subtitle">สรุปข้อมูลลูกค้า สัตว์เลี้ยง บริการ การจอง และรายได้ของร้าน</p>
            </div>
            <div class="updated">ข้อมูล ณ {{ now()->format('d/m/Y H:i') }}</div>
        </header>

        <section class="stats" aria-label="ข้อมูลสรุป">
            <article class="card stat-card">
                <div class="stat-heading">จำนวนลูกค้าทั้งหมด <span class="stat-icon">♙</span></div>
                <div class="stat-value">{{ number_format($customerCount) }} <span class="stat-unit">ราย</span></div>
            </article>
            <article class="card stat-card">
                <div class="stat-heading">จำนวนสัตว์เลี้ยง <span class="stat-icon">🐾</span></div>
                <div class="stat-value">{{ number_format($petCount) }} <span class="stat-unit">ตัว</span></div>
            </article>
            <article class="card stat-card">
                <div class="stat-heading">การจองทั้งหมด <span class="stat-icon">▣</span></div>
                <div class="stat-value">{{ number_format($bookingCount) }} <span class="stat-unit">ครั้ง</span></div>
            </article>
            <article class="card stat-card">
                <div class="stat-heading">รายได้รวมที่ชำระแล้ว <span class="stat-icon">฿</span></div>
                <div class="stat-value"><span class="stat-unit">฿</span>{{ number_format($revenue, 2) }}</div>
            </article>
        </section>

        <section class="card chart-card" aria-labelledby="bookings-chart-heading">
            <div class="section-heading">
                <div>
                    <h2 id="bookings-chart-heading">แนวโน้มการจอง 6 เดือนล่าสุด</h2>
                    <p class="section-note">จำนวนการจองรายเดือนสำหรับดูภาพรวมการใช้บริการ</p>
                </div>
            </div>
            <div class="chart" role="img" aria-label="กราฟจำนวนการจองหกเดือนล่าสุด">
                @foreach ($monthlyBookings as $month)
                    <div class="bar-column">
                        <span class="bar-value">{{ number_format($month['count']) }} รายการ</span>
                        <div class="bar" style="height: {{ max(3, ($month['count'] / $maxMonthlyBookings) * 160) }}px"></div>
                        <span class="bar-label">{{ $month['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="bottom-grid">
            <section class="card detail-card" id="services" aria-labelledby="services-heading">
                <div class="section-heading">
                    <div><h2 id="services-heading">บริการที่ได้รับความนิยม</h2><p class="section-note">เรียงตามจำนวนการจอง</p></div>
                </div>
                @php($largestServiceCount = max(1, (int) $topServices->max('bookings_count')))
                @if ($topServices->isEmpty())
                    <p class="empty">ยังไม่มีข้อมูลบริการในฐานข้อมูล</p>
                @else
                    <div class="service-list">
                        @foreach ($topServices as $service)
                            <div>
                                <div class="service-top">
                                    <span class="service-name">{{ $service->service_name }}</span>
                                    <span class="service-count">{{ number_format($service->bookings_count) }} การจอง</span>
                                </div>
                                <div class="progress-track"><div class="progress" style="width: {{ ($service->bookings_count / $largestServiceCount) * 100 }}%"></div></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="card detail-card" id="bookings" aria-labelledby="bookings-heading">
                <div class="section-heading">
                    <div><h2 id="bookings-heading">สถานะการจอง</h2><p class="section-note">จำนวนรายการแยกตามสถานะ</p></div>
                </div>
                <div class="status-layout">
                    <div class="donut" style="background: {{ $totalBookings ? 'conic-gradient(#19a974 0 '.$completedPercent.'%, #e46b73 '.$completedPercent.'% '.($completedPercent + $confirmedPercent).'%, #f2a93b '.($completedPercent + $confirmedPercent).'% 100%)' : '#eeedf2' }}">
                        <div class="donut-inner"><span class="donut-total">{{ number_format($totalBookings) }}</span><span class="donut-caption">การจอง</span></div>
                    </div>
                    <div class="legend">
                        <div class="legend-item"><span class="legend-label"><i class="dot pending"></i>รอดำเนินการ</span><strong>{{ number_format($bookingStatuses['pending']) }}</strong></div>
                        <div class="legend-item"><span class="legend-label"><i class="dot confirmed"></i>ยืนยันแล้ว</span><strong>{{ number_format($bookingStatuses['confirmed']) }}</strong></div>
                        <div class="legend-item"><span class="legend-label"><i class="dot completed"></i>เสร็จสิ้น</span><strong>{{ number_format($bookingStatuses['completed']) }}</strong></div>
                    </div>
                </div>
            </section>
        </div>
        <p class="footer-note">สรุปภาพรวมจากข้อมูลในฐานข้อมูล รายได้รวมเฉพาะรายการชำระเงินที่สำเร็จแล้ว</p>
    </main>
</div>
</body>
</html>
