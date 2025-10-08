@php
    use App\Enums\ViewPaths\Admin\FirebaseOTPVerification;
    use App\Enums\ViewPaths\Admin\Recaptcha;
    use App\Enums\ViewPaths\Admin\SMSModule;
    use App\Enums\ViewPaths\Admin\SocialMediaChat;
    use App\Enums\ViewPaths\Admin\SocialLoginSettings;
    use App\Enums\ViewPaths\Admin\BusinessSettings;
    use App\Enums\ViewPaths\Admin\StorageConnectionSettings;
@endphp
<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        
        {{-- SMS Module --}}
        <li class="{{ Request::is('admin/business-settings/sms-module') ? 'active' : '' }}">
            <a class="text-capitalize" href="{{ route('admin.business-settings.sms-module') }}">
                {{ translate('sms_module') }}
            </a>
        </li>
        
        {{-- Mail Config --}}
        <li class="{{ Request::is('admin/business-settings/mail') ? 'active' : '' }}">
            <a class="text-capitalize" href="{{ route('admin.business-settings.mail.index') }}">
                {{ translate('mail_config') }}
            </a>
        </li>
        
        {{-- Google Map APIs --}}
        <li class="{{ Request::is('admin/business-settings/map-api') ? 'active' : '' }}">
            <a class="text-capitalize" href="{{ route('admin.business-settings.map-api') }}">
                {{ translate('google_map_APIs') }}
            </a>
        </li>

        {{-- Social Media Login --}}
        <li class="{{ Request::is('admin/social-login/view') ? 'active' : '' }}">
            <a class="text-capitalize" href="{{ route('admin.social-login.view') }}">
                {{ translate('social_media_login') }}
            </a>
        </li>
        
        {{-- reCAPTCHA --}}
        <li class="{{ Request::is('admin/business-settings/recaptcha*') ? 'active' : '' }}">
            <a class="text-capitalize" href="{{ route('admin.business-settings.captcha') }}">
                {{ translate('reCAPTCHA') }}
            </a>
        </li>

        {{-- Social Media Chat --}}
        <li class="{{ Request::is('admin/social-media-chat/view') ? 'active' : '' }}">
            <a class="text-capitalize" href="{{ route('admin.social-media-chat.view') }}">
                {{ translate('social_media_chat') }}
            </a>
        </li>

    </ul>
</div>