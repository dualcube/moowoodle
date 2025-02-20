jQuery(function ($) {
    console.log("Gift Someone checkbox script loaded");

    function toggleGiftFields() {
        if ($('#billing-MooWoodle-gift_someone').is(':checked')) {
            console.log("Gift Someone checkbox is checked");
            $('.wc-block-components-address-form__MooWoodle-full_name').show(); // Show Name field div
            $('.wc-block-components-address-form__MooWoodle-email_address').show(); // Show Email field div
        } else {
            console.log("Gift Someone checkbox is unchecked");
            $('.wc-block-components-address-form__MooWoodle-full_name').hide(); // Hide Name field div
            $('.wc-block-components-address-form__MooWoodle-email_address').hide(); // Hide Email field div
        }
    }

    // Ensure fields are hidden on page load before checking checkbox state
    $('.wc-block-components-address-form__MooWoodle-full_name, .wc-block-components-address-form__MooWoodle-email_address').hide();

    // Event listener for checkbox change
    $(document).on('change', '#billing-MooWoodle-gift_someone', toggleGiftFields);

    // Delay execution slightly to ensure WooCommerce has rendered everything
    setTimeout(toggleGiftFields, 800);
});
