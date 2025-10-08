@extends('layouts.back-end.partials._blank-auth')

@section('title',  translate('Admin_OTP_Verification'))

@section('content')
    {{-- Main Form pointing to the Admin OTP Verify Route --}}
    <form class="needs-validation otp-form" action="{{ route('admin.auth.otp.verify') }}" method="post">
        @csrf
        {{-- Hidden input to pass the admin ID --}}
        <input type="hidden" name="id" value="{{ $admin->id }}">

        <div class="text-center">
            <img class="mb-4" src="{{asset('/public/assets/front-end/img/icons/otp-login-icon.svg')}}" alt="Image Description" style="width: 60px;">
            <h3 class="mb-1">{{ translate('OTP_Verification') }}</h3>
            <p class="text-muted">{{ translate('A verification code has been sent to your phone number.') }}</p>
            <span class="d-block mb-3">{{ $admin->phone }}</span>
        </div>

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <div class="d-flex gap-2 gap-sm-3 align-items-end justify-content-center my-4">
                <input class="otp-field form-control form-control-lg text-center" type="text" name="opt-field[]" maxlength="1" autocomplete="off" required>
                <input class="otp-field form-control form-control-lg text-center" type="text" name="opt-field[]" maxlength="1" autocomplete="off" required>
                <input class="otp-field form-control form-control-lg text-center" type="text" name="opt-field[]" maxlength="1" autocomplete="off" required>
                <input class="otp-field form-control form-control-lg text-center" type="text" name="opt-field[]" maxlength="1" autocomplete="off" required>
                <input class="otp-field form-control form-control-lg text-center" type="text" name="opt-field[]" maxlength="1" autocomplete="off" required>
                <input class="otp-field form-control form-control-lg text-center" type="text" name="opt-field[]" maxlength="1" autocomplete="off" required>
            </div>
            {{-- Hidden field to combine the OTP digits --}}
            <input class="otp-value" type="hidden" name="otp">
        </div>

        <button class="btn btn-primary btn-block" type="submit">{{ translate('Verify') }}</button>
    </form>

    <div class="text-center mt-3">
        <span class="fs-14 mr-1">{{ translate("Didn't receive the code?") }}</span>
        {{-- Resend OTP requires a separate form --}}
        <form action="{{ route('admin.auth.otp.resend')}}" method="post" class="d-inline-block">
            @csrf
            <input type="hidden" name="id" value="{{ $admin->id }}">
            <button class="btn btn-link p-0" type="submit">
                {{ translate('Resend_OTP') }}
            </button>
        </form>
    </div>
@endsection

@push('script')
    {{-- This JavaScript is perfect for handling the OTP inputs. --}}
    <script>
        $(document).ready(function () {
            $(".otp-form .otp-field:first").focus();

            let otp_fields = $(".otp-form .otp-field"),
                otp_value_field = $(".otp-form .otp-value");

            otp_fields
                .on("input", function (e) {
                    $(this).val($(this).val().replace(/[^0-9]/g, ""));
                    let opt_value = "";
                    otp_fields.each(function () {
                        let field_value = $(this).val();
                        if (field_value !== "") opt_value += field_value;
                    });
                    otp_value_field.val(opt_value);
                })
                .on("keyup", function (e) {
                    let key = e.keyCode || e.charCode;
                    if (key === 8 || key === 46 || key === 37 || key === 40) {
                        $(this).prev().focus();
                    } else if (key === 38 || key === 39 || $(this).val() !== "") {
                        $(this).next().focus();
                    }
                })
                .on("paste", function (e) {
                    let paste_data = e.originalEvent.clipboardData.getData("text");
                    let paste_data_splitted = paste_data.split("");
                    $.each(paste_data_splitted, function (index, value) {
                        otp_fields.eq(index).val(value);
                    });
                    otp_fields.eq(paste_data_splitted.length).focus();
                });
        });
    </script>
@endpush