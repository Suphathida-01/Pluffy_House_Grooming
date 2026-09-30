<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลลูกค้า | Pluffy House</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8f7fb; color: #242338; font-family: Arial, Tahoma, sans-serif; }
        a { color: inherit; text-decoration: none; }
        .page { width: min(1080px, 100%); margin: 0 auto; padding: 30px 24px 50px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
        .eyebrow { margin: 0 0 7px; color: #8955f5; font-size: 11px; font-weight: 700; letter-spacing: .08em; }
        h1 { margin: 0; font-size: 25px; }
        h2 { margin: 0; font-size: 15px; }
        .subtitle { margin: 7px 0 0; color: #858394; font-size: 13px; }
        .button { display: inline-flex; min-height: 40px; align-items: center; justify-content: center; padding: 0 14px; border: 1px solid #e5dff0; border-radius: 9px; background: #fff; color: #514d61; font-size: 12px; font-weight: 700; }
        .primary { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; padding: 0 15px; border: 0; border-radius: 8px; background: #8955f5; color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; }
        .card { margin-bottom: 16px; padding: 20px; border: 1px solid #e9e4f1; border-radius: 12px; background: #fff; box-shadow: 0 3px 12px rgba(52, 35, 82, .035); }
        .section-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
        .section-note { margin: 5px 0 0; color: #858394; font-size: 12px; }
        .form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 13px; }
        label { display: grid; gap: 7px; color: #666375; font-size: 12px; font-weight: 600; }
        input { width: 100%; min-height: 42px; padding: 0 11px; border: 1px solid #e5e1ec; border-radius: 8px; background: #fff; color: #29273b; font: inherit; font-size: 13px; }
        .flash { margin-bottom: 14px; padding: 12px 14px; border: 1px solid #bfe8d3; border-radius: 9px; background: #effaf4; color: #216c4e; font-size: 13px; }
        .error { color: #bd4050; font-size: 11px; font-weight: 400; }
        .actions { display: flex; justify-content: flex-end; margin-top: 15px; }
        .pet-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .pet-card { padding: 15px; border: 1px solid #eee9f5; border-radius: 10px; background: #fcfbfe; }
        .pet-name { font-size: 14px; font-weight: 700; }
        .pet-detail { margin-top: 7px; color: #777585; font-size: 12px; line-height: 1.65; }
        .pet-note { margin-top: 7px; color: #9694a2; font-size: 11px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 10px; border-bottom: 1px solid #ece8f2; color: #858394; font-size: 11px; font-weight: 600; white-space: nowrap; }
        td { padding: 13px 10px; border-bottom: 1px solid #f0edf4; color: #4d4a5d; font-size: 12px; vertical-align: middle; }
        .status { display: inline-flex; padding: 5px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; white-space: nowrap; }
        .status.pending { background: #fff4dd; color: #a96900; }
        .status.confirmed { background: #fcebed; color: #b24456; }
        .status.completed { background: #e5f6ee; color: #21805e; }
        .empty { padding: 28px 12px; color: #8b8998; text-align: center; font-size: 13px; }
        @media (max-width: 760px) { .page { padding: 20px 14px 35px; } .topbar { align-items: flex-start; flex-direction: column; } .form-grid { grid-template-columns: 1fr; } .pet-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .card { padding: 15px; } }
        @media (max-width: 460px) { .pet-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<main class="page">
    <header class="topbar">
        <div>
            <p class="eyebrow">PLUFFY HOUSE · BACK-OFFICE</p>
            <h1>{{ $customer->user_name }}</h1>
            <p class="subtitle">ลูกค้าตั้งแต่ {{ \Carbon\Carbon::parse($customer->customer_since)->format('d/m/Y') }}</p>
        </div>
        <a class="button" href="{{ route('customers.index') }}">กลับรายชื่อลูกค้า</a>
    </header>

    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    <section class="card">
        <div class="section-heading"><div><h2>ข้อมูลติดต่อ</h2><p class="section-note">แก้ไขชื่อ อีเมล และเบอร์โทรศัพท์ของลูกค้า</p></div></div>
        <form method="POST" action="{{ route('customers.update', $customer->customer_id) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <label>ชื่อลูกค้า
                    <input type="text" name="user_name" value="{{ old('user_name', $customer->user_name) }}" required maxlength="255">
                    @error('user_name')<span class="error">{{ $message }}</span>@enderror
                </label>
                <label>อีเมล
                    <input type="email" name="user_email" value="{{ old('user_email', $customer->user_email) }}" required maxlength="255">
                    @error('user_email')<span class="error">{{ $message }}</span>@enderror
                </label>
                <label>เบอร์โทรศัพท์
                    <input type="text" name="user_phone" value="{{ old('user_phone', $customer->user_phone) }}" required maxlength="30">
                    @error('user_phone')<span class="error">{{ $message }}</span>@enderror
                </label>
            </div>
            <div class="actions"><button class="primary" type="submit">บันทึกข้อมูล</button></div>
        </form>
    </section>

    <section class="card">
        <div class="section-heading"><div><h2>สัตว์เลี้ยงของลูกค้า</h2><p class="section-note">แสดงข้อมูลจากทะเบียนสัตว์เลี้ยงในระบบ</p></div></div>
        @if ($pets->isEmpty())
            <p class="empty">ยังไม่มีข้อมูลสัตว์เลี้ยง</p>
        @else
            <div class="pet-grid">
                @foreach ($pets as $pet)
                    <article class="pet-card">
                        <div class="pet-name">{{ $pet->pet_name }}</div>
                        <div class="pet-detail">
                            {{ $pet->pet_type_name }} · {{ $pet->pet_breed_name }}<br>
                            {{ $pet->pet_gender === 'male' ? 'เพศผู้' : 'เพศเมีย' }} · {{ number_format((float) $pet->pet_weight, 2) }} กก.<br>
                            เกิด {{ \Carbon\Carbon::parse($pet->pet_birth)->format('d/m/Y') }}
                        </div>
                        @if ($pet->pet_notes)<div class="pet-note">หมายเหตุ: {{ $pet->pet_notes }}</div>@endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="card">
        <div class="section-heading"><div><h2>ประวัติการจอง</h2><p class="section-note">แสดง 20 รายการล่าสุด</p></div></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>วันที่และเวลา</th><th>สัตว์เลี้ยง</th><th>บริการ</th><th>ยอดรวม</th><th>สถานะ</th></tr></thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }} · {{ substr($booking->booking_start_time, 0, 5) }} น.</td>
                            <td>{{ $booking->pet_name }}</td>
                            <td>{{ $booking->service_name }}</td>
                            <td>{{ number_format((float) $booking->booking_total_price, 2) }} ฿</td>
                            <td><span class="status {{ $booking->booking_status }}">{{ ['pending' => 'รอดำเนินการ', 'confirmed' => 'ยืนยันแล้ว', 'completed' => 'เสร็จสิ้น'][$booking->booking_status] }}</span></td>
                        </tr>
                    @empty
                        <tr><td class="empty" colspan="5">ลูกค้ารายนี้ยังไม่มีประวัติการจอง</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
