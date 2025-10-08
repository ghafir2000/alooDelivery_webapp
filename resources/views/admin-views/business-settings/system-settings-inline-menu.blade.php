@php
    use App\Enums\ViewPaths\Admin\BusinessSettings;
    use App\Enums\ViewPaths\Admin\Currency;
    use App\Enums\ViewPaths\Admin\DatabaseSetting;
    use App\Enums\ViewPaths\Admin\EnvironmentSettings;
    use App\Enums\ViewPaths\Admin\SiteMap;
    use App\Enums\ViewPaths\Admin\SoftwareUpdate;
@endphp
<div class="inline-page-menu my-4">
    <ul class="list-unstyled">


        <li class="{{ Request::is('admin/business-settings/web-config/'.BusinessSettings::APP_SETTINGS[URI]) ?'active':'' }}">
            <a href="{{route('admin.business-settings.web-config.app-settings')}}">{{translate('app_Settings')}}</a>
        </li>


        <li class="{{ Request::is('admin/business-settings/language') ?'active':'' }}">
            <a href="{{route('admin.business-settings.language.index')}}">{{translate('language')}}</a>
        </li>
        <li class="{{ Request::is('admin/currency/'.Currency::LIST[URI]) ?'active':'' }}">
            <a href="{{route('admin.currency.view')}}">{{translate('Currency')}}</a>
        </li>
     
    </ul>
</div>
