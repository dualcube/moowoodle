<?php

namespace MooWoodle;

defined( 'ABSPATH' ) || exit;

class FrontEnd {

    public function __construct() {
        add_action('woocommerce_init', [$this, 'register_checkout_fields']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function register_checkout_fields() {
        $this->register_gift_checkbox();
        $this->register_name_field();
        $this->register_email_field();
    }

    private function register_gift_checkbox() {
        woocommerce_register_additional_checkout_field([
            'id'       => 'MooWoodle/gift_someone',
            'label'    => 'Gift Someone',
            'location' => 'address',
            'type'     => 'checkbox',
        ]);
    }

    private function register_name_field() {
        woocommerce_register_additional_checkout_field([
            'id'         => 'MooWoodle/full_name',
            'label'      => 'Name',
            'location'   => 'address',
            'required'   => false,
            'type'       => 'text',
            'attributes' => ['data-custom' => 'custom-data-name'],
        ]);
    }

    private function register_email_field() {
        woocommerce_register_additional_checkout_field([
            'id'         => 'MooWoodle/email_address',
            'label'      => 'Email',
            'location'   => 'address',
            'required'   => false,
            'type'       => 'text',
            'attributes' => ['data-custom' => 'custom-data-email'],
        ]);
    }

    public function enqueue_scripts() {
        if ( is_checkout() ) { 
            wp_enqueue_script(
                'gift-toggle',
                plugin_dir_url(dirname(__FILE__)) . 'assets/js/gift-toggle.js',
                ['jquery'], 
                null,
                true 
            );
        }
    }
}