"use strict";


$(document).ready(function () {
    initializeMapsWhenReady();

    // --- STEP 1: Handle the DEFAULT SELECTED ADDRESS ---
    let firstAddressRadio = $('[name="shipping_address_id"]').first();
    if (firstAddressRadio.length) {
        firstAddressRadio.prop('checked', true);
        let cardBody = firstAddressRadio.closest('.card-header').siblings('.card-body');
        populateShippingFormFromAddress(cardBody);
        calculateShippingForSelectedAddress();

    }

    // --- STEP 2: Handle the DEFAULT SHIPPING METHOD ---
    let firstShippingMethodRadio = $('[name="shipping_method_id"]').first();
    if (firstShippingMethodRadio.length) {
        firstShippingMethodRadio.prop('checked', true);
        // CRITICAL FIX: Get the value DIRECTLY from the element we just found.
        selectedShippingMethodId = firstShippingMethodRadio.val();
    } else {
        // Fallback for the select dropdown
        let firstShippingOption = $('.set-shipping-onchange option').eq(1);
        if (firstShippingOption.length) {
            firstShippingOption.prop('selected', true);
            // CRITICAL FIX: Also get the value DIRECTLY here.
            selectedShippingMethodId = firstShippingOption.val();
        }
    }
    // This will now print the correct value (e.g., "9")
    console.log(`Default shipping method ID set to: ${selectedShippingMethodId}`);


    // This check is still valid for billing
    if ($('[name="billing_method_id"]').prop('checked')) {
        let cardBody = $('[name="billing_method_id"]:checked').parents('.card-header').siblings('.card-body');
        billingMethodSelect(cardBody);
    }
    
    
    try {
        initializePhoneInput(".phone-input-with-country-picker-shipping", ".country-picker-phone-number-shipping");
        initializePhoneInput(".phone-input-with-country-picker-billing", ".country-picker-phone-number-billing");
    } catch (error) {}
});

function calculateShippingForSelectedAddress() {
    // Find the currently checked saved address radio button
    let selectedAddressRadio = $('input[name="shipping_address_id"]:checked');
    console.log(" calculation for saved address...");


    // Proceed only if a radio button is actually selected
    if (selectedAddressRadio.length > 0) {
        // Find the parent 'card-body' to access the hidden lat/lon spans
        let cardBody = selectedAddressRadio.closest('.card-header').siblings('.card-body');

        // Extract the latitude and longitude
        let lat = cardBody.find('.shipping-latitude').text().trim();
        let lon = cardBody.find('.shipping-longitude').text().trim();

        // Get the other required IDs from the hidden inputs on the page
        let shippingMethodId = $('#chosen-shipping-method-id').val();
        let cartGroupId = $('#cart-group-id-for-shipping').val();

        // Ensure all four pieces of information are available before making the API call
        if (lat && lon && shippingMethodId && cartGroupId) {
            console.log("Triggering shipping calculation for address...");
            getDynamicShippingCost(lat, lon, shippingMethodId, cartGroupId);
        } else {
            console.warn("Could not calculate shipping: Missing one or more required IDs.", {
                lat: lat,
                lon: lon,
                methodId: shippingMethodId,
                cartGroupId: cartGroupId
            });
        }
    }
}



// Renamed for clarity and corrected to remove the unnecessary line.
function populateShippingFormFromAddress(cardBody) {
    let updateThisAddress = $('.customize-text').data('update-this-address');
    // REMOVED: The unnecessary 'let shippingMethodId = ...' line is gone.
    let shippingPerson = cardBody.find('.shipping-contact-person').text();
    let shippingPhone = cardBody.find('.shipping-contact-phone').text();
    let shippingAddress = cardBody.find('.shipping-contact-address').text();
    let shippingCity = cardBody.find('.shipping-contact-city').text();
    let shippingZip = cardBody.find('.shipping-contact-zip').text();
    let shippingCountry = cardBody.find('.shipping-contact-country').text();
    let shippingContactAddressType = cardBody.find('.shipping-contact-address-type').text();
    let updateAddress = `
                <input type="checkbox" name="update_address" id="update-address">${updateThisAddress}`;

    $('#name').val(shippingPerson);
    $('#phoneNumber').val(shippingPhone);
    $('#phoneNumber').keypress();
    $('#address').val(shippingAddress);
    $('#city').val(shippingCity);
    $('#zip').val(shippingZip);
    $('#select2-zip-container').text(shippingZip);
    $('#country').val(shippingCountry);
    $('#select2-country-container').text(shippingCountry);
    $('#address-type').val(shippingContactAddressType);
    $('#save-address-label').html(updateAddress);
}

