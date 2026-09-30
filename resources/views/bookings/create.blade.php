<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างการจอง | Pluffy House</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8f7fb; color: #242338; font-family: Arial, Tahoma, sans-serif; }
        a { color: inherit; text-decoration: none; }
        .page { width: min(980px, 100%); margin: 0 auto; padding: 30px 24px 50px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
        .eyebrow { margin: 0 0 7px; color: #8955f5; font-size: 11px; font-weight: 700; letter-spacing: .08em; }
        h1 { margin: 0; font-size: 25px; }
        h2 { margin: 0; font-size: 15px; }
        .subtitle { margin: 7px 0 0; color: #858394; font-size: 13px; }
        .button { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; padding: 0 16px; border: 0; border-radius: 9px; background: #8955f5; color: #fff; font-size: 13px; font-weight: 700; cursor: pointer; }
        .button.secondary { border: 1px solid #e5dff0; background: #fff; color: #514d61; }
        .button:disabled { opacity: .5; cursor: not-allowed; }
        .card { margin-bottom: 16px; padding: 20px; border: 1px solid #e9e4f1; border-radius: 12px; background: #fff; box-shadow: 0 3px 12px rgba(52, 35, 82, .035); }
        .section-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
        .section-note { margin: 5px 0 0; color: #858394; font-size: 12px; }
        .filters { display: grid; grid-template-columns: 1.2fr 1.2fr 1fr auto; align-items: end; gap: 11px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; }
        label { display: grid; gap: 7px; color: #666375; font-size: 12px; font-weight: 600; }
        input, select, textarea { width: 100%; min-height: 42px; padding: 9px 11px; border: 1px solid #e5e1ec; border-radius: 8px; background: #fff; color: #29273b; font: inherit; font-size: 13px; }
        textarea { min-height: 74px; resize: vertical; }
        .wide { grid-column: 1 / -1; }
        .service-summary { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 15px; border: 1px solid #e7ddfa; border-radius: 10px; background: #fbf9ff; }
        .service-name { color: #33274c; font-weight: 700; }
        .service-detail { margin-top: 4px; color: #898596; font-size: 11px; }
        .price { color: #7041d0; font-size: 17px; font-weight: 700; white-space: nowrap; }
        .flash { margin-bottom: 14px; padding: 12px 14px; border: 1px solid #bfe8d3; border-radius: 9px; background: #effaf4; color: #216c4e; font-size: 13px; }
        .error { color: #bd4050; font-size: 11px; font-weight: 400; }
        .empty { padding: 13px; border: 1px dashed #e3dcef; border-radius: 9px; color: #8b8998; font-size: 12px; line-height: 1.6; }
        .actions { display: flex; justify-content: flex-end; gap: 9px; margin-top: 18px; }
        @media (max-width: 720px) { .page { padding: 20px 14px 35px; } .topbar { align-items: flex-start; flex-direction: column; } .filters { grid-template-columns: 1fr 1fr; } .form-grid { grid-template-columns: 1fr; } .wide { grid-column: auto; } .card { padding: 15px; } }
        @media (max-width: 440px) { .filters { grid-template-columns: 1fr; } .service-summary { align-items: flex-start; flex-direction: column; } }
    </style>
</head>
<body>
<main class="page">
    <header class="topbar">
        <div>
            <p class="eyebrow">PLUFFY HOUSE · BACK-OFFICE</p>
            <h1>สร้างการจอง</h1>
            <p class="subtitle">เลือกผู้จอง บริการ และวัน เพื่อดูเวลาว่างจากตารางช่าง</p>
        </div>
        <a class="button secondary" href="{{ route('bookings.index') }}">กลับรายการจอง</a>
    </header>

    @if ($errors->any())
        <div class="flash" style="border-color:#f1c8ce;background:#fff3f4;color:#a83445">
            กรุณาตรวจสอบข้อมูลที่กรอกและเลือกเวลาใหม่อีกครั้ง
        </div>
    @endif

    <section class="card">
        <div class="section-heading">
            <div><h2>1. เลือกผู้จอง บริการ และวัน</h2><p class="section-note">กด “ดูเวลาว่าง” เพื่อโหลดช่วงเวลาที่ไม่ชนกับคิวอื่น</p></div>
        </div>
        <form class="filters" method="GET" action="{{ route('bookings.create') }}">
            <label>ลูกค้า
                <select name="customer_id" required>
                    <option value="">เลือกลูกค้า</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->customer_id }}" @selected((string) $selectedCustomerId === (string) $customer->customer_id)>
                            {{ $customer->user_name }} · {{ $customer->user_phone }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label>บริการ
                <select name="service_id" required>
                    @foreach ($services as $service)
                        <option value="{{ $service->service_id }}" @selected((int) $selectedServiceId === (int) $service->service_id)>
                            {{ $service->service_name }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label>วันที่ต้องการจอง
                <input type="date" name="booking_date" min="{{ today()->toDateString() }}" value="{{ $selectedDate }}" required>
            </label>
            <button class="button" type="submit">ดูเวลาว่าง</button>
        </form>
        @if ($services->isEmpty())
            <p class="empty" style="margin-top:14px">ยังไม่มีบริการที่เปิดให้จอง กรุณาเพิ่มหรือเปิดใช้งานบริการก่อน</p>
        @endif
    </section>

    <form method="POST" action="{{ route('bookings.store') }}">
        @csrf
        <input type="hidden" name="customer_id" value="{{ old('customer_id', $selectedCustomerId) }}">
        <input type="hidden" name="service_id" value="{{ old('service_id', $selectedServiceId) }}">
        <input type="hidden" name="booking_date" value="{{ old('booking_date', $selectedDate) }}">

        @if ($selectedService)
            <section class="card">
                <div class="section-heading"><div><h2>2. ตรวจสอบบริการ</h2><p class="section-note">ราคาและระยะเวลาคำนวณจากข้อมูลบริการในระบบ</p></div></div>
                <div class="service-summary">
                    <div><div class="service-name">{{ $selectedService->service_name }}</div><div class="service-detail">ใช้เวลาประมาณ {{ number_format($selectedService->service_duration_minutes) }} นาที · วันที่ {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</div></div>
                    <div class="price">{{ number_format((float) $selectedService->service_price, 2) }} ฿</div>
                </div>
            </section>
        @endif

        <section class="card">
            <div class="section-heading"><div><h2>3. เลือกสัตว์เลี้ยงและเวลานัด</h2><p class="section-note">ระบบจะแสดงเฉพาะสัตว์เลี้ยงของลูกค้าที่เลือกและเวลาที่ยังว่าง</p></div></div>
            <div class="form-grid">
                <label>สัตว์เลี้ยง
                    <select name="pet_id" required {{ $pets->isEmpty() ? 'disabled' : '' }}>
                        <option value="">เลือกสัตว์เลี้ยง</option>
                        @foreach ($pets as $pet)
                            <option value="{{ $pet->id }}" @selected((string) old('pet_id') === (string) $pet->id)>
                                {{ $pet->pet_name }} · {{ $pet->pet_type_name }} ({{ $pet->pet_breed_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('pet_id')<span class="error">{{ $message }}</span>@enderror
                </label>
                <label>เวลานัด
                    <select name="schedule_slot" required {{ empty($availableSlots) ? 'disabled' : '' }}>
                        <option value="">เลือกเวลาที่ว่าง</option>
                        @foreach ($availableSlots as $slot)
                            <option value="{{ $slot['value'] }}" @selected(old('schedule_slot') === $slot['value'])>{{ $slot['label'] }}</option>
                        @endforeach
                    </select>
                    @error('schedule_slot')<span class="error">{{ $message }}</span>@enderror
                </label>
            </div>

            @if (!$selectedCustomerId)
                <p class="empty" style="margin-top:14px">เลือกลูกค้าด้านบนก่อน ระบบจึงจะแสดงรายชื่อสัตว์เลี้ยงของลูกค้าคนนั้น</p>
            @elseif ($pets->isEmpty())
                <p class="empty" style="margin-top:14px">ลูกค้ารายนี้ยังไม่มีข้อมูลสัตว์เลี้ยง กรุณาเพิ่มข้อมูลสัตว์เลี้ยงก่อนสร้างการจอง</p>
            @endif
            @if ($selectedService && empty($availableSlots))
                <p class="empty" style="margin-top:14px">ไม่มีเวลาว่างในวันที่เลือก หรือยังไม่มีตารางเวลาทำงานของช่างในวันนี้ กรุณาเลือกวันอื่นหรือตรวจสอบตารางช่าง</p>
            @endif

            <div class="actions">
                <a class="button secondary" href="{{ route('bookings.index') }}">ยกเลิก</a>
                <button class="button" type="submit" @disabled(!$selectedCustomerId || $pets->isEmpty() || empty($availableSlots))>ยืนยันการจอง</button>
            </div>
        </section>
    </form>
</main>
</body>
</html>
