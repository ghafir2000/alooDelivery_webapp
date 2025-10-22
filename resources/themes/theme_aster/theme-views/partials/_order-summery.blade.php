@php
    use App\Utils\CartManager;
    use App\Utils\Helpers;
@endphp
<div class="col-lg-4">
    <div class="card text-dark sticky-top-80">
        <div class="card-body px-sm-4 d-flex flex-column gap-2">
            @php
                // PHP calculations remain the same...
                $product_price_total = 0;
                $total_tax = 0;
                $total_shipping_cost = 0;
                $total_discount_on_product = 0;
                $cart = CartManager::getCartListQuery(type: 'checked');
                $cart_group_ids = CartManager::get_cart_group_ids(type: 'checked');
                $shipping_cost = CartManager::get_shipping_cost(type: 'checked');
                $get_shipping_cost_saved_for_free_delivery = CartManager::getShippingCostSavedForFreeDelivery(type: 'checked');
                if ($cart->count() > 0) {
                    foreach ($cart as $cartItem) {
                        $product_price_total += $cartItem['price'] * $cartItem['quantity'];
                        $total_tax += $cartItem['tax_model'] == 'exclude' ? ($cartItem['tax'] * $cartItem['quantity']) : 0;
                        $total_discount_on_product += $cartItem['discount'] * $cartItem['quantity'];
                    }
                    if (session()->missing('coupon_type') || session('coupon_type') != 'free_delivery') {
                        $total_shipping_cost = $shipping_cost - $get_shipping_cost_saved_for_free_delivery;
                    } else {
                        $total_shipping_cost = $shipping_cost;
                    }
                }
                $coupon_discount = session()->has('coupon_discount') ? session('coupon_discount') : 0;
                $order_wise_shipping_discount = CartManager::order_wise_shipping_discount();
                $sub_total = $product_price_total - $total_discount_on_product;
                $grand_total = $sub_total + $total_tax + $total_shipping_cost - $coupon_discount - $order_wise_shipping_discount;
            @endphp

            @if(!empty($cart_group_ids))
                <input type="hidden" id="cart-group-id-for-shipping" value="{{ $cart_group_ids[0] }}">
            @endif

            <div class="d-flex mb-3">
                <h5 class="text-capitalize">{{ translate('order_summary') }}</h5>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>{{ translate('item_price') }}</div>
                <div>{{ webCurrencyConverter($product_price_total) }}</div>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="text-capitalize">{{ translate('product_discount') }}</div>
                <div>{{ webCurrencyConverter($total_discount_on_product) }}</div>
            </div>
            
            {{-- ADDED ID HERE --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>{{ translate('sub_total') }}</div>
                <div id="sub-total-display">{{ webCurrencyConverter($sub_total) }}</div>
            </div>
            
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>{{ translate('shipping_per_KM') }}</div>
                <div id="shipping-type-cost">{{ webCurrencyConverter($total_shipping_cost) }}</div>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>{{ translate('distance_in_KM') }}</div>
                <div id="distance">{{ translate('will_be_calculated') }}</div>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>{{ translate('shipping_total') }}</div>
                <div id="shipping-cost-display">{{ translate('will_be_calculated') }}</div>
            </div>

            {{-- ADDED ID HERE --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>{{ translate('tax') }}</div>
                <div id="tax-display">{{ webCurrencyConverter($total_tax) }}</div>
            </div>

            @if(auth('customer')->check() && session()->has('coupon_discount'))
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>{{ translate('coupon_discount') }}</div>
                    {{-- ADDED ID HERE --}}
                    <div class="text-primary" id="coupon-discount-display">
                         -{{ webCurrencyConverter($coupon_discount + $order_wise_shipping_discount) }}
                    </div>
                </div>
            @endif

            @if(auth('customer')->check() && !session()->has('coupon_discount'))
                {{-- Coupon Form remains the same --}}
                <form class="needs-validation" action="{{ route('coupon.apply') }}" method="post" id="submit-coupon-code">
                    @csrf
                    <div class="form-group my-3">
                        <label for="promo-code" class="fw-semibold">{{ translate('Promo_Code') }}</label>
                        <div class="form-control focus-border pe-1 rounded d-flex align-items-center">
                            <input type="text" name="code" id="promo-code" class="w-100 text-dark bg-transparent border-0 focus-input" placeholder="{{ translate('write_coupon_code_here') }}" required>
                            <button class="btn btn-primary text-nowrap" id="coupon-code-apply">{{ translate('apply') }}</button>
                        </div>
                    </div>
                </form>
            @endif

            <hr class="my-2">
            
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="text-capitalize">{{ translate('total') }}</h4>
                <h2 class="text-primary" id="grand-total-display">{{ webCurrencyConverter($grand_total) }}</h2>
            </div>
            
            {{-- Buttons remain the same --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
                <a href="{{ route('home') }}" class="btn-link text-primary text-capitalize user-select-none">
                    <i class="bi bi-chevron-double-left fs-10"></i> {{ translate('continue_shopping') }}
                </a>
                @if (str_contains(request()->url(), 'checkout-payment'))
                    <button class="btn btn-primary text-capitalize custom-disabled" id="proceed-to-payment-action" type="button">
                        {{translate('proceed_to_payment')}}
                    </button>
                @else
                    <button class="btn btn-primary text-capitalize {{$cart->count() <= 0 ? 'custom-disabled' : ''}}" id="proceed-to-next-action" type="button">
                        {{translate('proceed_to_next')}}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>