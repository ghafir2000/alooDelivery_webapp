{{-- resources/views/admin-views/business-settings/page/feedback-message.blade.php --}}

@extends('layouts.back-end.app')

@section('title', translate('WhatsApp_Feedback_Message'))

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
            <h5 class="mb-0">{{translate('WhatsApp_Feedback_Message_Settings')}}</h5>
        </div>
        <form action="{{ route('admin.business-settings.feedback-message-update') }}" method="post">
            @csrf
            <div class="card-body">

                {{-- The Toggle Switch --}}
                <div class="form-group d-flex justify-content-between align-items-center border rounded p-3">
                    <h5 class="mb-0">{{ translate('Send_Feedback_Message_After_Delivery') }}</h5>
                    <label class="switcher">
                        <input type="checkbox" class="switcher_input" name="status" value="1"
                               id="feedback-status-checkbox" {{ $feedbackStatus == 1 ? 'checked' : '' }}>
                        <span class="switcher_control"></span>
                    </label>
                </div>

                {{-- The Message Text Area --}}
                <div class="form-group mt-4">
                    <label for="editor" class="title-color">{{ translate('Feedback_Message_Content') }}</label>
                    <textarea name="feedback_message" id="editor" class="form-control summernote">{{ $feedbackMessage }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn--primary" type="submit">{{ translate('save') }}</button>
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