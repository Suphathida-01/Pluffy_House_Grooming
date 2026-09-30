<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pluffy House - Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, "Tahoma", sans-serif;
        }

        body {
            background: #f8f7fb;
            color: #202033;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            width: 230px;
            background: #11111f;
            color: white;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            padding: 20px 14px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 8px 20px;
            border-bottom: 1px solid #29293b;
            margin-bottom: 18px;
        }

        .logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #8b5cf6;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
        }

        .logo-text h2 {
            font-size: 17px;
        }

        .logo-text p {
            font-size: 9px;
            color: #aaaabd;
            margin-top: 3px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .menu a {
            color: #a9a9b9;
            text-decoration: none;
            padding: 11px 12px;
            border-radius: 7px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: .2s;
        }

        .menu a:hover {
            background: #222239;
            color: white;
        }

        .menu a.active {
            background: #24154a;
            color: white;
            border: 1px solid #8b5cf6;
        }

        .menu-icon {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }

        .admin {
            position: absolute;
            bottom: 20px;
            left: 14px;
            right: 14px;
            border-top: 1px solid #29293b;
            padding-top: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f0eaff;
            color: #7c4dff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }

        .admin-name {
            font-size: 11px;
        }

        .admin-role {
            font-size: 8px;
            color: #8f8fa2;
            margin-top: 2px;
        }

        /* ================= MAIN ================= */

        .main {
            margin-left: 230px;
            width: calc(100% - 230px);
            padding: 20px 22px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .page-title h1 {
            font-size: 21px;
            margin-bottom: 5px;
        }

        .page-title p {
            color: #8a8998;
            font-size: 11px;
        }

        .search {
            width: 170px;
            height: 34px;
            border: 1px solid #e4def1;
            border-radius: 6px;
            background: white;
            padding: 0 12px;
            font-size: 10px;
            outline: none;
        }

        /* ================= STAT CARDS ================= */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 15px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e9e1f5;
            border-radius: 11px;
            padding: 14px;
            min-height: 80px;
            box-shadow: 0 2px 8px rgba(80, 50, 120, .03);
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-title {
            font-size: 10px;
            color: #777585;
        }

        .stat-icon {
            width: 23px;
            height: 23px;
            border-radius: 7px;
            background: #f2eaff;
            color: #8b5cf6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .stat-number {
            font-size: 20px;
            font-weight: bold;
            margin-top: 7px;
        }

        .increase {
            color: #20a36a;
            background: #e9f8f1;
            font-size: 8px;
            padding: 3px 5px;
            border-radius: 4px;
            margin-left: 5px;
        }

        /* ================= CHART ================= */

        .card {
            background: white;
            border: 1px solid #e9e1f5;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(80, 50, 120, .03);
        }

        .chart-card {
            padding: 16px 20px 12px;
            margin-bottom: 15px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .section-header h2 {
            font-size: 13px;
        }

        .section-header p {
            color: #9997a4;
            font-size: 9px;
            margin-top: 3px;
        }

        .chart-tabs {
            display: flex;
            gap: 10px;
            font-size: 9px;
            color: #777585;
        }

        .chart-tabs span:last-child {
            color: #8b5cf6;
            background: #f1eaff;
            padding: 4px 7px;
            border-radius: 5px;
        }

        .chart {
            height: 190px;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            padding: 20px 15px 0;
        }

        .bar-wrapper {
            height: 155px;
            width: 60px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            position: relative;
        }

        .bar-value {
            font-size: 7px;
            background: #8b5cf6;
            color: white;
            padding: 3px 5px;
            border-radius: 3px;
            margin-bottom: 5px;
        }

        .bar {
            width: 20px;
            background: #8955f5;
            border-radius: 5px 5px 0 0;
        }

        .month {
            font-size: 8px;
            color: #777585;
            margin-top: 7px;
        }

        /* ================= BOTTOM ================= */

        .bottom-grid {
            display: grid;
            grid-template-columns: 1.3fr .9fr;
            gap: 15px;
        }

        .service-card,
        .booking-card {
            padding: 16px;
            min-height: 210px;
        }

        .service-list {
            margin-top: 17px;
        }

        .service-row {
            margin-bottom: 10px;
        }

        .service-info {
            display: grid;
            grid-template-columns: 25px 1fr auto auto;
            gap: 5px;
            align-items: center;
            font-size: 9px;
        }

        .service-number {
            color: #8b5cf6;
            font-weight: bold;
        }

        .service-name {
            color: #30303c;
        }

        .service-count {
            color: #666574;
        }

        .service-price {
            font-weight: bold;
            margin-left: 5px;
        }

        .progress-bg {
            height: 5px;
            background: #eeeef0;
            border-radius: 5px;
            margin-top: 5px;
            margin-left: 25px;
        }

        .progress {
            height: 5px;
            background: #8b5cf6;
            border-radius: 5px;
        }

        /* ================= DONUT ================= */

        .donut-area {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 15px 0;
        }

        .donut {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: conic-gradient(
                #16b981 0 69%,
                #f59e0b 69% 87%,
                #ef4444 87% 100%
            );
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .donut-inner {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .donut-inner strong {
            font-size: 15px;
        }

        .donut-inner span {
            font-size: 7px;
            color: #777585;
        }

        .legend {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .legend-row {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }

        .legend-left {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        .green {
            background: #16b981;
        }

        .orange {
            background: #f59e0b;
        }

        .red {
            background: #ef4444;
        }

        .percent {
            color: #9997a4;
            margin-left: 5px;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 900px) {

            .sidebar {
                width: 190px;
            }

            .main {
                margin-left: 190px;
                width: calc(100% - 190px);
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {

            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .admin {
                position: static;
                margin-top: 20px;
            }

            .dashboard {
                flex-direction: column;
            }

            .main {
                margin-left: 0;
                width: 100%;
            }

            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="dashboard">

    <!-- ================= SIDEBAR ================= -->

    <aside class="sidebar">

        <div class="logo">
            <div class="logo-icon">🐾</div>

            <div class="logo-text">
                <h2>Pluffy House</h2>
                <p>BACK-OFFICE</p>
            </div>
        </div>

        <nav class="menu">

            <a href="#" class="active">
                <span class="menu-icon">⌂</span>
                หน้าแรก
            </a>

            <a href="#">
                <span class="menu-icon">▣</span>
                การจอง
            </a>

            <a href="#">
                <span class="menu-icon">♙</span>
                จัดการลูกค้า
            </a>

            <a href="#">
                <span class="menu-icon">✂</span>
                บริการ & แพ็กเกจ
            </a>

            <a href="#">
                <span class="menu-icon">♧</span>
                จัดการช่าง
            </a>

            <a href="#">
                <span class="menu-icon">▥</span>
                รายงานยอดขาย
            </a>

        </nav>

        <div class="admin">

            <div class="admin-info">

                <div class="admin-avatar">
                    ADM
                </div>

                <div>
                    <div class="admin-name">
                        แอดมินหลัก
                    </div>

                    <div class="admin-role">
                        ผู้ดูแลระบบ
                    </div>
                </div>

            </div>

            <div>↪</div>

        </div>

    </aside>


    <!-- ================= MAIN ================= -->

    <main class="main">

        <!-- HEADER -->

        <div class="topbar">

            <div class="page-title">
                <h1>รายงานและสรุปข้อมูล</h1>

                <p>
                    ภาพรวมข้อมูลลูกค้า สัตว์เลี้ยง การจอง และรายได้ของร้าน
                </p>
            </div>

            <input
                type="text"
                class="search"
                placeholder="🔍 ค้นหาข้อมูลรายงาน..."
            >

        </div>


        <!-- ================= STATISTICS ================= -->

        <div class="stats">

            <div class="stat-card">

                <div class="stat-top">

                    <span class="stat-title">
                        จำนวนลูกค้าทั้งหมด
                    </span>

                    <span class="stat-icon">
                        ●
                    </span>

                </div>

                <div class="stat-number">
                    1,248 ราย
                    <span class="increase">+12%</span>
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-top">

                    <span class="stat-title">
                        จำนวนสัตว์เลี้ยง
                    </span>

                    <span class="stat-icon">
                        🐾
                    </span>

                </div>

                <div class="stat-number">
                    1,876 ตัว
                    <span class="increase">+8%</span>
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-top">

                    <span class="stat-title">
                        การจองทั้งหมด
                    </span>

                    <span class="stat-icon">
                        ■
                    </span>

                </div>

                <div class="stat-number">
                    3,542 ครั้ง
                    <span class="increase">+15%</span>
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-top">

                    <span class="stat-title">
                        รายได้รวม
                    </span>

                    <span class="stat-icon">
                        ฿
                    </span>

                </div>

                <div class="stat-number">
                    ฿1,285,400
                    <span class="increase">+18%</span>
                </div>

            </div>

        </div>


        <!-- ================= CHART ================= -->

        <section class="card chart-card">

            <div class="section-header">

                <div>
                    <h2>สรุปรายได้ตามช่วงเวลา</h2>

                    <p>
                        เปรียบเทียบรายได้ของร้านในแต่ละเดือน
                    </p>
                </div>

                <div class="chart-tabs">
                    <span>รายวัน</span>
                    <span>รายสัปดาห์</span>
                    <span>รายเดือน</span>
                </div>

            </div>


            <div class="chart">

                <div class="bar-wrapper">

                    <div class="bar-value">฿185K</div>

                    <div
                        class="bar"
                        style="height: 83px;"
                    ></div>

                    <div class="month">ม.ค.</div>

                </div>


                <div class="bar-wrapper">

                    <div class="bar-value">฿210K</div>

                    <div
                        class="bar"
                        style="height: 95px;"
                    ></div>

                    <div class="month">ก.พ.</div>

                </div>


                <div class="bar-wrapper">

                    <div class="bar-value">฿198K</div>

                    <div
                        class="bar"
                        style="height: 90px;"
                    ></div>

                    <div class="month">มี.ค.</div>

                </div>


                <div class="bar-wrapper">

                    <div class="bar-value">฿225K</div>

                    <div
                        class="bar"
                        style="height: 102px;"
                    ></div>

                    <div class="month">เม.ย.</div>

                </div>


                <div class="bar-wrapper">

                    <div class="bar-value">฿240K</div>

                    <div
                        class="bar"
                        style="height: 110px;"
                    ></div>

                    <div class="month">พ.ค.</div>

                </div>


                <div class="bar-wrapper">

                    <div class="bar-value">฿227K</div>

                    <div
                        class="bar"
                        style="height: 104px;"
                    ></div>

                    <div class="month">มิ.ย.</div>

                </div>

            </div>

        </section>


        <!-- ================= BOTTOM ================= -->

        <div class="bottom-grid">


            <!-- SERVICES -->

            <section class="card service-card">

                <div class="section-header">

                    <div>

                        <h2>บริการยอดนิยม</h2>

                        <p>
                            บริการที่ลูกค้าใช้บริการและสร้างรายได้สูงสุด
                        </p>

                    </div>

                </div>


                <div class="service-list">


                    <div class="service-row">

                        <div class="service-info">

                            <span class="service-number">01</span>

                            <span class="service-name">
                                อาบน้ำและตัดขน
                            </span>

                            <span class="service-count">
                                856 ครั้ง
                            </span>

                            <span class="service-price">
                                ฿428,000
                            </span>

                        </div>

                        <div class="progress-bg">
                            <div
                                class="progress"
                                style="width: 82%;"
                            ></div>
                        </div>

                    </div>


                    <div class="service-row">

                        <div class="service-info">

                            <span class="service-number">02</span>

                            <span class="service-name">
                                อาบ-สระสัตว์เลี้ยง
                            </span>

                            <span class="service-count">
                                624 ครั้ง
                            </span>

                            <span class="service-price">
                                ฿374,400
                            </span>

                        </div>

                        <div class="progress-bg">
                            <div
                                class="progress"
                                style="width: 62%;"
                            ></div>
                        </div>

                    </div>


                    <div class="service-row">

                        <div class="service-info">

                            <span class="service-number">03</span>

                            <span class="service-name">
                                ตัดเล็บ
                            </span>

                            <span class="service-count">
                                512 ครั้ง
                            </span>

                            <span class="service-price">
                                ฿76,800
                            </span>

                        </div>

                        <div class="progress-bg">
                            <div
                                class="progress"
                                style="width: 52%;"
                            ></div>
                        </div>

                    </div>


                    <div class="service-row">

                        <div class="service-info">

                            <span class="service-number">04</span>

                            <span class="service-name">
                                ฝากเลี้ยง
                            </span>

                            <span class="service-count">
                                348 ครั้ง
                            </span>

                            <span class="service-price">
                                ฿278,400
                            </span>

                        </div>

                        <div class="progress-bg">
                            <div
                                class="progress"
                                style="width: 40%;"
                            ></div>
                        </div>

                    </div>


                    <div class="service-row">

                        <div class="service-info">

                            <span class="service-number">05</span>

                            <span class="service-name">
                                ตรวจสุขภาพ
                            </span>

                            <span class="service-count">
                                202 ครั้ง
                            </span>

                            <span class="service-price">
                                ฿121,200
                            </span>

                        </div>

                        <div class="progress-bg">
                            <div
                                class="progress"
                                style="width: 28%;"
                            ></div>
                        </div>

                    </div>


                </div>

            </section>


            <!-- BOOKING -->

            <section class="card booking-card">

                <div class="section-header">

                    <div>

                        <h2>สรุปการจอง</h2>

                        <p>
                            สถานะการจองรวมของลูกค้า
                        </p>

                    </div>

                </div>


                <div class="donut-area">

                    <div class="donut">

                        <div class="donut-inner">

                            <strong>3,542</strong>

                            <span>ทั้งหมด</span>

                        </div>

                    </div>

                </div>


                <div class="legend">

                    <div class="legend-row">

                        <div class="legend-left">

                            <span class="dot green"></span>

                            <span>เสร็จสิ้น</span>

                        </div>

                        <div>
                            2,450
                            <span class="percent">69%</span>
                        </div>

                    </div>


                    <div class="legend-row">

                        <div class="legend-left">

                            <span class="dot orange"></span>

                            <span>รอดำเนินการ</span>

                        </div>

                        <div>
                            620
                            <span class="percent">18%</span>
                        </div>

                    </div>


                    <div class="legend-row">

                        <div class="legend-left">

                            <span class="dot red"></span>

                            <span>ยกเลิก</span>

                        </div>

                        <div>
                            472
                            <span class="percent">13%</span>
                        </div>

                    </div>

                </div>

            </section>

        </div>

    </main>

</div>

</body>
</html>
