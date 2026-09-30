<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Pluffy House Grooming</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="auth-card">
        <div class="auth-logo">
            <div class="icon">🐾</div>
            Pluffy House Grooming
        </div>

        <div class="auth-grid">
            {{-- ฝั่งซ้าย รูป --}}
            <div class="auth-hero">
                <img src="{{ asset('images\ภาพหน้าlogin.jpg') }}" alt="Pluffy House Grooming">
                <div class="auth-hero-text">
                    <h2>ดูแลสัตว์เลี้ยงตัวโปรดด้วย<br>ความรักและใส่ใจ</h2>
                    <p>อีเมลเสร็จประสบการณ์ครบวงจรที่พรั่งพร้อมไปด้วยบรรยากาศแสนอบอุ่น เป็นมิตรกับสัตว์เลี้ยงของคุณ</p>
                </div>
            </div>

            {{-- ฝั่งขวา ฟอร์ม --}}
            <div class="auth-form-side">
                <div class="tabs">
                    <button type="button" class="tab-btn active" data-tab="login" onclick="switchTab('login')">
                        🔑 เข้าสู่ระบบ (Login)
                    </button>
                    <button type="button" class="tab-btn" data-tab="register" onclick="switchTab('register')">
                        📝 สมัครสมาชิก (Register)
                    </button>
                </div>

                @if ($errors->any())
                    <div class="alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ===== LOGIN ===== --}}
                <div class="tab-panel active" id="panel-login">
                    <div class="form-title">ยินดีต้อนรับกลับมา</div>
                    <div class="form-subtitle">กรุณากรอกข้อมูลเพื่อเข้าสู่ระบบสมาชิก</div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="field">
                            <label>อีเมล <span class="req">*</span></label>
                            <input type="email" name="user_email" value="{{ old('user_email') }}" required placeholder="example@email.com">
                        </div>

                        <div class="field">
                            <label>รหัสผ่าน <span class="req">*</span></label>
                            <div class="input-wrap">
                                <input type="password" name="user_password" id="login-password" required placeholder="••••••••">
                                <button type="button" class="toggle-pass" onclick="togglePassword('login-password', this)">👁</button>
                            </div>
                            <a href="{{ route('password.request') }}" class="forgot-link">ลืมรหัสผ่าน?</a>
                        </div>

                        <button type="submit" class="btn-submit">เข้าสู่ระบบ</button>
                    </form>
                </div>

                {{-- ===== REGISTER ===== --}}
                <div class="tab-panel" id="panel-register">
                    <div class="form-title">สมัครสมาชิกใหม่</div>
                    <div class="form-subtitle">มาร่วมเป็นครอบครัวเดียวกันเพื่อสิทธิพิเศษมากมาย</div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="field">
                            <label>ชื่อ-นามสกุล <span class="req">*</span></label>
                            <input type="text" name="user_name" value="{{ old('user_name') }}" required placeholder="กรอกชื่อและนามสกุลของคุณ">
                        </div>

                        <div class="field">
                            <label>อีเมล <span class="req">*</span></label>
                            <input type="email" name="user_email" value="{{ old('user_email') }}" required placeholder="example@email.com">
                        </div>

                        <div class="field">
                            <label>เบอร์โทรศัพท์ <span class="req">*</span></label>
                            <input type="tel" name="user_phone" value="{{ old('user_phone') }}" required placeholder="08X-XXX-XXXX">
                        </div>

                        <div class="field">
                            <label>รหัสผ่าน <span class="req">*</span></label>
                            <div class="input-wrap">
                                <input type="password" name="user_password" id="reg-password" required minlength="8" placeholder="ตั้งรหัสผ่านอย่างน้อย 8 ตัวอักษร">
                                <button type="button" class="toggle-pass" onclick="togglePassword('reg-password', this)">👁</button>
                            </div>
                        </div>

                        <div class="field">
                            <label>ยืนยันรหัสผ่าน <span class="req">*</span></label>
                            <div class="input-wrap">
                                <input type="password" name="user_password_confirmation" id="reg-password-confirm" required minlength="8" placeholder="กรอกรหัสผ่านอีกครั้งให้ตรงกัน">
                                <button type="button" class="toggle-pass" onclick="togglePassword('reg-password-confirm', this)">👁</button>
                            </div>
                        </div>

                        <label class="checkbox-row">
                            <input type="checkbox" required>
                            ยอมรับเงื่อนไขในการใช้บริการและนโยบายความเป็นส่วนตัวของ Pluffy House
                        </label>

                        <button type="submit" class="btn-submit">สมัครสมาชิก</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.tab === tab);
            });
            document.getElementById('panel-login').classList.toggle('active', tab === 'login');
            document.getElementById('panel-register').classList.toggle('active', tab === 'register');
        }

        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.textContent = isHidden ? '🙈' : '👁';
        }

        // ถ้ามี validation error ฝั่ง register ให้เปิด tab register ไว้อัตโนมัติ
        @if ($errors->has('user_name') || $errors->has('user_phone') || $errors->has('user_password_confirmation'))
            switchTab('register');
        @endif
    </script>

</body>
</html>