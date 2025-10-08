@extends('layouts.back-end.app')

@section('title', translate('driver_tracking'))

@push('css_or_js')
    <style>
        #map {
            height: 600px;
            width: 100%;
            border-radius: 8px;
        }

        /* CSS for pulsing dot */
        .driver-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            animation: pulse 1.5s ease-in-out infinite;
        }

        .billing-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #FFFF00; /* Yellow for billing */
            border: 2px solid white;
            box-shadow: 0 0 10px rgba(255, 255, 0, 0.8);
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            }
            50% {
                transform: scale(1.2);
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            }
        }

        /* Style for InfoWindow */
        .gm-style-iw {
            background-color: white;
            border-radius: 5px;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
@endpush

@section('content')
<div class="content container-fluid main-card {{Session::get('direction')}}">
    <div class="mb-4">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            {{ translate('driver_tracking') }}
        </h2>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="map"></div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiEveSJpdRGmDMqDle6xFz_0-qbUNGZ90&libraries=places&cache_bust={{ time() }}"></script>
    <script>
        let map;
        let orderMarkers = [];
        let orderPolylines = [];

        // Function to generate random color for drivers
        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        // Function to sanitize and validate coordinate strings
        function sanitizeCoordinate(coord) {
            if (coord == null || typeof coord === 'undefined') {
                console.log('الإحداثيات مفقودة أو غير معرفة:', coord);
                return null;
            }
            const cleaned = String(coord).trim();
            console.log('الإحداثيات بعد التنظيف:', cleaned);
            const parsed = parseFloat(cleaned);
            if (isNaN(parsed)) {
                console.log('فشل تحويل الإحداثيات إلى رقم:', cleaned);
                return null;
            }
            return parsed;
        }

        function initMap() {
            console.log('بدء تهيئة الخريطة...');
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 33.44573021, lng: 36.20113865 },
                zoom: 12,
            });
            console.log('الخريطة تم تهيئتها بنجاح.');

            console.log('جاري جلب البيانات من الـ API...');
            fetch('/api/v2/delivery-man/getDeliveryManLocation', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('حالة استجابة الـ API:', response.status, response.statusText);
                    if (!response.ok) {
                        throw new Error(`خطأ في الـ HTTP! الحالة: ${response.status} ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('بيانات استجابة الـ API:', JSON.stringify(data, null, 2));
                    if (!data || !Array.isArray(data) || data.length === 0) {
                        console.warn('لا توجد مواقع توصيل متاحة من الـ API.');
                        alert('لا توجد مواقع توصيل متاحة.');
                        return;
                    }

                    const bounds = new google.maps.LatLngBounds();
                    let validMarkers = false;

                    data.forEach((location, index) => {
                        console.log(`معالجة الموقع عند الفهرس ${index}:`, JSON.stringify(location, null, 2));

                        // Validate location object
                        if (!location || typeof location !== 'object') {
                            console.error(`بيانات الموقع غير صالحة عند الفهرس ${index}:`, location);
                            return;
                        }

                        // Validate driver name
                        if (!location.f_name || !location.l_name) {
                            console.error(`اسم السائق غير صالح عند الفهرس ${index}:`, { f_name: location.f_name, l_name: location.l_name });
                            return;
                        }

                        // Validate delivery object
                        if (!location.delivery || typeof location.delivery !== 'object' || !location.delivery.latitude || !location.delivery.longitude) {
                            console.error(`بيانات التوصيل غير صالحة عند الفهرس ${index}:`, location.delivery);
                            return;
                        }

                        // Validate and sanitize delivery coordinates
                        const driverLat = sanitizeCoordinate(location.delivery.latitude);
                        const driverLng = sanitizeCoordinate(location.delivery.longitude);
                        console.log(`إحداثيات التوصيل المحولة عند الفهرس ${index}:`, { latitude: driverLat, longitude: driverLng });

                        if (driverLat === null || driverLng === null) {
                            console.error(`إحداثيات التوصيل غير صالحة عند الفهرس ${index}:`, { latitude: location.delivery.latitude, longitude: location.delivery.longitude });
                            return;
                        }

                        // Generate unique color for this driver
                        const driverColor = getRandomColor();
                        console.log(`لون السائق عند الفهرس ${index}:`, driverColor);

                        // Create marker for driver
                        const driverPosition = { lat: driverLat, lng: driverLng };
                        const driverMarker = new google.maps.Marker({
                            position: driverPosition,
                            map: map,
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                fillColor: driverColor,
                                fillOpacity: 1,
                                strokeWeight: 2,
                                strokeColor: 'white',
                                scale: 10
                            },
                            title: `${location.f_name} ${location.l_name}`
                        });

                        // Add CSS class for pulsing effect
                        driverMarker.set('labelClass', 'driver-dot');

                        // Add info window for driver
                        const driverInfoWindow = new google.maps.InfoWindow({
                            content: `<div style="font-weight: bold;">${location.f_name} ${location.l_name} (سائق)</div>`
                        });
                        driverInfoWindow.open(map, driverMarker);

                        // Extend bounds for driver marker
                        bounds.extend(driverPosition);
                        validMarkers = true;
                        console.log(`تم إنشاء علامة السائق عند الفهرس ${index}:`, {
                            name: `${location.f_name} ${location.l_name}`,
                            lat: driverLat,
                            lng: driverLng
                        });

                        // Handle billing location if it exists
                        if (location.billing && typeof location.billing === 'object' && location.billing.latitude != null && location.billing.longitude != null) {
                            // Ensure billing is an array to handle multiple clients
                            const billings = Array.isArray(location.billing) ? location.billing : [location.billing];

                            billings.forEach((billing, billingIndex) => {
                                const billingLat = sanitizeCoordinate(billing.latitude);
                                const billingLng = sanitizeCoordinate(billing.longitude);
                                console.log(`إحداثيات الفوترة المحولة عند الفهرس ${index}, عميل ${billingIndex}:`, { latitude: billingLat, longitude: billingLng });

                                if (billingLat === null || billingLng === null) {
                                    console.warn(`إحداثيات الفوترة غير صالحة عند الفهرس ${index}, عميل ${billingIndex}:`, { latitude: billing.latitude, longitude: billing.longitude });
                                    return;
                                }

                                // Skip invalid billing coordinates (e.g., 0.0, 0.0)
                                if (billingLat === 0 && billingLng === 0) {
                                    console.warn(`إحداثيات الفوترة غير صالحة (0.0, 0.0) عند الفهرس ${index}, عميل ${billingIndex}. يتم تخطي علامة الفوترة.`);
                                    return;
                                }

                                // Create marker for billing location
                                const billingPosition = { lat: billingLat, lng: billingLng };
                                const billingMarker = new google.maps.Marker({
                                    position: billingPosition,
                                    map: map,
                                    icon: {
                                        path: google.maps.SymbolPath.CIRCLE,
                                        fillColor: '#FFFF00', // Yellow for billing
                                        fillOpacity: 1,
                                        strokeWeight: 2,
                                        strokeColor: 'white',
                                        scale: 10
                                    },
                                    title: `عميل ${billingIndex + 1} لـ ${location.f_name} ${location.l_name}`
                                });

                                // Add CSS class for pulsing effect
                                billingMarker.set('labelClass', 'billing-dot');

                                // Add info window for billing
                                const billingInfoWindow = new google.maps.InfoWindow({
                                    content: `<div style="font-weight: bold;">عميل ${billingIndex + 1} لـ ${location.f_name} ${location.l_name}</div>`
                                });
                                billingInfoWindow.open(map, billingMarker);

                                // Draw line between driver and billing location
                                const line = new google.maps.Polyline({
                                    path: [driverPosition, billingPosition],
                                    geodesic: true,
                                    strokeColor: driverColor,
                                    strokeOpacity: 0.8, // Increased opacity for better visibility
                                    strokeWeight: 3 // Increased thickness for better visibility
                                });
                                line.setMap(map);
                                orderPolylines.push(line);

                                // Extend bounds for billing marker
                                bounds.extend(billingPosition);
                                orderMarkers.push(billingMarker);
                                console.log(`تم إنشاء علامة الفوترة عند الفهرس ${index}, عميل ${billingIndex}:`, {
                                    name: `عميل ${billingIndex + 1} لـ ${location.f_name} ${location.l_name}`,
                                    lat: billingLat,
                                    lng: billingLng
                                });
                            });
                        } else {
                            console.log(`لا توجد بيانات فوترة صالحة عند الفهرس ${index}.`);
                        }
                    });

                    // Adjust map to fit all markers
                    if (validMarkers) {
                        console.log('ضبط حدود الخريطة:', bounds.toJSON());
                        map.fitBounds(bounds);
                    } else {
                        console.warn('لا توجد علامات صالحة لضبط حدود الخريطة.');
                        alert('لا توجد علامات صالحة لضبط حدود الخريطة.');
                    }
                })
                .catch(error => {
                    console.error('خطأ في جلب المواقع من الـ API:', error);
                    alert('فشل تحميل مواقع التوصيل. تحقق من الكونسول للحصول على التفاصيل.');
                });
        }

        // Ensure map loads after the window is fully loaded
        window.onload = function() {
            console.log('تحميل النافذة، جاري فحص Google Maps API...');
            if (typeof google === 'undefined' || !google.maps) {
                console.error('فشل تحميل Google Maps API.');
                alert('فشل تحميل Google Maps API. تحقق من مفتاح API أو الاتصال بالإنترنت.');
                return;
            }
            console.log('Google Maps API تم تحميله بنجاح.');
            initMap();
        };
    </script>
@endpush
