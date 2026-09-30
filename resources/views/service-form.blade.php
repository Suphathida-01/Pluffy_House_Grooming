@extends('layouts.admin')

@section('title', $service->exists ? 'แก้ไขบริการ' : 'เพิ่มบริการ')

@section('content')
<div class="service-form-page">
    <header class="page-header">
        <div class="page-title">
            <h1>{{ $service->exists ? 'แก้ไขบริการ' : 'เพิ่มบริการ' }}</h1>
            <p>กรอกรายละเอียด ราคา และระยะเวลาให้บริการ</p>
        </div>
        <a class="btn btn-light" href="{{ route('services') }}">กลับไปรายการ</a>
    </header>

    <form class="service-form" method="POST" action="{{ $service->exists ? route('updateService', $service) : route('storeService') }}">
        @csrf
        @if ($service->exists)
            @method('PUT')
        @endif

        <label class="service-field service-field-full">
            <span>ชื่อบริการ</span>
            <input type="text" name="service_name" maxlength="255" value="{{ old('service_name', $service->service_name) }}" required>
        </label>

        <label class="service-field service-field-full">
            <span>รายละเอียด</span>
            <textarea name="service_description" rows="4" required>{{ old('service_description', $service->service_description) }}</textarea>
        </label>

        <label class="service-field">
            <span>ระยะเวลา (นาที)</span>
            <input type="number" name="service_duration_minutes" min="1" step="1" value="{{ old('service_duration_minutes', $service->service_duration_minutes) }}" required>
        </label>

        <label class="service-field">
            <span>สถานะ</span>
            <select name="service_status" required>
                <option value="active" @selected(old('service_status', $service->service_status ?: 'active') === 'active')>เปิดใช้งาน</option>
                <option value="inactive" @selected(old('service_status', $service->service_status) === 'inactive')>ปิดใช้งาน</option>
            </select>
        </label>

        <label class="service-field">
            <span>ราคาไซซ์เล็ก (บาท)</span>
            <input type="number" name="price_small" min="0" max="99999999.99" step="0.01" value="{{ old('price_small', $service->price_small) }}" required>
        </label>

        <label class="service-field">
            <span>ราคาไซซ์กลาง (บาท)</span>
            <input type="number" name="price_medium" min="0" max="99999999.99" step="0.01" value="{{ old('price_medium', $service->price_medium) }}" required>
        </label>

        <label class="service-field">
            <span>ราคาไซซ์ใหญ่ (บาท)</span>
            <input type="number" name="price_large" min="0" max="99999999.99" step="0.01" value="{{ old('price_large', $service->price_large) }}" required>
        </label>

        <div class="service-form-actions service-field-full">
            <a class="btn btn-light" href="{{ route('services') }}">ยกเลิก</a>
            <button class="btn btn-primary" type="submit">บันทึกบริการ</button>
        </div>
    </form>
</div>
@endsection