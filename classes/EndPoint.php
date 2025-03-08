<?php

namespace MooWoodle;

class EndPoint {
    private $endpoint_slug_course = 'my-courses';

    public function __construct() {
        add_action('init', [$this, 'initialize_custom_endpoints']);
        add_filter('woocommerce_account_menu_items', [$this, 'add_my_courses_menu']);
        add_action('woocommerce_account_' . $this->endpoint_slug_course . '_endpoint', [$this, 'load_my_courses_account_endpoint']);
    }

    /**
     * Adds my-courses endpoint.
     */
    public function initialize_custom_endpoints() {
        add_rewrite_endpoint($this->endpoint_slug_course, EP_ROOT | EP_PAGES);
    }

    /**
     * Register custom menu items to the My Account WooCommerce menu.
     *
     * @param array $menu_links Existing WooCommerce account menu links.
     * @return array Modified menu links.
     */
    public function add_my_courses_menu($menu_links) {
        $priority = (int) MooWoodle()->setting->get_setting('my_courses_priority') ?: 0;
        $menu_links = array_merge(
            array_slice($menu_links, 0, $priority + 1, true),
            [$this->endpoint_slug_course => __('My Courses', 'moowoodle')],
            array_slice($menu_links, $priority + 1, null, true)
        );
        return $menu_links;
    }

    /**
     * Load My Courses React component on the WooCommerce My Account page.
     */
    public function load_my_courses_account_endpoint() {
        if (!is_account_page()) {
            return;
        }

        wp_enqueue_script(
            'moowoodle-myaccount-mycourse-script',
            MOOWOODLE_PLUGIN_URL . 'build/blocks/MyCourses/index.js',
            ['wp-element', 'wp-i18n', 'react-jsx-runtime'],
            filemtime(MOOWOODLE_PLUGIN_PATH . 'build/blocks/MyCourses/index.js'),
            true
        );

        wp_localize_script('moowoodle-myaccount-mycourse-script', 'appLocalizer', [
            'apiUrl'          => untrailingslashit(get_rest_url()),
            'restUrl'         => 'moowoodle/v1',
            'nonce'           => wp_create_nonce('wp_rest'),
            'moodle_site_url' => MooWoodle()->setting->get_setting('moodle_url'),
        ]);

        echo '<div id="moowoodle-my-course"></div>';
    }
}
