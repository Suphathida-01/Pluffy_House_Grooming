<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการการจอง | Pluffy House</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8f7fb; color: #242338; font-family: Arial, Tahoma, sans-serif; }
        a { color: inherit; text-decoration: none; }
        .page { width: min(1180px, 100%); margin: 0 auto; padding: 30px 24px 50px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
        .eyebrow { margin: 0 0 7px; color: #8955f5; font-size: 11px; font-weight: 700; letter-spacing: .08em; }
        h1 { margin: 0; font-size: 25px; }
        .subtitle { margin: 7px 0 0; color: #858394; font-size: 13px; }
        .button { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; padding: 0 16px; border: 0; border-radius: 9px; background: #8955f5; color: #fff; font-size: 13px; font-weight: 700; cursor: pointer; }
        .button.secondary { border: 1px solid #e5dff0; background: #fff; color: #514d61; }
        .cards { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
        .card { border: 1px solid #e9e4f1; border-radius: 12px; background: #fff; box-shadow: 0 3px 12px rgba(52, 35, 82, .035); }
        .stat { padding: 17px 19px; }
        .stat-label { color: #777585; font-size: 12px; }
        .stat-value { margin-top: 8px; font-size: 24px; font-weight: 700; }
        .panel { padding: 20px; }
        .filters { display: grid; grid-template-columns: minmax(220px, 1.5fr) repeat(3, minmax(130px, 1fr)) auto; align-items: end; gap: 11px; }
        label { display: grid; gap: 7px; color: #666375; font-size: 12px; font-weight: 600; }
        input, select { width: 100%; min-height: 42px; padding: 0 11px; border: 1px solid #e5e1ec; border-radius: 8px; background: #fff; color: #29273b; font: inherit; font-size: 13px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 10px; border-bottom: 1px solid #ece8f2; color: #858394; font-size: 11px; font-weight: 600; white-space: nowrap; }
        td { padding: 13px 10px; border-bottom: 1px solid #f0edf4; color: #4d4a5d; font-size: 12px; vertical-align: middle; }
        .main-text { color: #28263a; font-weight: 700; }
        .main-text a:hover { color: #7041d0; }
        .sub-text { margin-top: 4px; color: #92909f; font-size: 11px; }
        .status { display: inline-flex; padding: 5px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; white-space: nowrap; }
        .status.pending { background: #fff4dd; color: #a96900; }
        .status.confirmed { background: #fcebed; color: #b24456; }
        .status.completed { background: #e5f6ee; color: #21805e; }
        .status-form { display: flex; min-width: 190px; gap: 6px; }
        .status-form select { min-width: 120px; min-height: 34px; padding: 0 7px; font-size: 11px; }
        .status-form button { padding: 0 9px; border: 0; border-radius: 7px; background: #f0eaff; color: #7041d0; font-size: 11px; font-weight: 700; cursor: pointer; }
        .flash { margin-bottom: 14px; padding: 12px 14px; border: 1px solid #bfe8d3; border-radius: 9px; background: #effaf4; color: #216c4e; font-size: 13px; }
        .error { margin: 10px 0; color: #bd4050; font-size: 12px; }
        .empty { padding: 38px 15px; color: #8b8998; text-align: center; font-size: 13px; }
        .pagination { margin-top: 18px; }
        @media (max-width: 950px) { .filters { grid-template-columns: repeat(2, minmax(0, 1fr)); } .filters .search { grid-column: 1 / -1; } }
        @media (max-width: 620px) { .page { padding: 20px 14px 35px; } .topbar { align-items: flex-start; flex-direction: column; } .cards { gap: 8px; } .stat { padding: 13px 12px; } .stat-label { font-size: 10px; } .stat-value { font-size: 20px; } .filters { grid-template-columns: 1fr; } .filters .search { grid-column: auto; } .panel { padding: 14px; } }
    </style>
</head>
<body>
<main class="page">
    <header class="topbar">
        <div>
            <p class="eyebrow">PLUFFY HOUSE · BACK-OFFICE</p>
            <h1>จัดการการจอง</h1>
            <p class="subtitle">ตรวจสอบคิวของลูกค้า กรองรายการ และอัปเดตสถานะการให้บริการ</p>
        </div>
        <div style="display:flex;gap:9px;flex-wrap:wrap">
            <a class="button secondary" href="{{ route('admin.home') }}">กลับหน้าแรก</a>
            <a class="button" href="{{ route('bookings.create') }}">＋ สร้างการจอง</a>
        </div>
    </header>

    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif
    @if ($errors->has('booking'))
        <div class="flash" style="border-color:#f1c8ce;background:#fff3f4;color:#a83445">{{ $errors->first('booking') }}</div>
    @endif

    <section class="cards" aria-label="สรุปสถานะการจอง">
        <article class="card stat"><div class="stat-label">รอดำเนินการ</div><div class="stat-value">{{ number_format($bookingStats['pending']) }}</div></article>
        <article class="card stat"><div class="stat-label">ยืนยันแล้ว</div><div class="stat-value">{{ number_format($bookingStats['confirmed']) }}</div></article>
        <article class="card stat"><div class="stat-label">เสร็จสิ้น</div><div class="stat-value">{{ number_format($bookingStats['completed']) }}</div></article>
    </section>

    <section class="card panel">
        <form class="filters" method="GET" action="{{ route('bookings.index') }}">
            <label class="search">ค้นหาลูกค้า สัตว์เลี้ยง หรือบริการ
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="พิมพ์คำที่ต้องการค้นหา">
            </label>
            <label>สถานะ
                <select name="status">
                    <option value="">ทุกสถานะ</option>
                    <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>รอดำเนินการ</option>
                    <option value="confirmed" @selected(($filters['status'] ?? '') === 'confirmed')>ยืนยันแล้ว</option>
                    <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>เสร็จสิ้น</option>
                </select>
            </label>
            <label>ตั้งแต่วันที่
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
            </label>
            <label>ถึงวันที่
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
            </label>
            <button class="button" type="submit">ค้นหา</button>
        </form>
        @if ($errors->any())
            <p class="error">กรุณาตรวจสอบเงื่อนไขการค้นหาอีกครั้ง</p>
        @endif
    </section>

    <section class="card panel" style="margin-top:16px">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>วันและเวลา</th><th>ลูกค้า</th><th>สัตว์เลี้ยง</th><th>บริการ</th><th>ราคา</th><th>สถานะ</th><th>จัดการสถานะ</th></tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td><div class="main-text">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</div><div class="sub-text">{{ substr($booking->booking_start_time, 0, 5) }}–{{ substr($booking->booking_end_time, 0, 5) }} น.</div></td>
                            <td><div class="main-text">{{ $booking->customer_name }}</div><div class="sub-text">{{ $booking->customer_phone }}</div></td>
                            <td><div class="main-text">{{ $booking->pet_name }}</div><div class="sub-text">{{ $booking->pet_breed_name }}</div></td>
                            <td>{{ $booking->service_name }}</td>
                            <td>{{ number_format((float) $booking->booking_total_price, 2) }} ฿</td>
                            <td><span class="status {{ $booking->booking_status }}">{{ ['pending' => 'รอดำเนินการ', 'confirmed' => 'ยืนยันแล้ว', 'completed' => 'เสร็จสิ้น'][$booking->booking_status] }}</span></td>
                            <td>
                                <form class="status-form" method="POST" action="{{ route('bookings.status', $booking->booking_id) }}">
                                    @csrf
                                    <select name="booking_status" aria-label="เปลี่ยนสถานะการจองหมายเลข {{ $booking->booking_id }}">
                                        <option value="pending" @selected($booking->booking_status === 'pending')>รอดำเนินการ</option>
                                        <option value="confirmed" @selected($booking->booking_status === 'confirmed')>ยืนยันแล้ว</option>
                                        <option value="completed" @selected($booking->booking_status === 'completed')>เสร็จสิ้น</option>
                                    </select>
                                    <button type="submit">บันทึก</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="empty" colspan="7">ไม่พบรายการจองตามเงื่อนไขที่เลือก</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $bookings->links() }}</div>
    </section>
</main>
</body>
</html>