$('[name="billing_method_id"]').on('change', function () {
    let cardBody = $(this).parents('.card-header').siblings('.card-body')
    billingMethodSelect(cardBody);
})

function billingMethodSelect(cardBody) {
    let updateThisAddress = $('.customize-text').data('update-this-address');
    let billingMethodId = $('[name="billing_method_id"]:checked').val();
    let billingPerson = cardBody.find('.billing-contact-name').text();
    let billingPhone = cardBody.find('.billing-contact-phone').text();
    let billingAddress = cardBody.find('.billing-contact-address').text();
    let billingCity = cardBody.find('.billing-contact-city').text();
    let billingZip = cardBody.find('.billing-contact-zip').text();
    let billingCountry = cardBody.find('.billing-contact-country').text();
    let billingContactAddressType = cardBody.find('.billing-contact-address-type').text();
    let updateAddressBilling = `
                <input type="checkbox" name="update_billing_address" id="update-billing-address">${updateThisAddress}`;
    $('#billing-contact-person-name').val(billingPerson);
    $('#billing-phone').val(billingPhone);
    $('#billing-phone').keypress()
    $('#billing_address').val(billingAddress);
    $('#billing-city').val(billingCity);
    $('#billing-zip').val(billingZip);
    $('#select2-billing_zip-container').text(billingZip);
    $('#billing-country').val(billingCountry);
    $('#select2-billing_country-container').text(billingCountry);
    $('#billing-address-type').val(billingContactAddressType);
    $('#save-billing-address-label').html(updateAddressBilling);
}

$('#same-as-shipping-address').on('click', function () {
    let checkSameAsShipping = $('#same-as-shipping-address').is(":checked");
    if (checkSameAsShipping) {
        $('#hide-billing-address').slideUp();
    } else {
        $('#hide-billing-address').slideDown();
    }
})


async function initAutoComplete() {
    try {
        console.log("1. initAutoComplete function started.");

        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        const { SearchBox } = await google.maps.importLibrary("places");
        const { Geocoder } = await google.maps.importLibrary("geocoding");

        console.log("2. Google Maps libraries imported successfully.");

        let lat = $('#shipping-address-location').data('latitude') || 33.5138;
        let lng = $('#shipping-address-location').data('longitude') || 36.2765;
        let myLatLng = { lat: parseFloat(lat), lng: parseFloat(lng) };

        const map = new Map(document.getElementById("location_map_canvas"), {
            center: myLatLng,
            zoom: 13,
            mapId: "roadmap",
            clickableIcons: false // Keep this, it's still best practice
        });

        console.log("3. Map object created successfully.");

        let marker = new AdvancedMarkerElement({ map, position: myLatLng });
        const geocoder = new Geocoder();

        console.log("4. Attaching the map click listener now...");

        map.addListener('click', (mapsMouseEvent) => {
            // IF YOU SEE THIS, THE CLICK IS WORKING!
            console.log("%c 5. SUCCESS! Map click was captured.", "color: green; font-size: 1.2em;");

            const latlng = mapsMouseEvent.latLng.toJSON();
            marker.position = latlng; // This moves the red marker
            map.panTo(latlng);
            $('#latitude').val(latlng.lat);
            $('#longitude').val(latlng.lng).trigger('change');
            geocoder.geocode({ 'location': latlng }, (results, status) => {
                if (status === "OK" && results[0]) {
                    $('#address').val(results[0].formatted_address);
                }
            });
        });

        console.log("6. Click listener has been attached.");

        // ... rest of searchbox code ...
        const input = document.getElementById("pac-input");
        const searchBox = new SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
        // ... and so on

    } catch (e) {
        console.error("A critical error occurred in initAutoComplete:", e);
    }
}


