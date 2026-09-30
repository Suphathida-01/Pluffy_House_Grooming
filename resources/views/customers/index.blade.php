<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการลูกค้า | Pluffy House</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8f7fb; color: #242338; font-family: Arial, Tahoma, sans-serif; }
        a { color: inherit; text-decoration: none; }
        .page { width: min(1180px, 100%); margin: 0 auto; padding: 30px 24px 50px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
        .eyebrow { margin: 0 0 7px; color: #8955f5; font-size: 11px; font-weight: 700; letter-spacing: .08em; }
        h1 { margin: 0; font-size: 25px; }
        .subtitle { margin: 7px 0 0; color: #858394; font-size: 13px; }
        .button { display: inline-flex; min-height: 40px; align-items: center; justify-content: center; padding: 0 14px; border: 1px solid #e5dff0; border-radius: 9px; background: #fff; color: #514d61; font-size: 12px; font-weight: 700; }
        .cards { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
        .card { border: 1px solid #e9e4f1; border-radius: 12px; background: #fff; box-shadow: 0 3px 12px rgba(52, 35, 82, .035); }
        .stat { padding: 17px 19px; }
        .stat-label { color: #777585; font-size: 12px; }
        .stat-value { margin-top: 8px; font-size: 24px; font-weight: 700; }
        .panel { padding: 20px; }
        .search-form { display: flex; align-items: end; gap: 10px; }
        label { display: grid; flex: 1; gap: 7px; color: #666375; font-size: 12px; font-weight: 600; }
        input { width: 100%; min-height: 42px; padding: 0 11px; border: 1px solid #e5e1ec; border-radius: 8px; background: #fff; color: #29273b; font: inherit; font-size: 13px; }
        .primary { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; padding: 0 15px; border: 0; border-radius: 8px; background: #8955f5; color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 10px; border-bottom: 1px solid #ece8f2; color: #858394; font-size: 11px; font-weight: 600; white-space: nowrap; }
        td { padding: 14px 10px; border-bottom: 1px solid #f0edf4; color: #4d4a5d; font-size: 12px; vertical-align: middle; }
        .main-text { color: #28263a; font-weight: 700; }
        .sub-text { margin-top: 4px; color: #92909f; font-size: 11px; }
        .detail-link { color: #7041d0; font-weight: 700; white-space: nowrap; }
        .empty { padding: 38px 15px; color: #8b8998; text-align: center; font-size: 13px; }
        .pagination { margin-top: 18px; }
        @media (max-width: 650px) { .page { padding: 20px 14px 35px; } .topbar { align-items: flex-start; flex-direction: column; } .cards { gap: 8px; } .stat { padding: 13px 12px; } .stat-label { font-size: 10px; } .stat-value { font-size: 20px; } .panel { padding: 14px; } .search-form { align-items: stretch; flex-direction: column; } }
    </style>
</head>
<body>
<main class="page">
    <header class="topbar">
        <div>
            <p class="eyebrow">PLUFFY HOUSE · BACK-OFFICE</p>
            <h1>จัดการลูกค้า</h1>
            <p class="subtitle">ค้นหาข้อมูลติดต่อ ดูสัตว์เลี้ยง และตรวจสอบประวัติการจองของลูกค้า</p>
        </div>
        <a class="button" href="{{ route('admin.home') }}">กลับหน้าแรก</a>
    </header>

    <section class="cards" aria-label="สรุปข้อมูลลูกค้า">
        <article class="card stat"><div class="stat-label">ลูกค้าทั้งหมด</div><div class="stat-value">{{ number_format($customerCount) }}</div></article>
        <article class="card stat"><div class="stat-label">สัตว์เลี้ยงในระบบ</div><div class="stat-value">{{ number_format($petCount) }}</div></article>
        <article class="card stat"><div class="stat-label">รายการจองทั้งหมด</div><div class="stat-value">{{ number_format($bookingCount) }}</div></article>
    </section>

    <section class="card panel">
        <form class="search-form" method="GET" action="{{ route('customers.index') }}">
            <label>ค้นหาจากชื่อลูกค้า อีเมล หรือเบอร์โทรศัพท์
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="พิมพ์ข้อมูลที่ต้องการค้นหา">
            </label>
            <button class="primary" type="submit">ค้นหา</button>
        </form>
    </section>

    <section class="card panel" style="margin-top:16px">
        <div class="table-wrap">
            <table>
                <thead><tr><th>ลูกค้า</th><th>เบอร์โทรศัพท์</th><th>สัตว์เลี้ยง</th><th>ประวัติการจอง</th><th>สมัครเมื่อ</th><th></th></tr></thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td><div class="main-text">{{ $customer->user_name }}</div><div class="sub-text">{{ $customer->user_email }}</div></td>
                            <td>{{ $customer->user_phone }}</td>
                            <td>{{ number_format($customer->pet_count) }} ตัว</td>
                            <td>{{ number_format($customer->booking_count) }} ครั้ง</td>
                            <td>{{ \Carbon\Carbon::parse($customer->customer_since)->format('d/m/Y') }}</td>
                            <td><a class="detail-link" href="{{ route('customers.show', $customer->customer_id) }}">ดูรายละเอียด →</a></td>
                        </tr>
                    @empty
                        <tr><td class="empty" colspan="6">ไม่พบข้อมูลลูกค้าตามคำค้น</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $customers->links() }}</div>
    </section>
</main>
</body>
</html>
