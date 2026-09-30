@extends('layouts.admin')

@section('title', 'จัดการช่าง')

@section('content')
<div class="staff-page">
    <header class="page-header">
        <div class="page-title">
            <h1>จัดการช่าง</h1>
            <p>จัดการวันเปิดร้าน กะช่าง และคิวจอง</p>
        </div>
        <div class="staff-actions">
            <button class="staff-button staff-button-primary" type="button" data-schedule-open data-status="available">กำหนดช่วงเวลางาน</button>
        </div>
    </header>

    <section class="staff-panel month-panel" aria-labelledby="month-title">
        <div class="month-panel-heading">
            <h2 id="month-title">ตารางร้านประจำเดือน</h2>
            <nav class="month-navigation" aria-label="เปลี่ยนเดือน">
                <a href="{{ route('staff', ['month' => $previousMonth, 'date' => $previousMonth . '-01']) }}" aria-label="เดือนก่อน">‹</a>
                <strong>{{ \Illuminate\Support\Carbon::parse($selectedMonth . '-01')->locale('th')->translatedFormat('F Y') }}</strong>
                <a href="{{ route('staff', ['month' => $nextMonth, 'date' => $nextMonth . '-01']) }}" aria-label="เดือนถัดไป">›</a>
            </nav>
            <div class="month-legend" aria-label="สถานะวัน">
                <span><i class="legend-dot legend-open"></i>มีช่างเข้ากะ</span>
                <span><i class="legend-dot legend-unscheduled"></i>ยังไม่จัดกะ</span>
                <span><i class="legend-dot legend-closed"></i>ปิดร้าน</span>
            </div>
        </div>

        <div class="month-weekdays" aria-hidden="true">
            @foreach (['จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.', 'อา.'] as $weekday)
                <span>{{ $weekday }}</span>
            @endforeach
        </div>

        <div class="month-grid">
            @foreach ($calendarDays as $day)
                <article class="month-day {{ $day['is_current_month'] ? '' : 'is-outside-month' }} {{ $day['is_selected'] ? 'is-selected' : '' }} {{ $day['is_today'] ? 'is-today' : '' }} {{ $day['is_closed'] ? 'day-closed' : ($day['working_staff_count'] ? 'day-open' : 'day-unscheduled') }}">
                    <a class="month-day-link" href="{{ route('staff', ['month' => $selectedMonth, 'date' => $day['date']]) }}">
                        <span class="month-day-number">{{ $day['day'] }}</span>
                        @if ($day['is_closed'])
                            <span class="month-day-state">ปิดร้าน</span>
                        @elseif ($day['working_staff_count'] > 0)
                            <span class="month-day-state">ช่าง {{ $day['working_staff_count'] }} คน</span>
                        @else
                            <span class="month-day-state">ยังไม่จัดกะ</span>
                        @endif
                        @if ($day['booking_count'] > 0)
                            <small>{{ $day['booking_count'] }} คิว</small>
                        @endif
                    </a>
                    @if ($day['is_current_month'])
                        <form method="POST" action="{{ route('toggleStoreClosure') }}" class="month-day-toggle">
                            @csrf
                            <input type="hidden" name="work_date" value="{{ $day['date'] }}">
                            <input type="hidden" name="closed" value="{{ $day['is_closed'] ? '0' : '1' }}">
                            <button type="submit" aria-label="{{ $day['is_closed'] ? 'เปิดร้าน' : 'ปิดร้าน' }}วันที่ {{ $day['day'] }}">
                                {{ $day['is_closed'] ? 'เปิดร้าน' : 'ปิดร้าน' }}
                            </button>
                        </form>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="selected-day-section" aria-labelledby="selected-day-title">
        <header class="selected-day-heading">
            <div>
                <h2 id="selected-day-title">ตารางรายวัน</h2>
                <p>{{ \Illuminate\Support\Carbon::parse($selectedDate)->format('d/m/Y') }}</p>
            </div>
            <button class="staff-button staff-button-light" type="button" data-staff-create-open>+ เพิ่มช่าง</button>
        </header>

        <div class="day-detail-grid">
            <section class="staff-panel day-staff-panel" aria-labelledby="day-staff-title">
                <div class="panel-heading">
                    <div><h3 id="day-staff-title">ช่างที่เข้าวันนี้</h3><p>ดึงจากตารางกะของช่าง</p></div>
                    <button class="staff-button staff-button-primary" type="button" data-schedule-open data-status="available">กำหนดช่วงเวลางาน</button>
                </div>
                <div class="technician-list">
                    @forelse ($dailyStaff as $staff)
                        <article class="technician-card">
                            <span class="technician-avatar">{{ $staff['initial'] }}</span>
                            <div class="technician-info">
                                <strong>{{ $staff['name'] }}</strong>
                                <small>
                                    @foreach ($staff['shifts'] as $shift)
                                        {{ substr($shift->start_time, 0, 5) }}-{{ substr($shift->end_time, 0, 5) }}@if (! $loop->last), @endif
                                    @endforeach
                                    · {{ $staff['booking_count'] }} คิว
                                </small>
                            </div>
                            <span class="technician-status status-free">เข้างาน</span>
                        </article>
                    @empty
                        <p class="staff-empty">ยังไม่มีช่างลงกะในวันนี้</p>
                    @endforelse
                </div>
            </section>

            <section class="staff-panel day-bookings-panel" aria-labelledby="day-bookings-title">
                <div class="panel-heading">
                    <div><h3 id="day-bookings-title">คิวจองของวันนี้</h3><p>ช่างแสดงตามผู้รับงานในรายการจอง</p></div>
                    <span class="result-count">{{ $dailyBookings->count() }} คิว</span>
                </div>
                <div class="day-bookings-list">
                    @forelse ($dailyBookings as $booking)
                        <article class="day-booking">
                            <time>{{ substr($booking->booking_start_time, 0, 5) }}-{{ substr($booking->booking_end_time, 0, 5) }}</time>
                            <div>
                                <strong>{{ $booking->pet?->pet_name ?? 'ไม่พบข้อมูลสัตว์เลี้ยง' }}</strong>
                                <small>{{ $booking->service?->service_name ?? 'ไม่พบข้อมูลบริการ' }}</small>
                            </div>
                            <span>{{ $booking->admin?->user?->user_name ?? 'ยังไม่ระบุช่าง' }}</span>
                        </article>
                    @empty
                        <p class="staff-empty">วันนี้ยังไม่มีคิวจอง</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