// This is the corrected version of your billingMap function
async function billingMap() {
    try {
        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        const { SearchBox } = await google.maps.importLibrary("places");
        const { Geocoder } = await google.maps.importLibrary("geocoding");

        let lat = $('#shipping-address-location').data('latitude') || 33.5138;
        let lng = $('#shipping-address-location').data('longitude') || 36.2765;
        let myLatLng = { lat: parseFloat(lat), lng: parseFloat(lng) };

        // CORRECT: Use the billing map's canvas ID and search box ID
        const map = new Map(document.getElementById("billing-location-map-canvas"), {
            center: myLatLng,
            zoom: 13,
            mapId: "roadmap",
            clickableIcons: false // Keep this for consistent behavior
        });
        const input = document.getElementById("pac-input-billing");

        let marker = new AdvancedMarkerElement({ map, position: myLatLng });
        const geocoder = new Geocoder();
        const searchBox = new SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

        // This listener updates the BILLING fields
        map.addListener('click', (mapsMouseEvent) => {
            const latlng = mapsMouseEvent.latLng.toJSON();
            marker.position = latlng;
            map.panTo(latlng);
            // CORRECT: Update the billing latitude and longitude inputs
            $('#billing-latitude').val(latlng.lat);
            $('#billing-longitude').val(latlng.lng);
            geocoder.geocode({ 'location': latlng }, (results, status) => {
                if (status === "OK" && results[0]) {
                    $('#billing_address').val(results[0].formatted_address);
                }
            });
        });
        
    } catch (e) {
        console.error("Error in billingMap:", e);
    }
}

$(document).on("keydown", "input", function (e) {
    if (e.which === 13) e.preventDefault();
});

// CORRECTED Calculation function
function triggerShippingCalculation() {
    let destination_lat, destination_lon, cart_group_id;
    let selectedShippingMethodId = $('#chosen-shipping-method-id').val();


    if (!selectedShippingMethodId) {
        toastr.error('Please select a shipping method.');
        console.log("Calculation stopped: No shipping method selected.");
        return;
    }

    let map_lat = $('#latitude').val();
    let map_lon = $('#longitude').val();

    if (map_lat && map_lon && map_lat !== '0' && map_lon !== '0') {
         destination_lat = map_lat;
         destination_lon = map_lon;
    } else {
        let checked_radio = $('input[name="shipping_method_id"]:checked');
        if (checked_radio.length > 0) {
            let cardBody = checked_radio.parents('.card-header').siblings('.card-body');
            destination_lat = cardBody.find('.shipping-latitude').text().trim();
            destination_lon = cardBody.find('.shipping-longitude').text().trim();
        }
    }

    cart_group_id = $('#cart-group-id-for-shipping').val();

    // Use the GLOBAL selectedShippingMethodId
    if (destination_lat && destination_lon && selectedShippingMethodId && cart_group_id) {
        getDynamicShippingCost(destination_lat, destination_lon, selectedShippingMethodId, cart_group_id);
    } else {
        console.log("Not enough info to calculate shipping.", {
            lat: destination_lat,
            lon: destination_lon,
            method: selectedShippingMethodId,
            cart: cart_group_id
        });
    }
}

function parseCurrency(currencyString) {
    if (typeof currencyString !== 'string') return 0;
    // Removes currency symbols, letters, spaces, and thousand separators
    const cleanedString = currencyString.replace(/[^\d.-]/g, '');
    const number = parseFloat(cleanedString);
    return isNaN(number) ? 0 : number;
}
function getDynamicShippingCost(destination_lat, destination_lon, shipping_method_id, cart_group_id) {
    const shippingCostUrl = $('#address-form').data('shipping-cost-url');
    $('#shipping-cost-display').html('Calculating...');
    $('#distance').html('Calculating...');

    $.ajaxSetup({
        headers: { "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content") },
    });

    $.ajax({
        url: shippingCostUrl,
        type: 'GET',
        data: {
            destination_lat,
            destination_lon,
            shipping_method_id,
            cart_group_id
        },
        success: function (data) {
            if (data.cost_formatted) {
                // 1. Update the shipping cost and distance
                $('#shipping-cost-display').html(data.cost_formatted);
                $('#distance').html(data.distance_km);

                // --- RECALCULATION LOGIC ---
                // 2. Read all other values from the page using their new IDs
                const subTotal = parseCurrency($('#sub-total-display').text());
                const tax = parseCurrency($('#tax-display').text());
                const newShippingCost = parseCurrency(data.cost_formatted); // Use the new value

                // Handle coupon discount only if the element exists
                let couponDiscount = 0;
                if ($('#coupon-discount-display').length) {
                    couponDiscount = parseCurrency($('#coupon-discount-display').text());
                }

                // 3. Do the math
                const newGrandTotal = subTotal + tax + newShippingCost + couponDiscount;

                // 4. Update the grand total on the page, preserving currency format
                let currencySymbol = data.cost_formatted.replace(/[\d,.\s-]/g, '');
                $('#grand-total-display').html(currencySymbol + ' ' + newGrandTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                
            } else {
                $('#shipping-cost-display').html('N/A');
                $('#distance').html('N/A');
                toastr.error(data.message || 'Could not calculate shipping cost.');
            }
        },
        error: function (xhr) {
            $('#shipping-cost-display').html('Error');
            $('#distance').html('Error');
            toastr.error('An error occurred while fetching the shipping rate.');
            console.error(xhr.responseText);
        }
    });
}

