@extends('layouts.back-end.app')

@section('title', translate('add_new_Vendor'))
@push('css_or_js')
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
    <style>
        #map {
            height: 300px;
            width: 100%;
            border-radius: 0.375rem;
            margin-top: 1rem;
        }
        .pac-container {
            z-index: 1050 !important; /* Ensure Places dropdown appears above other elements */
        }
    </style>
@endpush
@section('content')
<div class="content container-fluid main-card {{Session::get('direction')}}">
    <div class="mb-4">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{dynamicAsset(path: 'public/assets/back-end/img/add-new-seller.png')}}" class="mb-1" alt="">
            {{ translate('add_new_Vendor') }}
        </h2>
    </div>
    <form class="user" action="{{route('admin.vendors.add')}}" method="post" enctype="multipart/form-data" id="add-vendor-form">
        @csrf
        <div class="card">
            <div class="card-body">
                <input type="hidden" name="status" value="approved">
                <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2 border-bottom pb-3 mb-4 pl-4">
                    <img src="{{dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png')}}" class="mb-1" alt="">
                    {{ translate('vendor_information') }}
                </h5>
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="form-group">
                            <label for="exampleFirstName" class="title-color d-flex gap-1 align-items-center">{{translate('first_name')}}</label>
                            <input type="text" class="form-control form-control-user" id="exampleFirstName" name="f_name" value="{{old('f_name')}}" placeholder="{{translate('ex')}}: Jhone" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleLastName" class="title-color d-flex gap-1 align-items-center">{{translate('last_name')}}</label>
                            <input type="text" class="form-control form-control-user" id="exampleLastName" name="l_name" value="{{old('l_name')}}" placeholder="{{translate('ex')}}: Doe" required>
                        </div>
                        <div class="form-group">
                            <label class="title-color d-flex" for="exampleFormControlInput1">{{translate('phone')}}</label>
                            <div class="mb-3">
                                <input class="form-control form-control-user phone-input-with-country-picker"
                                       type="tel" id="exampleInputPhone" value="{{old('phone')}}"
                                       placeholder="{{ translate('enter_phone_number') }}" required>
                                <div class="">
                                    <input type="text" class="country-picker-phone-number w-50" value="{{old('phone')}}" name="phone" hidden readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <div class="d-flex justify-content-center">
                                <img class="upload-img-view" id="viewer"
                                    src="{{dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg')}}" alt="{{translate('banner_image')}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="title-color mb-2 d-flex gap-1 align-items-center">{{translate('vendor_Image')}} <span class="text-info">({{translate('ratio')}} {{translate('1')}}:{{translate('1')}})</span></div>
                            <div class="custom-file text-left">
                                <input typecom.googlemaps.com/maps/api/js?key=AIzaSyAiEveSJpdRGmDMqDle6xFz_0-qbUNGZ90&libraries=places"></script>
                                <input type="file" name="image" id="custom-file-upload" class="custom-file-input image-input"
                                       data-image-id="viewer"
                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class Place="custom-file-label" for="custom-file-upload">{{translate('upload_image')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <input type="hidden" name="status" value="approved">
                <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2 border-bottom pb-3 mb-4 pl-4">
                    <img src="{{dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png')}}" class="mb-1" alt="">
                    {{translate('account_information')}}
                </h5>
                <div class="row">
                    <div class="col-lg-4 form-group">
                        <label for="exampleInputEmail" class="title-color d-flex gap-1 align-items-center">{{translate('email')}}</label>
                        <input type="email" class="form-control form-control-user" id="exampleInputEmail" name="email" value="{{old('email')}}" placeholder="{{translate('ex').':'.'Jhone@company.com'}}" required>
                    </div>
                    <div class="col-lg-4 form-group">
                        <label for="user_password" class="title-color d-flex gap-1 align-items-center">
                            {{translate('password')}}
                            <span class="input-label-secondary cursor-pointer d-flex" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{translate('The_password_must_be_at_least_8_characters_long_and_contain_at_least_one_uppercase_letter').','.translate('_one_lowercase_letter').','.translate('_one_digit_').','.translate('_one_special_character').','.translate('_and_no_spaces').'.'}}">
                                <img alt="" width="16" src={{dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}>
                            </span>
                        </label>
                        <div class="input-group input-group-merge">
                            <input type="password" class="js-toggle-password form-control password-check"
                                   name="password" required id="user_password" minlength="8"
                                   placeholder="{{ translate('password_minimum_8_characters') }}"
                                   data-hs-toggle-password-options='{
                                                         "target": "#changePassTarget",
                                                        "defaultClass": "tio-hidden-outlined",
                                                        "showClass": "tio-visible-outlined",
                                                        "classChangeTarget": "#changePassIcon"
                                                }'>
                            <div id="changePassTarget" class="input-group-append">
                                <a class="input-group-text" href="javascript:">
                                    <i id="changePassIcon" class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </div>
                        <span class="text-danger mx-1 password-error"></span>
                    </div>
                    <div class="col-lg-4 form-group">
                        <label for="confirm_password" class="title-color d-flex gap-1 align-items-center">{{translate('confirm_password')}}</label>
                        <div class="input-group input-group-merge">
                            <input type="password" class="js-toggle-password form-control"
                                   name="confirm_password" required id="confirm_password"
                                   placeholder="{{ translate('confirm_password') }}"
                                   data-hs-toggle-password-options='{
                                                         "target": "#changeConfirmPassTarget",
                                                        "defaultClass": "tio-hidden-outlined",
                                                        "showClass": "tio-visible-outlined",
                                                        "classChangeTarget": "#changeConfirmPassIcon"
                                                }'>
                            <div id="changeConfirmPassTarget" class="input-group-append">
                                <a class="input-group-text" href="javascript:">
                                    <i id="changeConfirmPassIcon" class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </div>
                        <div class="pass invalid-feedback">{{translate('repeat_password_not_match').'.'}}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2 border-bottom pb-3 mb-4 pl-4">
                    <img src="{{dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png')}}" class="mb-1" alt="">
                    {{translate('shop_information')}}
                </h5>

                <div class="row">
                    <div class="col-lg-6 form-group">
                        <label for="shop_name" class="title-color d-flex gap-1 align-items-center">{{translate('shop_name')}}</label>
                        <input type="text" class="form-control form-control-user" id="shop_name" name="shop_name" placeholder="{{translate('ex').':'.translate('Jhon')}}" value="{{old('shop_name')}}" required>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="shop_address" class="title-color d-flex gap-1 align-items-center">{{translate('shop_address')}}</label>
                        <textarea name="shop_address" class="form-control text-area-max" id="shop_address" rows="1" placeholder="{{translate('ex').':'.translate('doe')}}">{{old('shop_address')}}</textarea>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="search_address" class="title-color d-flex gap-1 align-items-center">{{translate('search_location')}}</label>
                        <input type="text" class="form-control form-control-user" id="search_address" placeholder="{{translate('search_for_location')}}">
                        <input type="hidden" name="latitude" id="latitude" value="{{old('latitude')}}">
                        <input type="hidden" name="longitude" id="longitude" value="{{old('longitude')}}">
                        <div id="map"></div>
                    </div>
                    <div class="col-lg-6 form-group">
                        <div class="d-flex justify-content-center">
                            <img class="upload-img-view" id="viewerLogo"
                                src="{{dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg')}}" alt="{{translate('banner_image')}}"/>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex gap-1 align-items-center title-color mb-2">
                                {{translate('shop_logo')}}
                                <span class="text-info">({{translate('ratio').' '.'1:1'}})</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" name="logo" id="logo-upload" class="custom-file-input image-input"
                                       data-image-id="viewerLogo"
                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="logo-upload">{{translate('upload_logo')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 form-group">
                        <div class="d-flex justify-content-center">
                            <img class="upload-img-view upload-img-view__banner" id="viewerBanner"
                                    src="{{dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg')}}" alt="{{translate('banner_image')}}"/>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex gap-1 align-items-center title-color mb-2">
                                {{translate('shop_banner')}}
                                <span class="text-info">{{ THEME_RATIO[theme_root_path()]['Store cover Image'] }}</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" name="banner" id="banner-upload" class="custom-file-input image-input"
                                       data-image-id="viewerBanner"
                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label text-capitalize" for="banner-upload">{{translate('upload_Banner')}}</label>
                            </div>
                        </div>
                    </div>

                    @if(theme_root_path() == "theme_aster")
                    <div class="col-lg-6 form-group">
                        <div class="d-flex justify-content-center">
                            <img class="upload-img-view upload-img-view__banner" id="viewerBottomBanner"
                                    src="{{dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg')}}" alt="{{translate('banner_image')}}"/>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex gap-1 align-items-center title-color mb-2">
                                {{translate('shop_secondary_banner')}}
                                <span class="text-info">{{ THEME_RATIO[theme_root_path()]['Store Banner Image'] }}</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" name="bottom_banner" id="bottom-banner-upload" class="custom-file-input image-input"
                                       data-image-id="viewerBottomBanner"
                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label text-capitalize" for="bottom-banner-upload">{{translate('upload_bottom_banner')}}</label>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="col-lg-6 form-group mt-4">
                        <label for="shop_type" class="title-color d-flex gap-1 align-items-center">{{translate('shop_type')}}</label>
                        <select class="form-control form-control-user" id="shop_type" name="type" required>
                            <option value="store">{{translate('store')}}</option>
                            <option value="restaurant">{{translate('restaurant')}}</option>
                        </select>
                    </div>
                    <div class="col-lg-6 form-group mt-4">
                        <label for="category_id" class="title-color d-flex gap-1 align-items-center">{{translate('category')}}</label>
                        <select class="form-control form-control-user" id="category_id" name="category_id" required>
                            <option value="" disabled selected>{{translate('select_category')}}</option>
                            <!-- Categories will be populated via JavaScript from API -->
                        </select>
                    </div>
                </div>


                
                <div class="d-flex align-items-center justify-content-end gap-10">
                    <input type="hidden" name="from_submit" value="admin">
                    <button type="reset" class="btn btn-secondary reset-button">{{translate('reset')}} </button>
                    <button type="button" class="btn btn--primary btn-user form-submit" data-form-id="add-vendor-form" data-redirect-route="{{route('admin.vendors.vendor-list')}}"
                            data-message="{{translate('want_to_add_this_vendor').'?'}}">{{translate('submit')}}</button>
                </div>
            </div>

            <!-- إضافة أزرار جديدة للمتاجر الخارجية -->
            <div class="mt-4 d-flex justify-content-end gap-3">
                <a href="{{ route('admin.vendors.external-shops.create') }}" class="btn btn-success">إضافة متجر خارجي</a>
                <a href="{{ route('admin.vendors.external-shops.index') }}" class="btn btn-info">استعراض المتاجر الخارجية</a>
            </div>

        </div>
    </div>
</form>
</div>
@endsection

// ... existing code ...


@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/country-picker-init.js') }}"></script>
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/admin/vendor.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiEveSJpdRGmDMqDle6xFz_0-qbUNGZ90&libraries=places"></script>
    <script>
        $(document).ready(function () {
            // Fetch categories from API and populate the select
            $.ajax({
                url: "{{ url('admin/vendors/category-list') }}",
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    const select = $('#category_id');
                    data.forEach(category => {
                        select.append(`<option value="${category.id}">${category.name}</option>`);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching categories:', error);
                    $('#category_id').append('<option disabled>Error loading categories</option>');
                }
            });

            // Initialize Google Map
            let map, marker;
            const mapElement = document.getElementById('map');
            const searchInput = document.getElementById('search_address');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');

            function initMap() {
                // Default coordinates (e.g., centered on a generic location)
                const defaultLocation = { lat: 24.7136, lng: 46.6753 }; // Example: Riyadh, Saudi Arabia
                map = new google.maps.Map(mapElement, {
                    center: defaultLocation,
                    zoom: 12,
                });

                marker = new google.maps.Marker({
                    map: map,
                    position: defaultLocation,
                    draggable: true,
                });

                // Update coordinates when marker is dragged
                google.maps.event.addListener(marker, 'dragend', function () {
                    const position = marker.getPosition();
                    latitudeInput.value = position.lat();
                    longitudeInput.value = position.lng();
                });

                // Initialize Places Autocomplete
                const autocomplete = new google.maps.places.Autocomplete(searchInput);
                autocomplete.bindTo('bounds', map);

                autocomplete.addListener('place_changed', function () {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) {
                        console.error('No details available for input: ' + place.name);
                        return;
                    }

                    map.setCenter(place.geometry.location);
                    marker.setPosition(place.geometry.location);
                    latitudeInput.value = place.geometry.location.lat();
                    longitudeInput.value = place.geometry.location.lng();
                });
            }


            initMap();
        });
    </script>
@endpush
