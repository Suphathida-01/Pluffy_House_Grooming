@extends('layouts.admin')

@section('title', 'จัดการบริการ')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>จัดการบริการ</h1>
        <p>ตั้งค่าบริการ ราคาตามขนาดสัตว์เลี้ยง และสถานะเปิดให้บริการ</p>
    </div>
    <a class="btn btn-primary" href="{{ route('createService') }}">+ เพิ่มบริการ</a>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ลำดับ</th>
                <th>ชื่อบริการ</th>
                <th>รายละเอียด</th>
                <th>ราคา (เล็ก / กลาง / ใหญ่)</th>
                <th>ระยะเวลา</th>
                <th>สถานะ</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($services as $service)
                <tr>
                    <td>{{ $service->service_id }}</td>
                    <td><strong>{{ $service->service_name }}</strong></td>
                    <td>{{ $service->service_description }}</td>
                    <td>
                        <span class="badge badge-success">S: ฿{{ number_format($service->price_small) }}</span>
                        <span class="badge badge-success">M: ฿{{ number_format($service->price_medium) }}</span>
                        <span class="badge badge-success">L: ฿{{ number_format($service->price_large) }}</span>
                    </td>
                    <td>{{ $service->service_duration_minutes }} นาที</td>
                    <td><span class="badge {{ $service->service_status === 'active' ? 'badge-success' : 'badge-danger' }}">{{ $service->service_status === 'active' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}</span></td>
                    <td>
                        <div class="service-actions">
                            <a class="btn btn-light" href="{{ route('editService', $service) }}">แก้ไข</a>
                            <form method="POST" action="{{ route('deleteService', $service) }}" onsubmit="return confirm('ยืนยันลบบริการนี้หรือไม่?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">ลบ</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td class="service-empty" colspan="7">ยังไม่มีบริการ กรุณาเพิ่มบริการรายการแรก</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
