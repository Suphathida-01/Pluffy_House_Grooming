<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Pluffy House Grooming</title>

    {{-- ต้องมี Tailwind อยู่แล้วในโปรเจกต์ (ผ่าน Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    {{-- ใช้ Alpine.js สลับแท็บ login/register (ถ้ายังไม่มีให้ npm install alpinejs แล้ว import ใน app.js) --}}
</head>
{{-- เพิ่ม style เพื่อบังคับใช้ฟอนต์ Prompt ทั้งหน้า --}}
<body class="bg-neutral-900 min-h-screen flex items-center justify-center p-6" style="font-family: 'Prompt', sans-serif;">

    <div x-data="{ tab: 'login' }" class="w-full max-w-5xl bg-[#faf6f0] rounded-2xl shadow-xl p-8">

        {{-- โลโก้ --}}
        <div class="flex items-center gap-2 mb-6">
            <div class="w-9 h-9 rounded-lg bg-violet-500 flex items-center justify-center text-white">🐾</div>
            <span class="font-semibold text-lg text-neutral-800">Pluffy House Grooming</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- ฝั่งซ้าย: รูปภาพ + ข้อความ --}}
            <div class="relative rounded-xl overflow-hidden min-h-[420px]">
                <img src="{{ asset('images/grooming-hero.jpg') }}"
                     alt="Pluffy House Grooming"
                     class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/30"></div>
                <div class="relative p-6 text-white">
                    <h2 class="text-2xl font-bold leading-snug">
                        ดูแลสัตว์เลี้ยงตัวโปรดด้วย<br>ความรักและใส่ใจ
                    </h2>
                    <p class="mt-3 text-sm text-white/90">
                        อีเมลเสร็จประสบการณ์ครบวงจรที่พรั่งพร้อมไปด้วยบรรยากาศแสนอบอุ่น
                        เป็นมิตรกับสัตว์เลี้ยงของคุณ
                    </p>
                </div>
            </div>

            {{-- ฝั่งขวา: ฟอร์ม --}}
            <div>
                {{-- Tab switch --}}
                <div class="flex bg-white rounded-lg p-1 mb-6 shadow-sm">
                    <button type="button"
                            @click="tab = 'login'"
                            :class="tab === 'login' ? 'bg-violet-500 text-white' : 'text-neutral-500'"
                            class="flex-1 py-2 rounded-md text-sm font-medium transition">
                        เข้าสู่ระบบ (Login)
                    </button>
                    <button type="button"
                            @click="tab = 'register'"
                            :class="tab === 'register' ? 'bg-violet-500 text-white' : 'text-neutral-500'"
                            class="flex-1 py-2 rounded-md text-sm font-medium transition">
                        สมัครสมาชิก (Register)
                    </button>
                </div>

                {{-- แสดง error รวม (ถ้ามี) --}}
                @if ($errors->any())
                    <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg p-3">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ===== LOGIN FORM ===== --}}
                <form x-show="tab === 'login'" x-cloak method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <div>
                        <h3 class="font-semibold text-neutral-800 mb-1">ยินดีต้อนรับกลับมา</h3>
                        <p class="text-sm text-neutral-500 mb-4">กรุณากรอกข้อมูลเพื่อเข้าสู่ระบบสมาชิก</p>
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-700 mb-1">อีเมล <span class="text-red-500">*</span></label>
                        <input type="email" name="user_email" value="{{ old('user_email') }}" required
                               placeholder="example@email.com"
                               class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-700 mb-1">รหัสผ่าน <span class="text-red-500">*</span></label>
                        <div class="relative" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" name="user_password" required
                                   placeholder="••••••••"
                                   class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-violet-400">
                            <button type="button" @click="show = !show"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 text-sm">
                                👁
                            </button>
                        </div>
                        <div class="text-right mt-1">
                            <a href="{{ route('password.request') }}" class="text-xs text-violet-500 hover:underline">ลืมรหัสผ่าน?</a>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-violet-500 hover:bg-violet-600 text-white py-2.5 rounded-lg text-sm font-medium transition">
                        เข้าสู่ระบบ
                    </button>
                </form>

                {{-- ===== REGISTER FORM ===== --}}
                <form x-show="tab === 'register'" x-cloak method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <div>
                        <h3 class="font-semibold text-neutral-800 mb-1">สมัครสมาชิกใหม่</h3>
                        <p class="text-sm text-neutral-500 mb-4">มาร่วมเป็นครอบครัวเดียวกันเพื่อสิทธิพิเศษมากมาย</p>
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-700 mb-1">ชื่อ-นามสกุล <span class="text-red-500">*</span></label>
                        <input type="text" name="user_name" value="{{ old('user_name') }}" required
                               placeholder="กรอกชื่อและนามสกุลของคุณ"
                               class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-700 mb-1">อีเมล <span class="text-red-500">*</span></label>
                        <input type="email" name="user_email" value="{{ old('user_email') }}" required
                               placeholder="example@email.com"
                               class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-700 mb-1">เบอร์โทรศัพท์ <span class="text-red-500">*</span></label>
                        <input type="text" name="user_phone" value="{{ old('user_phone') }}" required
                               placeholder="08X-XXX-XXXX"
                               class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-700 mb-1">รหัสผ่าน <span class="text-red-500">*</span></label>
                        <input type="password" name="user_password" required minlength="8"
                               placeholder="ตั้งรหัสผ่านอย่างน้อย 8 ตัวอักษร"
                               class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-700 mb-1">ยืนยันรหัสผ่าน <span class="text-red-500">*</span></label>
                        <input type="password" name="user_password_confirmation" required minlength="8"
                               placeholder="กรอกรหัสผ่านอีกครั้งให้ตรงกัน"
                               class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                    </div>

                    <label class="flex items-start gap-2 text-xs text-neutral-500">
                        <input type="checkbox" required class="mt-0.5">
                        ยอมรับเงื่อนไขในการใช้บริการและนโยบายความเป็นส่วนตัวของ Pluffy House
                    </label>

                    <button type="submit"
                            class="w-full bg-violet-500 hover:bg-violet-600 text-white py-2.5 rounded-lg text-sm font-medium transition">
                        สมัครสมาชิก
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>