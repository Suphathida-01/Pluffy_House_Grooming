<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จองบริการ | Pluffy House Grooming</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}?v={{ filemtime(public_path('css/booking.css')) }}">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark">🐾</span>
            <span class="brand-name">Pluffy House <span>Grooming</span></span>
        </a>
        <nav class="nav">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('home') }}">บริการ</a>
            <a class="active" href="{{ route('booking.create', $serviceSlug) }}">จองคิว</a>
            <a href="{{ route('bookings.index') }}">การจองของฉัน</a>
        </nav>
        <div class="account">
            <a class="login" href="{{ route('login') }}">เข้าสู่ระบบ</a>
            <a class="book-top" href="{{ route('booking.create', $serviceSlug) }}">จองคิวเลย ▣</a>
        </div>
    </header>

    <main class="booking-page" data-initial-step="{{ $selectedStep }}" data-initial-date="{{ $selectedDate->toDateString() }}" data-initial-time="{{ $selectedTime }}" data-date-time-url="{{ route('booking.datetime', $serviceSlug) }}" data-pet-url="{{ route('booking.create', $serviceSlug) }}" data-base-price="{{ (int) $service->service_price }}">
        <section class="steps" aria-label="ขั้นตอนการจอง">
            <button class="step{{ $selectedStep === 1 ? ' active' : '' }}{{ $selectedStep > 1 ? ' done' : '' }}" type="button" data-step-target="1"><span>1</span><strong>สัตว์เลี้ยง</strong></button>
            <i></i>
            <button class="step{{ $selectedStep === 2 ? ' active' : '' }}{{ $selectedStep > 2 ? ' done' : '' }}" type="button" data-step-target="2"><span>2</span><strong>วันเวลา</strong></button>
            <i></i>
            <button class="step" type="button" data-step-target="3"><span>3</span><strong>ยืนยัน</strong></button>
        </section>

        @if (session('status') || $errors->any())
            <div class="form-message{{ $errors->any() ? ' error' : '' }}">
                @if ($errors->any())
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                @else
                    {{ session('status') }}
                @endif
            </div>
        @endif

        <div class="booking-layout">
            <div class="booking-main">
                <section class="panel pets-panel" id="pet-panel" {{ $selectedStep !== 1 ? 'hidden' : '' }}>
                    <h1>เลือกสัตว์เลี้ยงของคุณ <span>🐾</span></h1>
                    <p class="demo-note">หน้านี้ใช้ข้อมูลตัวอย่างสำหรับจัดทำหน้าเว็บ ยังไม่ได้เชื่อมฐานข้อมูล</p>
                    <div class="pet-list">
                        @forelse ($pets as $pet)
                            @php
                                $isSelected = $selectedPetId
                                    ? (int) $selectedPetId === (int) $pet->id
                                    : $loop->first;
                            @endphp
                            <label class="pet-option{{ $isSelected ? ' selected' : '' }}" data-pet-name="{{ $pet->name }}" data-pet-breed="{{ $pet->breed }}" data-pet-type="{{ $pet->type }}" data-pet-weight="{{ $pet->weight }}">
                                <input type="radio" name="pet" value="{{ $pet->id }}" {{ $isSelected ? 'checked' : '' }}>
                                <span class="pet-avatar {{ $pet->type === 'แมว' ? 'cat' : 'dog' }}" aria-hidden="true">{{ $pet->type === 'แมว' ? '🐱' : '🐶' }}</span>
                                <span class="pet-info">
                                    <strong>{{ $pet->name }}</strong>
                                    <small>{{ $pet->type }} · {{ $pet->breed }} · {{ $pet->age }} · น้ำหนัก {{ $pet->weight }} กก.</small>
                                </span>
                                <span class="radio-mark" aria-hidden="true">✓</span>
                            </label>
                        @empty
                            <p class="empty-pets">ยังไม่มีข้อมูลสัตว์เลี้ยงในบัญชี เพิ่มข้อมูลสัตว์เลี้ยงใหม่ด้านล่างได้เลยค่ะ</p>
                        @endforelse
                    </div>
                </section>

                <section class="panel add-pet-panel" id="add-pet-panel" {{ $selectedStep !== 1 ? 'hidden' : '' }}>
                    <h2>＋ ลงทะเบียนสัตว์เลี้ยงใหม่</h2>
                    <form id="add-pet-form">
                    <div class="pet-type-label">ประเภทสัตว์เลี้ยง</div>
                    <div class="type-options">
                        <label class="type-option{{ old('pet_type', 'สุนัข') === 'สุนัข' ? ' chosen' : '' }}"><input type="radio" name="pet_type" value="สุนัข" {{ old('pet_type', 'สุนัข') === 'สุนัข' ? 'checked' : '' }} required> สุนัข 🐶</label>
                        <label class="type-option{{ old('pet_type') === 'แมว' ? ' chosen' : '' }}"><input type="radio" name="pet_type" value="แมว" {{ old('pet_type') === 'แมว' ? 'checked' : '' }}> แมว 🐱</label>
                    </div>
                    <div class="pet-fields">
                        <label>ชื่อสัตว์เลี้ยง<input id="pet-name" name="pet_name" type="text" value="{{ old('pet_name') }}" placeholder="เช่น น้องบู้เบ๊บ" maxlength="100" required></label>
                        <label>สายพันธุ์<input id="pet-breed" name="pet_breed" type="text" value="{{ old('pet_breed') }}" placeholder="เช่น ปอมเมอเรเนียน" maxlength="100" required></label>
                        <label>อายุ (ปี)<input id="pet-age" name="pet_age" type="number" value="{{ old('pet_age') }}" min="0" max="40" step="1" placeholder="เช่น 2" required></label>
                        <label>น้ำหนัก (กก.)<input id="pet-weight" name="pet_weight" type="number" value="{{ old('pet_weight') }}" min="0.1" max="200" step="0.1" placeholder="เช่น 4.5" required></label>
                        <label class="gender-field">เพศ
                            <select name="pet_gender" required>
                                <option value="" {{ old('pet_gender') ? '' : 'selected' }} disabled>เลือกเพศ</option>
                                <option value="male" {{ old('pet_gender') === 'male' ? 'selected' : '' }}>ผู้</option>
                                <option value="female" {{ old('pet_gender') === 'female' ? 'selected' : '' }}>เมีย</option>
                            </select>
                        </label>
                    </div>
                    <button class="save-pet-button" type="submit" id="save-pet">เพิ่มและเลือกสัตว์เลี้ยง (ตัวอย่าง) 🐾</button>
                    <p class="demo-note" id="demo-confirm-message">ข้อมูลสัตว์เลี้ยงใหม่จะอยู่ในหน้านี้ชั่วคราว ยังไม่ได้บันทึก</p>
                    </form>
                </section>

                <section class="panel date-panel" id="date-panel" {{ $selectedStep !== 2 ? 'hidden' : '' }}>
                    <div class="calendar-heading">
                        <h1>เลือกวันที่ต้องการจองคิว 🗓️</h1>
                        <div class="month-switch">
                            <button type="button" id="previous-month" aria-label="เดือนก่อนหน้า">‹</button>
                            <strong id="calendar-month">{{ $calendarMonthLabel }}</strong>
                            <button type="button" id="next-month" aria-label="เดือนถัดไป">›</button>
                        </div>
                    </div>
                    <div class="calendar-weekdays" aria-hidden="true">
                        <span>อา.</span><span>จ.</span><span>อ.</span><span>พ.</span><span>พฤ.</span><span>ศ.</span><span>ส.</span>
                    </div>
                    <div class="calendar-days" id="calendar-days" aria-label="เลือกวันที่">
                        @foreach ($calendarDates as $date)
                            <button type="button" class="calendar-day{{ $date->outside_month ? ' outside-month' : '' }}{{ $date->past ? ' past-date' : '' }}{{ $date->today ? ' today' : '' }}{{ $date->selected ? ' selected' : '' }}" data-date="{{ $date->value }}" aria-pressed="{{ $date->selected ? 'true' : 'false' }}" aria-label="{{ $date->value }}" {{ ($date->outside_month || $date->past) ? 'disabled' : '' }}>{{ $date->day }}</button>
                        @endforeach
                    </div>
                    <p class="calendar-note">ช่วงเวลาในหน้านี้เป็นข้อมูลตัวอย่าง ยังไม่ได้ตรวจสอบเวลาว่างจริง</p>
                </section>

                <section class="panel time-panel" id="time-panel" {{ $selectedStep !== 2 ? 'hidden' : '' }}>
                    <h1>เลือกเวลาให้บริการที่สะดวก ⏱️</h1>
                    <p class="availability-message" id="availability-message">เลือกวันที่ก่อน แล้วเลือกเวลาที่ต้องการ</p>
                    <div class="time-options" id="time-options">
                        @foreach ($sampleTimeSlots as $slot)
                            <button type="button" class="time-option{{ $selectedTime === $slot['value'] ? ' chosen' : '' }}" data-time="{{ $slot['value'] }}" {{ $slot['available'] ? '' : 'disabled' }}>{{ (int) substr($slot['value'], 0, 2) }}:00 น.{{ $slot['available'] ? '' : ' (เต็มแล้ว)' }}</button>
                        @endforeach
                    </div>
                </section>

                <section class="panel confirm-panel" id="confirm-panel" hidden>
                    <h1>ตรวจสอบความถูกต้องของการจองคิว ✨</h1>
                    <div class="confirm-pet-band">
                        <span class="pet-avatar dog" id="confirm-pet-avatar" aria-hidden="true">🐶</span>
                        <div><strong id="confirm-pet-name">ยังไม่ได้เลือกสัตว์เลี้ยง</strong><span id="confirm-pet-service"></span></div>
                    </div>
                    <div class="confirm-info-grid">
                        <div><small>🗓️ วันเวลาที่นัดหมาย</small><strong id="confirm-date">ยังไม่ได้เลือกวันเวลา</strong><b id="confirm-time"></b></div>
                        <div><small>✂️ บริการที่เลือก</small><strong id="confirm-service">{{ $service->service_name }}</strong><span>ระยะเวลา {{ $service->duration_label }}</span></div>
                    </div>
                    <form id="contact-form" class="contact-form">
                        <h2>ข้อมูลผู้ติดต่อ &amp; รายละเอียดเพิ่มเติม 📞</h2>
                        <div class="contact-fields">
                            <label>ชื่อ-นามสกุล เจ้าของ<input id="owner-name" name="owner_name" type="text" placeholder="กรอกชื่อ-นามสกุล" maxlength="120" required></label>
                            <label>เบอร์โทรศัพท์ติดต่อ<input id="owner-phone" name="owner_phone" type="tel" placeholder="เช่น 089-123-4567" maxlength="20" pattern="[0-9+() -]{9,20}" required></label>
                        </div>
                        <label class="note-field">ความต้องการเพิ่มเติม / หมายเหตุถึงสไตลิสต์<textarea id="owner-note" name="owner_note" rows="3" maxlength="500" placeholder="แจ้งรายละเอียดที่อยากให้ทางร้านทราบ (ไม่บังคับ)"></textarea></label>
                        <label class="terms-check"><input id="booking-terms" type="checkbox" required><span>ฉันยอมรับเงื่อนไขการให้บริการ และนโยบายความเป็นส่วนตัวของ Pluffy House Grooming</span></label>
                    </form>
                </section>

                <section class="panel payment-panel" id="payment-panel" hidden>
                    <div class="payment-heading">
                        <div><h1>ชำระเงินค่าบริการ</h1><p>โปรดตรวจสอบรายละเอียดการจองและเลือกช่องทางชำระเงิน</p></div>
                        <div class="booking-code"><small>หมายเลขจอง (ตัวอย่าง)</small><strong id="payment-booking-id">PH-BOOKING</strong></div>
                    </div>
                    <div class="payment-layout">
                        <section class="payment-card">
                            <h2>สรุปรายละเอียดการจอง</h2>
                            <div class="payment-pet"><span id="payment-pet-avatar">🐶</span><div><small>สัตว์เลี้ยง</small><strong id="payment-pet-name"></strong></div></div>
                            <div class="payment-summary-grid">
                                <div><small>บริการที่จอง</small><strong id="payment-service"></strong></div>
                                <div><small>วันที่รับบริการ</small><strong id="payment-date"></strong></div>
                                <div><small>เวลานัดหมาย</small><strong id="payment-time"></strong></div>
                            </div>
                            <div class="payment-total"><strong>ยอดชำระทั้งหมด</strong><b id="payment-total"></b></div>
                        </section>
                        <section class="payment-card">
                            <h2>เลือกช่องทางการชำระเงิน</h2>
                            <div class="payment-methods">
                                <label class="payment-method selected" data-payment-choice="promptpay"><input type="radio" name="payment_method" value="promptpay" checked><span class="method-radio"></span><strong>QR Code พร้อมเพย์</strong><small>สแกนเพื่อชำระเงิน</small></label>
                                <div class="payment-extra" id="promptpay-extra">
                                    <div class="demo-qr" role="img" aria-label="QR ตัวอย่าง ไม่สามารถใช้ชำระเงินจริง">
                                        <svg viewBox="0 0 210 210" aria-hidden="true">
                                            <rect width="210" height="210" fill="#fff"/>
                                            <path fill="#222" d="M10 10h60v60H10zM20 20v40h40V20zM30 30h20v20H30zM140 10h60v60h-60zM150 20v40h40V20zM160 30h20v20h-20zM10 140h60v60H10zM20 150v40h40v-40zM30 160h20v20H30zM85 10h10v10H85zM105 10h10v10h-10zM85 30h20v10H85zM115 30h10v20h-10zM85 55h10v15H85zM105 60h20v10h-20zM80 85h15v15H80zM105 85h10v10h-10zM125 80h15v15h-15zM150 85h10v20h-10zM175 85h20v10h-20zM90 110h20v10H90zM120 105h10v20h-10zM140 115h20v10h-20zM175 110h15v15h-15zM80 135h15v10H80zM105 140h10v20h-10zM125 140h20v10h-20zM155 140h15v15h-15zM185 140h15v10h-15zM85 165h10v20H85zM105 175h20v10h-20zM140 165h10v25h-10zM160 170h10v10h-10zM180 165h20v20h-20z"/>
                                        </svg>
                                        <span>ตัวอย่าง</span>
                                    </div>
                                    <p>QR นี้ใช้ประกอบการแสดงผลเท่านั้น<br>ยังไม่สามารถสแกนชำระเงินจริง</p>
                                </div>
                                <label class="payment-method" data-payment-choice="card"><input type="radio" name="payment_method" value="card"><span class="method-radio"></span><strong>บัตรเครดิต / บัตรเดบิต</strong></label>
                                <div class="payment-extra" id="card-extra" hidden>
                                    <label>หมายเลขบัตร<input id="card-number" type="text" inputmode="numeric" placeholder="0000 0000 0000 0000" maxlength="19"></label>
                                    <div class="card-small-fields"><label>วันหมดอายุ<input id="card-expiry" type="text" placeholder="MM/YY" maxlength="5"></label><label>รหัส CVV<input id="card-cvv" type="password" inputmode="numeric" placeholder="123" maxlength="4"></label></div>
                                    <p>แบบฟอร์มตัวอย่าง จะไม่มีการส่งหรือเรียกเก็บข้อมูลบัตร</p>
                                </div>
                                <label class="payment-method" data-payment-choice="transfer"><input type="radio" name="payment_method" value="transfer"><span class="method-radio"></span><strong>โอนผ่านธนาคาร</strong><small>ดูข้อมูลบัญชีสำหรับโอน</small></label>
                                <div class="payment-extra transfer-info" id="transfer-extra" hidden>ข้อมูลบัญชีธนาคารจะแสดงเมื่อเชื่อมต่อระบบรับชำระเงินจริง</div>
                            </div>
                            <p class="payment-demo-notice">หน้าชำระเงินนี้เป็นตัวอย่าง ไม่มีการรับเงินจริง การกดปุ่มด้านล่างจะแสดงหน้าสำเร็จเพื่อทดสอบขั้นตอนเท่านั้น</p>
                            <div class="payment-page-actions">
                                <button class="outline-button" type="button" id="back-to-confirm">ย้อนกลับ</button>
                                <button class="primary-button" type="button" id="pay-demo">จำลองการชำระเงิน</button>
                            </div>
                        </section>
                    </div>
                </section>

                <section class="panel success-panel" id="success-panel" hidden>
                    <div class="success-icon" aria-hidden="true">✓</div>
                    <h1>จองคิวสำเร็จ!</h1>
                    <p>ระบบตัวอย่างตรวจสอบข้อมูลเรียบร้อยแล้ว<br>ร้านสามารถยืนยันคิวจริงให้คุณได้เมื่อเชื่อมต่อระบบค่ะ</p>
                    <div class="success-card">
                        <div class="success-card-heading"><h2>รายละเอียดการนัดหมาย</h2><span>ตัวอย่าง</span></div>
                        <div class="success-row"><small>รหัสการจอง</small><strong id="success-booking-id"></strong></div>
                        <div class="success-row"><small>สัตว์เลี้ยง</small><strong id="success-pet"></strong></div>
                        <div class="success-row"><small>บริการหลัก</small><strong id="success-service"></strong></div>
                        <div class="success-row"><small>วันเวลานัดหมาย</small><strong id="success-date-time"></strong></div>
                        <div class="success-row"><small>ช่องทางชำระเงิน</small><strong id="success-payment-method"></strong></div>
                        <div class="success-row success-total"><small>ยอดชำระ</small><strong id="success-total"></strong></div>
                    </div>
                    <div class="success-actions"><a class="primary-button" href="{{ route('bookings.index') }}">ดูการจองของฉัน</a><a class="outline-button" href="{{ route('home') }}">กลับหน้าหลัก</a></div>
                    <p class="demo-note">รายการนี้ยังไม่ถูกบันทึกและไม่มีการชำระเงินจริง</p>
                </section>
            </div>

            <aside class="panel summary-panel">
                <h2>สรุปรายการจองคิว</h2>
                <div class="summary-pet">
                    <span class="pet-avatar dog" id="summary-pet-avatar" aria-hidden="true">🐶</span>
                    <div><small>สัตว์เลี้ยง</small><strong id="summary-pet-name">ยังไม่ได้เลือก</strong></div>
                </div>
                <div class="summary-service">
                    <span class="service-icon" aria-hidden="true">✂️</span>
                    <div><small>บริการที่เลือก</small><strong>{{ $service->service_name }}</strong></div>
                </div>
                <div class="summary-row"><small>วันเวลา</small><span id="summary-date-time">{{ $selectedStep === 2 ? $selectedDate->day . ' ' . $calendarMonthLabel . ' · ' . (int) substr($selectedTime, 0, 2) . ':00 น.' : 'ยังไม่ได้เลือกวันเวลา' }}</span></div>
                <div class="summary-price"><strong>ยอดชำระโดยประมาณ</strong><b id="summary-price">{{ number_format($service->service_price, 0) }}฿</b></div>
                <p class="summary-weight-rate" id="summary-weight-rate">เลือกรายการสัตว์เลี้ยงเพื่อคำนวณราคาตามน้ำหนัก</p>
                <a class="primary-button" id="to-date" href="{{ route('booking.datetime', ['serviceSlug' => $serviceSlug, 'pet_id' => $selectedPetId]) }}" {{ $selectedStep !== 1 ? 'hidden' : '' }}>ขั้นตอนถัดไป (เลือกวันเวลา) ➡️</a>
                <div class="summary-actions" id="date-summary-actions" {{ $selectedStep !== 2 ? 'hidden' : '' }}>
                    <a class="outline-button" id="back-to-pet" href="{{ route('booking.create', ['serviceSlug' => $serviceSlug, 'pet_id' => $selectedPetId]) }}">ย้อนกลับ</a>
                    <button class="primary-button" type="button" id="to-confirm" {{ $selectedStep !== 2 ? 'disabled' : '' }}>ขั้นตอนถัดไป (ยืนยัน) ➡️</button>
                </div>
                <div class="summary-actions" id="confirm-summary-actions" hidden>
                    <button class="outline-button" type="button" id="back-to-date">ย้อนกลับ</button>
                    <button class="primary-button" type="button" id="confirm-and-pay">ยืนยันข้อมูล ➡️</button>
                </div>
                <p class="summary-note" id="booking-message">เลือกสัตว์เลี้ยงและลองเลือกวันเวลาได้ ข้อมูลจะหายเมื่อปิดหรือรีเฟรชหน้านี้</p>
            </aside>
        </div>
    </main>
    <script src="{{ asset('js/booking.js') }}?v={{ filemtime(public_path('js/booking.js')) }}"></script>
</body>
</html>
