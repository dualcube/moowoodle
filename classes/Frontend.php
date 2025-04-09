<?php
namespace MooWoodle;

class Frontend {
    public function __construct() {
        // Reset cart quantities after update
        add_action('woocommerce_cart_updated', [$this, 'restrict_cart_quantity_on_update']);
    }

    /**
     * Ensure cart quantities stay at 1 after cart updates (server level)
     */
    public function restrict_cart_quantity_on_update() {
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
}
