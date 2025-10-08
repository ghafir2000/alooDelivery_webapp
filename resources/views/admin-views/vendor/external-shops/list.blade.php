@extends('layouts.back-end.app')

@section('title', 'قائمة المتاجر الخارجية')

@section('content')
    <div class="content container-fluid">
        <h2>قائمة المتاجر الخارجية</h2>
        <a href="{{ route('admin.vendors.external-shops.create') }}" class="btn btn-primary">إضافة متجر خارجي</a>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>الاسم</th>
                    <th>العنوان</th>
                    <th>UID</th>
                    <th>API Key</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($externalShops as $shop)
                    <tr>
                        <td>{{ $shop->id }}</td>
                        <td>{{ $shop->name }}</td>
                        <td>{{ $shop->address }}</td>
                        <td>{{ $shop->uid }}</td>
                        <td>{{ $shop->api_key }}</td>
                        <td>
                            <a href="{{ route('admin.vendors.external-shops.edit', $shop->id) }}" class="btn btn-sm btn-warning">تعديل</a>
                            <form action="{{ route('admin.vendors.external-shops.destroy', $shop->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                            </form>
                            <!-- زر جديد لعرض تفاصيل الـ APIs -->
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#apiDetailsModal{{ $shop->id }}">عرض APIs</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $externalShops->links() }}

        <!-- Modals لكل متجر (يتم توليدها ديناميكياً) -->
        @foreach($externalShops as $shop)
            <div class="modal fade" id="apiDetailsModal{{ $shop->id }}" tabindex="-1" role="dialog" aria-labelledby="apiDetailsModalLabel{{ $shop->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="apiDetailsModalLabel{{ $shop->id }}">API Details for Shop: {{ $shop->name }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- API for Create Order -->
                            <h6>API for Create Order</h6>
                            <div class="position-relative">
                                <pre><code id="createOrderCurl{{ $shop->id }}">curl -X POST "http://yourdomain.com/api/create-external-order" \
-H "Content-Type: application/json" \
-d '{
    "order_data": {
        "longitude": 24.7136,
        "latitude": 46.6753,
        "customer_name": "John Doe",
        "customer_address": "123 Main St"
    },
    "external_shop_name": "{{ $shop->name }}",
    "api_key": "{{ $shop->api_key }}",
    "uid": "{{ $shop->uid }}"
}'</code></pre>
                                <button class="btn btn-sm btn-secondary copy-btn" data-target="createOrderCurl{{ $shop->id }}">Copy</button>
                            </div>

                            <!-- API for Calculate Distance -->
                            <h6 class="mt-4">API for Calculate Distance</h6>
                            <div class="position-relative">
                                <pre><code id="calculateDistanceCurl{{ $shop->id }}">curl -X POST "http://yourdomain.com/api/calculate-distance" \
-H "Content-Type: application/json" \
-d '{
    "lat1": 24.7136,
    "lon1": 46.6753,
    "lat2": 24.7743,
    "lon2": 46.7386
}'</code></pre>
                                <button class="btn btn-sm btn-secondary copy-btn" data-target="calculateDistanceCurl{{ $shop->id }}">Copy</button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('script')
    <script>
        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const codeElement = document.getElementById(targetId);
                const textToCopy = codeElement.innerText;

                navigator.clipboard.writeText(textToCopy).then(() => {
                    alert('Copied to clipboard!');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            });
        });
    </script>
@endpush
