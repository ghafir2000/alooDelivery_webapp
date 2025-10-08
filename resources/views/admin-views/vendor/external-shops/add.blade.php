@extends('layouts.back-end.app')

@section('title', 'إضافة متجر خارجي')

@section('content')
    <div class="content container-fluid">
        <h2>إضافة متجر خارجي</h2>
        <form action="{{ route('admin.vendors.external-shops.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>اسم المتجر</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>العنوان</label>
                <textarea name="address" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">إضافة</button>
        </form>
    </div>
@endsection