</div>

<dialog class="schedule-dialog" id="schedule-dialog" aria-labelledby="schedule-dialog-title">
    <form method="POST" action="{{ route('storeSchedule') }}">
        @csrf
        <div class="schedule-dialog-heading">
            <div><h2 id="schedule-dialog-title">กำหนดตารางงานช่าง</h2><p>เพิ่มช่วงเวลาที่ช่างเข้าทำงาน</p></div>
            <button class="schedule-close" type="button" aria-label="ปิด">×</button>
        </div>
        <label class="schedule-field">ช่าง
            <select name="admin_id" required>
                @foreach ($staffOptions as $staff)
                    <option value="{{ $staff['id'] }}">{{ $staff['name'] }}</option>
                @endforeach
            </select>
        </label>
        <label class="schedule-field">วันที่
            <input type="date" name="work_date" value="{{ $selectedDate }}" required>
        </label>
        <div class="schedule-time-fields">
            <label class="schedule-field">เริ่มงาน<input type="time" name="start_time" value="09:00" required></label>
            <label class="schedule-field">เลิกงาน<input type="time" name="end_time" value="17:00" required></label>
        </div>
        <label class="schedule-field">สถานะ
            <select name="status" id="schedule-status" required>
                <option value="available">เข้างาน</option>
                <option value="unavailable">ปิดทำการ</option>
            </select>
        </label>
        <div class="schedule-dialog-actions">
            <button class="staff-button staff-button-light schedule-close" type="button">ยกเลิก</button>
            <button class="staff-button staff-button-primary" type="submit">บันทึกตาราง</button>
        </div>
    </form>
</dialog>

<dialog class="schedule-dialog" id="staff-dialog" aria-labelledby="staff-dialog-title">
    <form method="POST" action="{{ route('storeStaff') }}">
        @csrf
        <div class="schedule-dialog-heading">
            <div><h2 id="staff-dialog-title">เพิ่มช่าง</h2><p>สร้างบัญชีผู้ใช้และเพิ่มในรายชื่อช่าง</p></div>
            <button class="staff-dialog-close schedule-close" type="button" aria-label="ปิด">×</button>
        </div>
        <label class="schedule-field">ชื่อช่าง<input type="text" name="user_name" maxlength="255" required autocomplete="name"></label>
        <label class="schedule-field">อีเมล<input type="email" name="user_email" maxlength="255" required autocomplete="email"></label>
        <label class="schedule-field">เบอร์โทรศัพท์<input type="tel" name="user_phone" maxlength="20" required autocomplete="tel"></label>
        <label class="schedule-field">รหัสผ่าน<input type="password" name="user_password" minlength="8" required autocomplete="new-password"></label>
        <div class="schedule-dialog-actions">
            <button class="staff-button staff-button-light staff-dialog-close" type="button">ยกเลิก</button>
            <button class="staff-button staff-button-primary" type="submit">เพิ่มช่าง</button>
        </div>
    </form>
</dialog>

<script>
    const scheduleDialog = document.getElementById('schedule-dialog');
    const scheduleStatus = document.getElementById('schedule-status');
    const staffDialog = document.getElementById('staff-dialog');

    document.querySelectorAll('[data-schedule-open]').forEach((button) => {
        button.addEventListener('click', () => {
            scheduleStatus.value = button.dataset.status;
            scheduleDialog.showModal();
        });
    });

    document.querySelectorAll('.schedule-close').forEach((button) => {
        button.addEventListener('click', () => scheduleDialog.close());
    });

    document.querySelector('[data-staff-create-open]').addEventListener('click', () => staffDialog.showModal());
    document.querySelectorAll('.staff-dialog-close').forEach((button) => {
        button.addEventListener('click', () => staffDialog.close());
    });

    scheduleDialog.addEventListener('click', (event) => {
        if (event.target === scheduleDialog) scheduleDialog.close();
    });
</script>
@endsection