<?php
class MooWoodle_Posttype_Course_Registration {
	private $labels = array();
	public $course;
	public function __construct() {
		$this->labels['course'] = array(
			'singular' => __('Course', 'moowoodle'),
			'plural' => __('Courses', 'moowoodle'),
			'menu' => __('Courses', 'moowoodle'),
		);
		$this->register_course_post_type();
		$this->register_course_cat_taxonomy();
	}
	public function register_course_post_type() {
		$args = array(
			'labels' => array(
				'name' => sprintf(_x('%s', 'post type general name', 'moowoodle'), $this->labels['course']['plural']),
				'singular_name' => sprintf(_x('%s', 'post type singular name', 'moowoodle'), $this->labels['course']['singular']),
				'add_new' => sprintf(_x('Add New %s', 'course', 'moowoodle'), $this->labels['course']['singular']),
				'add_new_item' => sprintf(__('Add New %s', 'moowoodle'), $this->labels['course']['singular']),
				'edit_item' => sprintf(__('Edit %s', 'moowoodle'), $this->labels['course']['singular']),
				'new_item' => sprintf(__('New %s', 'moowoodle'), $this->labels['course']['singular']),
				'all_items' => sprintf(__('%s', 'moowoodle'), $this->labels['course']['plural']),
				'view_item' => sprintf(__('View %s', 'moowoodle'), $this->labels['course']['singular']),
				'search_items' => sprintf(__('Search %s', 'moowoodle'), $this->labels['course']['plural']),
				'not_found' => sprintf(__('No %s found', 'moowoodle'), strtolower($this->labels['course']['plural'])),
				'not_found_in_trash' => sprintf(__('No %s found in Trash', 'moowoodle'), strtolower($this->labels['course']['plural'])),
			),
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
			'map_meta_cap' => false,
			'has_archive' => false,
			'hierarchical' => false,
			'show_in_menu' => false,
			'supports' => array('title', 'editor'),
			'capability_type' => 'post',
			'capabilities' => array('create_posts' => false,
				'delete_posts' => false,
			),
		);
		register_post_type('course', $args);
	}
	function register_course_cat_taxonomy() {
		register_taxonomy('course_cat', 'course',
			array(
				'labels' => array(
					'name' => sprintf(_x('%s category', 'moowoodle'), $this->labels['course']['singular']),
					'singular_name' => sprintf(_x('%s category', 'moowoodle'), $this->labels['course']['singular']),
					'add_new_item' => sprintf(_x('Add new %s category', 'moowoodle'), $this->labels['course']['singular']),
					'new_item_name' => sprintf(_x('New %s category', 'moowoodle'), $this->labels['course']['singular']),
					'menu_name' => sprintf(_x('%s category', 'moowoodle'), $this->labels['course']['singular']), //'Categories',
					'search_items' => sprintf(_x('Search %s categories', 'moowoodle'), $this->labels['course']['singular']), //'Search Course Categories',
					'all_items' => sprintf(_x('All %s categories', 'moowoodle'), $this->labels['course']['singular']), //'All Course Categories',
					'parent_item' => sprintf(_x('Parent %s category', 'moowoodle'), $this->labels['course']['singular']), //'Parent Course Category',
					'parent_item_colon' => sprintf(_x('Parent %s category', 'moowoodle'), $this->labels['course']['singular']), //'Parent Course Category:',
					'edit_item' => sprintf(_x('Edit %s category', 'moowoodle'), $this->labels['course']['singular']), //'Edit Course Category',
					'update_item' => sprintf(_x('New %s category name', 'moowoodle'), $this->labels['course']['singular']), //'New Course Category Name'
				),
				'show_ui' => false,
				'show_tagcloud' => false,
				'hierarchical' => true,
				'query_var' => true,
			)
		);
	}
}