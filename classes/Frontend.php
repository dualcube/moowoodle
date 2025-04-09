<?php
namespace MooWoodle;

class Frontend {
    public function __construct() {
        // Reset cart quantities after update
        add_action('woocommerce_cart_updated', [$this, 'enforce_quantity_restriction']);
        // Add messages when cart/checkout is viewed
        add_action('wp', [$this, 'show_cart_limit_notice']);
    }

    /**
     * Enforce quantity restriction based on plugin version and settings
     */
    public function enforce_quantity_restriction() {
        $group_purchase_enabled = MooWoodle()->setting->get_setting('group_purchase_enable');
        $is_khali_dabba = MooWoodle()->util->is_khali_dabba(); 

        if (
            is_array($group_purchase_enabled) &&
            in_array('group_purchase_enable', $group_purchase_enabled) &&
            $is_khali_dabba
        ) {
            return;
        }

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['quantity'] > 1) {
                WC()->cart->set_quantity($cart_item_key, 1);
            }
        }
    }

    /**
     * Show message on cart page based on version and settings
     */
    public function show_cart_limit_notice() {
        if ( !is_cart() ) {
            return;
        }

        $group_purchase_enabled = MooWoodle()->setting->get_setting('group_purchase_enable');
        $is_khali_dabba = MooWoodle()->util->is_khali_dabba();

        if (!$is_khali_dabba) {
            wc_add_notice(
                __('In the free version, each product is limited to a quantity of 1 per cart. Upgrade to Pro for unlimited quantities!', 'moowoodle'),
                'notice'
            );
        } elseif (!is_array($group_purchase_enabled) || !in_array('group_purchase_enable', $group_purchase_enabled)) {
            wc_add_notice(
                __('Group purchase is disabled. Enable it in MooWoodle settings to allow multiple quantities.', 'moowoodle'),
                'notice'
            );
        }
    }
}
