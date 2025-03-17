jQuery(function ($) {

    function toggleGiftFields() {
        if ($('#billing-MooWoodle-gift_someone').is(':checked')) {
            $('.wc-block-components-address-form__MooWoodle-full_name').show(); 
            $('.wc-block-components-address-form__MooWoodle-email_address').show();
        } else {
            $('.wc-block-components-address-form__MooWoodle-full_name').hide();
            $('.wc-block-components-address-form__MooWoodle-email_address').hide();
        }
    }

    $('.wc-block-components-address-form__MooWoodle-full_name, .wc-block-components-address-form__MooWoodle-email_address').hide();

    $(document).on('change', '#billing-MooWoodle-gift_someone', toggleGiftFields);

    toggleGiftFields();
});