function initializeMapsWhenReady() {
    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined' && typeof google.maps.importLibrary === 'function') {
        // SUCCESS: The API is fully loaded, now we can draw the maps.
        console.log("Google Maps API is ready. Initializing maps...");
        initAutoComplete();
        if ($('#billing-location-map-canvas').length > 0) {
            try { billingMap(); } catch (error) { console.error("Error in billingMap:", error); }
        }    
    } else {
        // NOT READY: Wait 100ms and try again.
        console.log("Waiting for Google Maps API to be ready...");
        setTimeout(initializeMapsWhenReady, 100);
    }
}


$('#proceed-to-next-action').on('click', function () {

    let physicalProduct = $('#physical-product').val();
    let billingAddressSameAsShipping = $('#same-as-shipping-address').is(":checked");
    if (physicalProduct === 'yes') {
        let allAreFilled = true;
        document.getElementById("address-form").querySelectorAll("[required]").forEach(function (i) {
            if (!allAreFilled) return;
            if (!i.value) allAreFilled = false;
            if (i.type === "radio") {
                let radioValueCheck = false;
                document.getElementById("address-form").querySelectorAll(`[name=${i.name}]`).forEach(function (r) {
                    if (r.checked) radioValueCheck = true;
                });
                allAreFilled = radioValueCheck;
            }
        });
        let allAreFilledShipping = true;
        if (billingAddressSameAsShipping !== true && $('#billing-input-enable').val() === 1) {
            document.getElementById("billing-address-form").querySelectorAll("[required]").forEach(function (i) {
                if (!allAreFilledShipping) return;
                if (!i.value) allAreFilledShipping = false;
                if (i.type === "radio") {
                    let radioValueCheck = false;
                    document.getElementById("billing-address-form").querySelectorAll(`[name=${i.name}]`).forEach(function (r) {
                        if (r.checked) radioValueCheck = true;
                    });
                    allAreFilledShipping = radioValueCheck;
                }
            });
        }
    } else {
        let billingAddressSameAsShipping = false;
    }

    let redirectUrl = $(this).data('checkout-payment');
    let formUrl = $(this).data('goto-checkout');

    let isCheckCreateAccount = $('#is_check_create_account');
    let customerPassword = $('#customer_password');
    let customerConfirmPassword = $('#customer_confirm_password');

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
        },
    });
    $.post({
        url: formUrl,
        data: {
            physical_product: physicalProduct,
            shipping: physicalProduct === 'yes' ? $('#address-form').serialize() : null,
            billing: $('#billing-address-form').serialize(),
            billing_addresss_same_shipping: billingAddressSameAsShipping,
            is_check_create_account: isCheckCreateAccount && isCheckCreateAccount.prop("checked") ? 1 : 0,
            customer_password: customerPassword ? customerPassword.val() : null,
            customer_confirm_password: customerConfirmPassword ? customerConfirmPassword.val() : null,
        },

        beforeSend: function () {
            $('#loading').addClass('d-grid');
        },
        success: function (data) {
            if (data.errors) {
                for (let i = 0; i < data.errors.length; i++) {
                    toastr.error(data.errors[i].message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            } else {
                location.href = redirectUrl;
            }
        },
        complete: function () {
            $('#loading').removeClass('d-grid');
        },
        error: function (data) {
            let errorMessage = data.responseJSON.errors;
            toastr.error(errorMessage, {
                CloseButton: true,
                ProgressBar: true
            });
        }
    });
});

$('#is_check_create_account').on('change', function() {
    if($(this).is(':checked')) {
        $('.is_check_create_account_password_group').fadeIn();
    } else {
        $('.is_check_create_account_password_group').fadeOut();
    }
});



$('[name="shipping_address_id"]').on('change', function () {
    // Its only job is to fill out the form fields.
    let cardBody = $(this).closest('.card-header').siblings('.card-body');
    populateShippingFormFromAddress(cardBody);
    calculateShippingForSelectedAddress();
});


// 2. LISTENER FOR MAP INTERACTIONS (Hidden Inputs)
$('#latitude, #longitude').on('change', function() {
    console.log('Map location changed, recalculating...');
    triggerShippingCalculation();
});
