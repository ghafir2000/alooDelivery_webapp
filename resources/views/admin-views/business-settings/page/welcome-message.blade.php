@extends('layouts.back-end.app')

@section('title', translate('welcome_message'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{dynamicAsset(path: 'public/assets/back-end/img/Pages.png')}}" width="20" alt="">
            {{translate('pages')}}
        </h2>
    </div>

    {{-- This will include your shared menu --}}
    @include('admin-views.business-settings.pages-inline-menu')

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{translate('welcome_message_settings')}}</h5>
        </div>
        <form action="{{ route('admin.business-settings.welcome-message-update') }}" method="post">
            @csrf
            <div class="card-body">

                {{-- The Toggle Switch --}}
                <div class="form-group d-flex justify-content-between align-items-center border rounded p-3">
                    <h5 class="mb-0">{{ translate('Show_Welcome_Message') }}</h5>
                    <label class="switcher">
                        <input type="checkbox" class="switcher_input" name="status" value="1"
                               id="welcome-status-checkbox" {{ $welcomeStatus == 1 ? 'checked' : '' }}>
                        <span class="switcher_control"></span>
                    </label>
                </div>

                {{-- The Message Text Area --}}
                <div class="form-group mt-4">
                    <label for="editor" class="title-color">{{ translate('Welcome_Message_Content') }}</label>
                    <textarea name="welcome_message" id="editor" class="form-control summernote">{{ $welcomeMessage }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button class="form-control btn--primary" type="submit">{{ translate('save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
    <script>
        'use strict';
        $(document).on('ready', function () {
            $('.summernote').summernote({
                'height': 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                ]
            });
        });
    </script>
@endpush
