<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pluffy House') - Back Office</title>
    <!-- เรียกใช้งาน External CSS ผ่านฟังก์ชัน asset()[cite: 5, 6] -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>
    <aside class="sidebar">
        <div>
            <a class="sidebar-brand" href="{{ route('services') }}">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 13.4c-2.7 0-5.1 2-5.1 4.3 0 1.5 1.2 2.3 2.6 1.9.8-.2 1.7-.6 2.5-.6s1.7.4 2.5.6c1.4.4 2.6-.4 2.6-1.9 0-2.3-2.4-4.3-5.1-4.3Z"/><ellipse cx="5.6" cy="9.1" rx="2" ry="2.6"/><ellipse cx="10" cy="6.3" rx="2" ry="2.6"/><ellipse cx="14.2" cy="6.3" rx="2" ry="2.6"/><ellipse cx="18.5" cy="9.1" rx="2" ry="2.6"/></svg>
                </span>
                <span><strong>Pluffy House</strong><small>BACK-OFFICE</small></span>
            </a>
            <nav class="sidebar-nav" aria-label="เมนูหลัก">
                <a href="{{ route('services') }}" class="menu-item">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/></svg><span>หน้าแรก</span>
                </a>
                <span class="menu-item is-disabled"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg><span>จัดการการจอง</span></span>
                <span class="menu-item is-disabled"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z"/></svg><span>จัดการลูกค้า</span></span>
                <a href="{{ route('services') }}" class="menu-item"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m14.5 6.5 3 3M4 20l4.5-1 11-11a2.1 2.1 0 0 0-3-3l-11 11zM3 3l4 4M2 8l6-6M15 15l6 6M16 16l5-5"/></svg><span>บริการ &amp; แพ็กเกจ</span></a>
                <a href="{{ route('staff') }}" class="menu-item {{ request()->routeIs('staff') ? 'active' : '' }}" @if(request()->routeIs('staff')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M5 21a7 7 0 0 1 14 0M19 8l2 2-4 4-2-2z"/></svg><span>จัดการช่าง</span></a>
                <a href="{{ route('payments') }}" class="menu-item {{ request()->routeIs('payments') ? 'active' : '' }}" @if(request()->routeIs('payments')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 19h17M8 15l4-4 3 2 5-7"/></svg><span>รายงานยอดขาย</span></a>
            </nav>
        </div>
        <div class="sidebar-profile"><span class="profile-avatar">ADM</span><span><strong>แอดมินใจดี</strong><small>ผู้ดูแลระบบ</small></span><span class="profile-exit" aria-hidden="true">↪</span></div>
    </aside>

    <main class="main-content">
        @if (session('success'))
            <div class="flash-message flash-success" role="status">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="flash-message flash-error" role="alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="flash-message flash-error" role="alert">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>