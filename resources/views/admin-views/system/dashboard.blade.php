@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('dashboard'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Modal Styling to match Dashboard */
        .modal-content {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        .modal-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1a1a1a;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer {
            border-top: 1px solid #e9ecef;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .form-control {
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    @if (auth('admin')->user()->admin_role_id == 1 || Helpers::module_permission_check('dashboard'))
        <div class="content container-fluid">
            <div class="page-header pb-0 mb-0 border-0">
                <div class="flex-between align-items-center">
                    <div>
                        <h1 class="page-header-title">{{ translate('welcome') . ' ' . auth('admin')->user()->name }}</h1>
                        <p>{{ translate('monitor_your_business_analytics_and_statistics') . '.' }}</p>
                    </div>
                </div>
            </div>
            <div class="card mb-2 remove-card-shadow">
                <div class="card-body">
                    <div class="row flex-between align-items-center g-2 mb-3">
                        <div class="col-sm-6">
                            <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/business_analytics.png') }}"
                                    alt="">{{ translate('business_analytics') }}
                            </h4>
                        </div>
                        <div class="col-sm-6 d-flex justify-content-sm-end">
                            <select class="custom-select w-auto" name="statistics_type" id="statistics_type">
                                <option value="overall"
                                    {{ session()->has('statistics_type') && session('statistics_type') == 'overall' ? 'selected' : '' }}>
                                    {{ translate('overall_statistics') }}
                                </option>
                                <option value="today"
                                    {{ session()->has('statistics_type') && session('statistics_type') == 'today' ? 'selected' : '' }}>
                                    {{ translate('todays_Statistics') }}
                                </option>
                                <option value="this_month"
                                    {{ session()->has('statistics_type') && session('statistics_type') == 'this_month' ? 'selected' : '' }}>
                                    {{ translate('this_Months_Statistics') }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2" id="order_stats">
                        @include('admin-views.partials._dashboard-order-status', ['data' => $data])
                    </div>
                </div>
            </div>

            <div class="card mb-3 remove-card-shadow">
                <div class="card-body">
                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-3">
                        <img width="20" class="mb-1"
                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png') }}" alt="">
                        {{ translate('admin_wallet') }}
                    </h4>

                    <div class="row g-2" id="order_stats">
                        @include('admin-views.partials._dashboard-wallet-stats', ['data' => $data])
                    </div>
                </div>
            </div>

            <!-- Delivery Price Per KM Card -->
            <div class="card mb-3 remove-card-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                            <img width="20" class="mb-1"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/distance.png') }}" alt="">
                            {{ translate('delivery_price_per_km') }}
                        </h4>
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editPriceModal">
                            {{ translate('edit') }}
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="text-center">
                                <h3 id="price-per-km" class="text-primary">0.00</h3>
                                <span class="text-muted">{{ translate('price_per_kilometer') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for Editing Price -->
            <div class="modal fade" id="editPriceModal" tabindex="-1" role="dialog" aria-labelledby="editPriceModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPriceModalLabel">{{ translate('edit_delivery_price_per_km') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="edit-price-form">
                                <div class="form-group">
                                    <label for="price_per_km">{{ translate('price_per_km') }}</label>
                                    <input type="number" step="0.01" class="form-control" id="price_per_km" name="price_per_km" required>
                                </div>
                                <button type="submit" class="btn btn-primary">{{ translate('save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-1">
                <div class="col-lg-8" id="order-statistics-div">
                    @include('admin-views.system.partials.order-statistics')
                </div>
                <div class="col-lg-4">
                    <div class="card remove-card-shadow h-100">
                        <div class="card-header">
                            <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0 ">
                                {{ translate('user_overview') }}
                            </h4>
                        </div>
                        <div class="card-body justify-content-center d-flex flex-column">
                            <div>
                                <div class="position-relative">
                                    <div id="chart" class="apex-pie-chart d-flex justify-content-center"></div>
                                    <div class="total--orders">
                                        <h3>{{ $data['getTotalCustomerCount'] + $data['getTotalVendorCount'] + $data['getTotalDeliveryManCount'] }}
                                        </h3>
                                        <span class="text-capitalize">{{ translate('total_User') }}</span>
                                    </div>
                                </div>
                                <div class="apex-legends flex-column">
                                    <div class="before-bg-0">
                                        <span class="text-capitalize">{{ translate('total_customer') . ' ' . '(' . $data['getTotalCustomerCount'] . ')' }}
                                        </span>
                                    </div>
                                    <div class="before-bg-1">
                                        <span
                                            class="text-capitalize">{{ translate('total_vendor') . ' ' . '(' . $data['getTotalVendorCount'] . ')' }}</span>
                                    </div>
                                    <div class="before-bg-2">
                                        <span
                                            class="text-capitalize">{{ translate('total_delivery_man') . ' ' . '(' . $data['getTotalDeliveryManCount'] . ')' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12" id="earn-statistics-div">
                    @include('admin-views.system.partials.earning-statistics')
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._top-customer', [
                            'top_customer' => $data['top_customer'],
                        ])
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._top-store-by-order', [
                            'top_store_by_order_received' => $data['top_store_by_order_received'],
                        ])
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._top-selling-store', [
                            'topVendorByEarning' => $data['topVendorByEarning'],
                        ])
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._most-rated-products', [
                            'mostRatedProducts' => $data['mostRatedProducts'],
                        ])
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._top-selling-products', [
                            'topSellProduct' => $data['topSellProduct'],
                        ])
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._top-delivery-man', [
                            'topRatedDeliveryMan' => $data['topRatedDeliveryMan'],
                        ])
                    </div>
                </div>

            </div>
        </div>
    @else
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-12 mb-2 mb-sm-0">
                        <h3 class="text-center">{{ translate('hi') }} {{ auth('admin')->user()->name }}
                            {{ ' , ' . translate('welcome_to_dashboard') }}.</h3>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <span id="earning-statistics-url" data-url="{{ route('admin.dashboard.earning-statistics') }}"></span>
    <span id="order-status-url" data-url="{{ route('admin.dashboard.order-status') }}"></span>
    <span id="seller-text" data-text="{{ translate('vendor') }}"></span>
    <span id="message-commission-text" data-text="{{ translate('commission') }}"></span>
    <span id="in-house-text" data-text="{{ translate('In-house') }}"></span>
    <span id="customer-text" data-text="{{ translate('customer') }}"></span>
    <span id="store-text" data-text="{{ translate('store') }}"></span>
    <span id="product-text" data-text="{{ translate('product') }}"></span>
    <span id="order-text" data-text="{{ translate('order') }}"></span>
    <span id="brand-text" data-text="{{ translate('brand') }}"></span>
    <span id="business-text" data-text="{{ translate('business') }}"></span>
    <span id="orders-text" data-text="{{ $data['order'] }}"></span>
    <span id="user-overview-data" style="background-color: #000;" data-customer="{{ $data['getTotalCustomerCount'] }}"
        data-customer-title="{{ translate('Total_Customer') }}" data-vendor="{{ $data['getTotalVendorCount'] }}"
        data-vendor-title="{{ translate('Total_Vendor') }}" data-delivery-man="{{ $data['getTotalDeliveryManCount'] }}"
        data-delivery-man-title="{{ translate('Total_Delivery_Man') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/apexcharts.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/dashboard.js') }}"></script>
    <script>
        // Fetch Delivery Price Per KM from API
        document.addEventListener('DOMContentLoaded', function () {
            function fetchPricePerKm() {
                fetch('/api/v2/settings/delivery-price-per-km', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => {
                        console.log('Delivery Price API Status:', response.status, response.statusText);
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status} ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Delivery Price API Data:', data);
                        if (data.status === 'success' && data.price_per_km !== undefined) {
                            document.getElementById('price-per-km').textContent = data.price_per_km.toFixed(2);
                            document.getElementById('price_per_km').value = data.price_per_km; // Set initial value for modal
                        } else {
                            console.warn('Invalid or missing price_per_km in API response:', data);
                            document.getElementById('price-per-km').textContent = '0.00';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching delivery price from API:', error);
                        document.getElementById('price-per-km').textContent = '0.00';
                    });
            }

            // Initial fetch
            fetchPricePerKm();

            // Handle form submission for updating price
            document.getElementById('edit-price-form').addEventListener('submit', function (e) {
                e.preventDefault();
                const pricePerKm = document.getElementById('price_per_km').value;

                fetch('/api/v2/settings/delivery-price-per-km', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ price_per_km: pricePerKm })
                })
                    .then(response => {
                        console.log('Update Price API Status:', response.status, response.statusText);
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status} ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Update Price API Data:', data);
                        if (data.status === 'success') {
                            document.getElementById('price-per-km').textContent = data.price_per_km.toFixed(2);
                            $('#editPriceModal').modal('hide');
                            alert('{{ translate('price_updated_successfully') }}');
                        } else {
                            console.warn('Failed to update price_per_km:', data);
                            alert('{{ translate('failed_to_update_price') }}');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating delivery price from API:', error);
                        alert('{{ translate('failed_to_update_price') }}');
                    });
            });
        });
    </script>
@endpush
