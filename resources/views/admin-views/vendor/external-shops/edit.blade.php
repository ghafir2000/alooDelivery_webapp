@extends('layouts.back-end.app')

@section('title', 'تعديل متجر خارجي')

@section('content')
    <div class="content container-fluid">
        <h2>تعديل متجر خارجي</h2>
        <form action="{{ route('admin.vendors.external-shops.update', $externalShop->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>اسم المتجر</label>
                <input type="text" name="name" class="form-control" value="{{ $externalShop->name }}" required>
            </div>
            <div class="form-group">
                <label>العنوان</label>
                <textarea name="address" class="form-control" required>{{ $externalShop->address }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        </form>
    </div>
@endsection


