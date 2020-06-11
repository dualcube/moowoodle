<?php
class DC_Woodle_shortcode {

	public function __construct() {
		add_shortcode('moowoodle',array( &$this, 'moowoodle_handler') );



		if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') ) {
		    add_filter('mce_external_plugins',array( &$this, 'moowoodle_add_plugin') );
		}


	}
	public function moowoodle_handler( $atts, $content = null ) {
		global $DC_Woodle;
	// clone attribs over any default values, builds variables out of them so we can use them below
	// $class => css class to put on link we build
	// $cohort => text id of the moodle cohort in which to enrol this user
	// $group => text id of the moodle group in which to enrol this user
	// $course => text id of the course, if you just want to enrol a user directly to a course
	// $authtext => string containing text content to display when not logged on (defaults to content between tags when empty / missing)
	// $activity => index of the first activity to open, if autoopen is enabled in moodle
		extract(shortcode_atts(array(
			"cohort" => '',
			"group" => '',
			"course" => '',
			"class" => 'moowoodle',
			"target" => '_self',
			"authtext" => '',
			"activity" => 0
			), $atts));

		if ($content == null || !is_user_logged_in() ) {
			if (trim($authtext) == "") {
		$url = '<a href="' . wp_registration_url() . '" class="'.esc_attr($class).'">' . do_shortcode($content) . '</a>'; // return content text linked to registration page  (value between start and end tag)
	} else {
		$url = '<a href="' . wp_registration_url() . '" class="'.esc_attr($class).'">' . do_shortcode($authtext) . '</a>'; // return authtext linked to registration page (value of attribute, if set)
		}
	} else {
		// url = moodle_url + "?data=" + <encrypted-value>
		$url = '<a target="'.esc_attr($target).'" class="'.esc_attr($class).'" href="'.$DC_Woodle->enrollment->moowoodle_generate_hyperlink($cohort,$group,$course,$activity).'">'.do_shortcode($content).'</a>'; // hyperlinked content
	}
		return $url;
	}

	public function moowoodle_add_plugin($plugin_array) {
	   $plugin_array['moowoodle'] = plugin_dir_url(__FILE__).'moowoodle.js';
	   return $plugin_array;
	}

}