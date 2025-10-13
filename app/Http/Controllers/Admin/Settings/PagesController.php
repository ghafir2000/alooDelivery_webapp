<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Contracts\Repositories\BusinessSettingRepositoryInterface;
use App\Enums\ViewPaths\Admin\Pages;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AboutUsRequest;
use App\Http\Requests\Admin\PageUpdateRequest;
use App\Http\Requests\Admin\PrivacyPolicyRequest;
use App\Http\Requests\Admin\TermsConditionRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PagesController extends BaseController
{

    public function __construct(
        private readonly BusinessSettingRepositoryInterface $businessSettingRepo,
    ){}

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View
    {
        return $this->getTermsConditionView();
    }

    public function getTermsConditionView(): View
    {
        $terms_condition = $this->businessSettingRepo->getFirstWhere(params: ['type'=>'terms_condition']);
        return view(Pages::TERMS_CONDITION[VIEW], compact('terms_condition'));
    }

    public function updateTermsCondition(TermsConditionRequest $request): RedirectResponse
    {
        $this->businessSettingRepo->updateWhere(params: ['type'=>'terms_condition'], data: ['value' => $request['value']]);
        clearWebConfigCacheKeys();
        Toastr::success(translate('Terms_and_Condition_Updated_successfully'));
        return back();
    }

    public function getFeedbackMessageView(): View
    {
         // 1. Fetch the single 'feedback_settings' row from the database.
        $feedbackSettings = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'feedback_settings']);

        
        // 2. Decode the JSON value. If it doesn't exist or is invalid, default to an empty array.
        $settingsData = json_decode($feedbackSettings->value,true) ?: [];
        // dd($settingsData['status']);

        // 3. Extract the status and message from the decoded array, providing default values.
        $feedbackStatus = $settingsData['status']?? 0; // Default to 0 (off)
        $feedbackMessage = $settingsData['message']?? ''; // Default to an empty string
        
        return view(Pages::FEEDBACK_MESSAGE[VIEW], [
            'feedbackStatus' => $feedbackStatus, // Default to 0 (off) if not set
            'feedbackMessage' => $feedbackMessage, // Default to empty string
        ]);
    }

    public function updateFeedbackMessage(Request $request): RedirectResponse
    {
        // Use updateOrInsert to create the setting if it doesn't exist, or update it if it does.
        $value = json_encode([
            'status' => $request->get('status', 0),
            'message' => $request->get('feedback_message', ''),
        ]);

        $this->businessSettingRepo->updateOrInsert(
            type: 'feedback_settings',
            value: $value
        );

        Toastr::success(translate('feedback_message_settings_updated_successfully'));
        return back();
    }
    
    
    public function getWelcomeMessageView(): View
    {
        // 1. Fetch the single 'welcome_settings' row from the database.
        $welcomeSettings = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'welcome_settings']);

        // 2. Decode the JSON value. If it doesn't exist or is invalid, default to an empty array.
        $settingsData = json_decode($welcomeSettings->value,true) ?: [];

        // 3. Extract the status and message from the decoded array, providing default values.
        $welcomeStatus = $settingsData['status']?? 0; // Default to 0 (off)
        $welcomeMessage = $settingsData['message']?? ''; // Default to an empty string

        return view(Pages::WELCOME_MESSAGE[VIEW], [
            'welcomeStatus' => $welcomeStatus, // Default to 0 (off) if not set
            'welcomeMessage' => $welcomeMessage, // Default to empty string
        ]);
    }

    public function updateWelcomeMessage(Request $request): RedirectResponse
    {
        // Use updateOrInsert to create the setting if it doesn't exist, or update it if it does.
        $value = json_encode([
            'status' => $request->get('status', 0),
            'message' => $request->get('welcome_message', ''),
        ]);

        $this->businessSettingRepo->updateOrInsert(
            type: 'welcome_settings',
            value: $value
        );

        Toastr::success(translate('welcome_message_settings_updated_successfully'));
        return back();
    }

    public function getPrivacyPolicyView(): View
    {
        $privacy_policy = $this->businessSettingRepo->getFirstWhere(params: ['type'=>'privacy_policy']);
        return view(Pages::PRIVACY_POLICY[VIEW], compact('privacy_policy'));
    }

    public function updatePrivacyPolicy(PrivacyPolicyRequest $request): RedirectResponse
    {
        $this->businessSettingRepo->updateWhere(params: ['type'=>'privacy_policy'], data: ['value' => $request['value']]);
        Toastr::success(translate('Privacy_policy_Updated_successfully'));
        return back();
    }


    public function getPageView($page): View|RedirectResponse
    {
        $pages = ['refund-policy', 'return-policy', 'cancellation-policy', 'shipping-policy'];
        if (in_array($page, $pages)) {
            $data = $this->businessSettingRepo->getFirstWhere(params: ['type' => $page]);
            return view(Pages::VIEW[VIEW], compact('page', 'data'));
        }
        Toastr::error(translate('invalid_page'));
        return back();
    }

    public function updatePage(PageUpdateRequest $request, $page): RedirectResponse
    {
        $pages = ['refund-policy', 'return-policy', 'cancellation-policy', 'shipping-policy'];
        if (in_array($page, $pages)) {
            $value = json_encode(['status' => $request->get('status', 0), 'content' => $request['value']]);
            $this->businessSettingRepo->updateOrInsert(type: $page, value: $value);
            Toastr::success(translate('updated_successfully'));
        } else {
            Toastr::error(translate('invalid_page'));
        }
        return back();
    }

    public function getAboutUsView(): View
    {
        $pageData = $this->businessSettingRepo->getFirstWhere(params: ['type' => 'about_us']);
        return view(Pages::ABOUT_US[VIEW], compact('pageData'));
    }

    public function updateAboutUs(AboutUsRequest $request): RedirectResponse
    {
        $this->businessSettingRepo->updateWhere(params: ['type'=>'about_us'], data: ['value' => $request['about_us']]);
        Toastr::success(translate('about_us_updated_successfully'));
        return back();
    }


}
