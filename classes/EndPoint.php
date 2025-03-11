<?php

namespace MooWoodle;

class EndPoint {
    private $endpoint = 'my-courses';

    public function __construct() {
        add_action('init', [$this, 'init']);
        add_filter('woocommerce_account_menu_items', [$this, 'modify_menu']);
        add_action('woocommerce_account_' . $this->endpoint . '_endpoint', [$this, 'render_content']);
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    /**
     * Initializes the endpoint and registers the block.
     */
    public function init() {
        add_rewrite_endpoint($this->endpoint, EP_ROOT | EP_PAGES);
        $this->register_block();
    }

    /**
     * Registers the Gutenberg block.
     */
    private function register_block() {
        register_block_type(MOOWOODLE_PLUGIN_PATH . 'build/blocks/MyCourses/', [
            'render_callback' => [$this, 'render_block'],
        ]);
    }

    /**
     * Modifies the WooCommerce My Account menu.
     */
    public function modify_menu($menu) {
        $priority = (int) MooWoodle()->setting->get_setting('my_courses_priority') ?: 0;

        return array_merge(
            array_slice($menu, 0, $priority + 1, true),
            [$this->endpoint => __('My Courses', 'moowoodle')],
            array_slice($menu, $priority + 1, null, true)
        );
    }

    /**
     * Renders the WooCommerce My Account section.
     */
    public function render_content() {
        echo render_block(['blockName' => 'moowoodle/my-courses']);
    }

    /**
     * Callback function to render the block.
     */
    public function render_block() {
        return is_account_page() ? '<div id="moowoodle-my-course"></div>' : '';
    }

    /**
     * Enqueues localized script data.
     */
    public function enqueue_assets() {
        wp_set_script_translations('my-courses', 'moowoodle');

        wp_localize_script(
            'moowoodle-my-courses-script',
            'appLocalizer',
            [
                'apiUrl'          => untrailingslashit(get_rest_url()),
                'restUrl'         => 'moowoodle/v1',
                'nonce'           => wp_create_nonce('wp_rest'),
                'moodle_site_url' => MooWoodle()->setting->get_setting('moodle_url'),
            ]
        );
    }
}
