<?php

namespace MooWoodle;

/**
 * MooWoodle FrontendScripts class
 *
 * @class 		FrontendScripts class
 * @version		6.0.4
 * @author 		Dualcube
 */
class FrontendScripts {

    public static $scripts = [];
    public static $styles = [];

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );
    }

    public static function register_script( $handle, $path, $deps = [], $version="", $text_domain="" ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, true );
        wp_set_script_translations( $handle, $text_domain );
	}

    public static function register_style( $handle, $path, $deps = [], $version="" ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version );
	}

    public static function register_scripts() {
		$version = MooWoodle()->version;

		$register_scripts = apply_filters('moowoodle_register_scripts', array(
			'moowoodle-my-courses-script' => [
				'src'     => MooWoodle()->plugin_url . 'build/blocks/my-courses/index.js',
				'deps'    => [ 'jquery', 'jquery-blockui', 'wp-element', 'wp-i18n' ],
				'version' => $version,
                'text_domain' => 'moowoodle'
            ],
		) );
		foreach ( $register_scripts as $name => $props ) {
			self::register_script( $name, $props['src'], $props['deps'], $props['version'], $props['text_domain'] );
		}
	}

    public static function register_styles() {
		$version = MooWoodle()->version;

		$register_styles = apply_filters('moowoodle_register_styles', [

        ] );
		foreach ( $register_styles as $name => $props ) {
			self::register_style( $name, $props['src'], $props['deps'], $props['version'] );
		}
	}

    /**
	 * Register/queue frontend scripts.
	 */
	public static function load_scripts() {
        self::register_scripts();
		self::register_styles();
    }

    public static function localize_scripts( $handle ) {

        $localize_scripts = apply_filters('moowoodle_localize_scripts', array(
			'moowoodle-my-courses-script' => [
				'object_name' => 'appLocalizer',
                'data' =>             [
                    'apiUrl'          => untrailingslashit(get_rest_url()),
                    'restUrl'         => 'moowoodle/v1',
                    'nonce'           => wp_create_nonce('wp_rest'),
                    'moodle_site_url' => MooWoodle()->setting->get_setting('moodle_url'),
                ]
            ],
		));
       
        if ( isset( $localize_scripts[ $handle ] ) ) {
            $props = $localize_scripts[ $handle ];
            self::localize_script( $handle, $props['object_name'], $props['data'] );
        }
	}

    public static function localize_script( $handle, $name, $data = [], ) {
		wp_localize_script( $handle, $name, $data );
	}

    public static function enqueue_script( $handle ) {
		wp_enqueue_script( $handle );
	}

    public static function enqueue_style( $handle ) {
		wp_enqueue_style( $handle );
	}
    
}