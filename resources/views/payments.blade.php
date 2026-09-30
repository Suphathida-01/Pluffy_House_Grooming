@extends('layouts.admin')

@section('title', 'รายงานการชำระเงิน')

@section('content')
<div class="payments-page">
    <header class="page-header">
        <div class="page-title">
            <h1>จัดการการชำระเงิน</h1>
            <p>ติดตามประวัติการรับชำระเงินและตรวจสอบรายได้ประจำวัน</p>
        </div>
        <form class="report-filters" id="report-filters" method="GET" action="{{ route('payments') }}" aria-label="ตัวกรองรายการชำระเงิน">
            <label class="filter-control date-control">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                <input id="report-date" name="date" type="date" value="{{ $selectedDate }}" aria-label="เลือกวันที่">
            </label>
            <label class="filter-control status-control">
                <select id="status-filter" name="status" aria-label="กรองตามสถานะ">
                    <option value="all" @selected($selectedStatus === 'all')>สถานะทั้งหมด</option>
                    <option value="completed" @selected($selectedStatus === 'completed')>ชำระแล้ว</option>
                    <option value="pending" @selected($selectedStatus === 'pending')>รอชำระ</option>
                    <option value="failed" @selected($selectedStatus === 'failed')>ไม่สำเร็จ</option>
                </select>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>
            </label>
        </form>
    </header>

    <section class="summary-grid" aria-label="สรุปยอดการชำระเงิน">
        <article class="summary-card">
            <span class="summary-icon icon-green" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h3"/></svg></span>
            <div><p>รายได้ตามวันที่เลือก</p><strong>{{ number_format($summary['dailyRevenue']) }} บาท</strong><small>{{ $summary['dailyCompleted'] }} รายการที่ชำระ</small></div>
        </article>
        <article class="summary-card">
            <span class="summary-icon icon-amber" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3v9l6 3M21 12a9 9 0 1 1-9-9"/></svg></span>
            <div><p>รอชำระทั้งหมด</p><strong>{{ $summary['pendingCount'] }} รายการ</strong><small>ยอดรวมค้างชำระ ฿{{ number_format($summary['pendingTotal']) }}</small></div>
        </article>
        <article class="summary-card">
            <span class="summary-icon icon-violet" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg></span>
            <div><p>ชำระสำเร็จตามวันที่เลือก</p><strong>{{ $summary['dailyCompleted'] }} รายการ</strong><small>นับเฉพาะรายการที่ชำระสำเร็จ</small></div>
        </article>
    </section>

    <section class="payments-table-section" aria-labelledby="payments-heading">
        <div class="table-heading">
            <div><h2 id="payments-heading">ประวัติรายการ</h2><p>รายการรับชำระเงินล่าสุด</p></div>
            <span class="result-count">{{ $payments->count() }} รายการ</span>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead><tr>
                    <th>หมายเลขรายการ</th><th>ลูกค้า</th><th>บริการ</th><th>จำนวนเงิน</th>
                    <th>ช่องทางการชำระ</th><th>สถานะ</th><th>วัน-เวลาทำรายการ</th><th>การจัดการ</th>
                </tr></thead>
                <tbody id="payment-rows">
                    @forelse ($payments as $payment)
                        @php
                            $reference = $payment->transaction_ref ?: 'TXN-' . str_pad((string) $payment->payment_id, 5, '0', STR_PAD_LEFT);
                            $customerName = $payment->booking?->customer?->user?->user_name ?? 'ไม่พบข้อมูลลูกค้า';
                            $serviceName = $payment->booking?->service?->service_name ?? 'ไม่พบข้อมูลบริการ';
                            $methodLabel = match ($payment->payment_method) {
                                'qrCode' => 'PromptPay',
                                'creditCard' => 'บัตรเครดิต',
                                default => $payment->payment_method,
                            };
                            [$statusLabel, $statusClass] = match ($payment->payment_status) {
                                'completed' => ['ชำระแล้ว', 'status-paid'],
                                'pending' => ['รอชำระ', 'status-pending'],
                                default => ['ไม่สำเร็จ', 'status-refunded'],
                            };
                            $paymentTime = \Illuminate\Support\Carbon::parse($payment->payment_date)->format('d/m/Y · H:i');
                        @endphp
                        <tr data-id="{{ $reference }}" data-customer="{{ $customerName }}" data-service="{{ $serviceName }}" data-amount="{{ $payment->payment_amount }}" data-method="{{ $methodLabel }}" data-time="{{ $paymentTime }}">
                            <td class="transaction-id">{{ $reference }}</td><td>{{ $customerName }}</td><td>{{ $serviceName }}</td><td class="amount">฿{{ number_format($payment->payment_amount) }}</td><td>{{ $methodLabel }}</td><td><span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td><td class="date-cell">{{ $paymentTime }}</td><td><button class="detail-button" type="button">เปิดดู</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state">ไม่พบรายการชำระเงินในวันที่เลือก</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<dialog class="payment-dialog" id="payment-dialog" aria-labelledby="dialog-title">
    <div class="dialog-heading"><h2 id="dialog-title">รายละเอียดรายการ</h2><button class="dialog-close" type="button" aria-label="ปิด">×</button></div>
    <dl class="payment-details">
        <div><dt>หมายเลขรายการ</dt><dd data-detail="id"></dd></div><div><dt>ลูกค้า</dt><dd data-detail="customer"></dd></div>
        <div><dt>บริการ</dt><dd data-detail="service"></dd></div><div><dt>จำนวนเงิน</dt><dd data-detail="amount"></dd></div>
        <div><dt>ช่องทางการชำระ</dt><dd data-detail="method"></dd></div><div><dt>วัน-เวลาทำรายการ</dt><dd data-detail="time"></dd></div>
    </dl>
</dialog>

<script>
    const paymentRows = [...document.querySelectorAll('#payment-rows tr[data-id]')];
    const filterForm = document.getElementById('report-filters');
    const paymentDialog = document.getElementById('payment-dialog');

    filterForm.querySelectorAll('input, select').forEach((control) => {
        control.addEventListener('change', () => filterForm.requestSubmit());
    });

    paymentRows.forEach((row) => {
        row.querySelector('.detail-button').addEventListener('click', () => {
            ['id', 'customer', 'service', 'method', 'time'].forEach((key) => {
                paymentDialog.querySelector(`[data-detail="${key}"]`).textContent = row.dataset[key];
            });
            paymentDialog.querySelector('[data-detail="amount"]').textContent = `฿${Number(row.dataset.amount).toLocaleString('th-TH')}`;
            paymentDialog.showModal();
        });
    });

    paymentDialog.querySelector('.dialog-close').addEventListener('click', () => paymentDialog.close());
    paymentDialog.addEventListener('click', (event) => {
        if (event.target === paymentDialog) paymentDialog.close();
    });
</script>
@endsection