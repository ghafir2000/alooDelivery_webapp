<?php

namespace App\Http\Requests\Admin; // Assuming the namespace

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the request for updating vendor/seller settings.
 *
 * @property int $commission
 * @property bool $seller_pos
 * @property bool $seller_registration
 * @property float $minimum_order_amount_by_seller
 * @property bool $new_product_approval
 * @property bool $product_wise_shipping_cost_approval
 * @property bool $vendor_review_reply_status
 * @property string $vendor_forgot_password_method
 */
class VendorSettingsRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        // Typically, you'd add authorization logic here, e.g., check if the user is an admin.
        // Returning true is fine for now but consider adding auth checks.
        return true;
    }

    public function rules(): array
    {
        return [
            // 'commission' should be a number, not negative.
            'commission' => 'required|numeric|min:0',

            // These are on/off settings, so 'boolean' is the correct rule.
            // Laravel correctly handles '1', '0', true, false, 'on', 'off'.
            'seller_pos' => 'boolean',
            'seller_registration' => 'boolean',

            // This should be a numeric value, not negative.
            'minimum_order_amount_by_seller' => 'numeric|min:0',

            // More boolean (on/off) settings.
            'new_product_approval' => 'boolean',
            'product_wise_shipping_cost_approval' => 'boolean',
            'vendor_review_reply_status' => 'boolean',

            // Use the 'in' rule to specify exactly which values are allowed.
            // This is more secure than just checking for a string.
            'vendor_forgot_password_method' => 'string|in:phone,email',
        ];
    }

    public function messages(): array
    {
        // This array was syntactically incorrect. It is now fixed.
        // It returns a single, valid array of custom error messages.
        return [
            'commission.required' => translate('the_commission_value_field_is_required'),
            'commission.numeric' => translate('the_commission_value_must_be_a_number'),
            'commission.min' => translate('the_commission_value_cannot_be_negative'),

            'seller_pos.required' => translate('the_seller_pos_setting_is_required'),
            'seller_pos.boolean' => translate('the_seller_pos_setting_must_be_on_or_off'),

            'seller_registration.required' => translate('the_seller_registration_setting_is_required'),
            'seller_registration.boolean' => translate('the_seller_registration_setting_must_be_on_or_off'),

            'minimum_order_amount_by_seller.required' => translate('the_minimum_order_amount_field_is_required'),
            'minimum_order_amount_by_seller.numeric' => translate('the_minimum_order_amount_must_be_a_number'),
            'minimum_order_amount_by_seller.min' => translate('the_minimum_order_amount_cannot_be_negative'),

            'new_product_approval.required' => translate('the_new_product_approval_setting_is_required'),
            'new_product_approval.boolean' => translate('the_new_product_approval_setting_must_be_on_or_off'),

            'product_wise_shipping_cost_approval.required' => translate('the_product_wise_shipping_cost_approval_setting_is_required'),
            'product_wise_shipping_cost_approval.boolean' => translate('the_product_wise_shipping_cost_approval_setting_must_be_on_or_off'),

            'vendor_review_reply_status.required' => translate('the_vendor_review_reply_status_setting_is_required'),
            'vendor_review_reply_status.boolean' => translate('the_vendor_review_reply_status_setting_must_be_on_or_off'),

            'vendor_forgot_password_method.required' => translate('the_vendor_forgot_password_method_is_required'),
            'vendor_forgot_password_method.in' => translate('the_vendor_forgot_password_method_must_be_either_phone_or_email'),
        ];
    }
}