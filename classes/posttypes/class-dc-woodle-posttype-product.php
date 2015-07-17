<?php
class DC_Woodle_Posttype_Product {

	public function __construct() {
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( &$this, 'save_product_callback' ) );
	}
	
	/**
   * Creates custom meta box for product types.
   *
   * @access public
   * @return void
   */  
  public function add_meta_boxes() {
  	global $DC_Woodle;
  	
    add_meta_box( 'select_courses', __( 'Related Course', $DC_Woodle->text_domain ), array( &$this, 'add_product_meta_boxes' ), 'product', 'normal' );
  }
	
  /**
   * Add meta box content.
   *
   * @access public
   * @return void
   */ 
	public function add_product_meta_boxes() {
		global $post, $DC_Woodle;
		
		$course_id = get_post_meta( $post->ID, '_course_id', true );
		$post_id = woodle_get_post_by_moodle_id( $course_id, 'course' );		
		$course_short_name = get_post_meta( $post_id, '_course_short_name', true );
		
		$courses = woodle_get_posts( array( 'post_type' => 'course', 'post_status' => 'publish' ) );
		?>
		<p>
			<label for="courses"><?php _e( 'Course Name', $DC_Woodle->text_domain ); ?></label>
			<select id="courses-select" name="course_id">
				<option value="0"><?php _e( 'Select course...', $DC_Woodle->text_domain ); ?></option>
				<?php
				if( ! empty( $courses ) ) {
					foreach($courses as $course) {
						$id = get_post_meta( $course->ID, '_course_id', true );
						$category_id = get_post_meta( $course->ID, '_category_id', true );
						$term_id = woodle_get_term_by_moodle_id( $category_id, 'course_cat', 'woodle_term' );
						$course_category_path = get_woodle_term_meta( $term_id, '_category_path', true );
						$category_ids = explode( '/', $course_category_path );
						$course_path = array();
						$x = array();
						if( ! empty( $category_ids ) ) {
							foreach( $category_ids as $cat_id ) {
								if( ! empty( $cat_id ) ) {
									$term_id = woodle_get_term_by_moodle_id( intval( $cat_id ), 'course_cat', 'woodle_term' );
									$term = get_term( $term_id, 'course_cat' );
									$course_path[] = $term->name;
								}
							}
						}
						$course_name = '';
						if( ! empty( $course_path ) ) {
							$course_name = implode( ' / ', $course_path );
							$course_name .= ' - ';
						}
						$course_name .= $course->post_title;
						if( ! empty( $id ) ) {
							?>
							<option value="<?php echo $id; ?>" <?php if( ! empty( $course_id ) && $id == $course_id) echo 'selected="selected"'; ?>><?php echo $course_name; ?></option>
							<?php
						}
					}
				}
				?>
			</select>
		</p>
    <p>
		  <label for="course_short_name"><?php _e( 'Course Short Name', $DC_Woodle->text_domain ); ?></label>
		  <input type="text" id="course_short_name" name="course_short_name" value="<?php if( ! empty( $course_short_name ) ) echo $course_short_name; ?>" readonly>
		</p>
    <?php
	}
	
	/**
   * Save product meta.
   *
   * @access public
   * @param int $post_id
   * @return void
   */  
	public function save_product_callback( $post_id ) {
		require_once( ABSPATH . 'wp-admin/includes/screen.php' );
		
		$screen = get_current_screen();
		$post = get_post( $post_id );
		
		if( $post && ! empty( $screen ) && $screen->id == 'product' && $post->post_type = 'product' && ! empty( $_POST ) && array_key_exists( 'course_id', $_POST ) ) {
			$product_id = woodle_get_post_by_moodle_id( $_POST['course_id'], 'product' );
			
			if( $product_id == $post_id || ! $product_id ) {
				$course_id = woodle_get_post_by_moodle_id( $_POST['course_id'], 'course' );
				if( $_POST['course_id'] == 0 ) {
					delete_post_meta( $post_id, '_course_id' );
					delete_post_meta( $post_id, '_category_id' );
					update_post_meta( $post_id, '_visibility', 'hidden' );
					delete_post_meta( $post_id, '_virtual' );
					delete_post_meta( $post_id, '_sku');
					return;
				}
				
				if( $course_id ) {
					$categoryid = get_post_meta( $course_id, '_category_id', true );
					$visibility = get_post_meta( $course_id, '_visibility', true );
					
					$term_id = woodle_get_term_by_moodle_id( $categoryid, 'product_cat', 'woocommerce_term' );
					if( $term_id ) {
						wp_set_post_terms( $post_id, $term_id, 'product_cat' );
					}
					
					update_post_meta( $post_id, '_course_id', (int) $_POST['course_id'] );
					update_post_meta( $post_id, '_category_id', (int) $categoryid );
					update_post_meta( $post_id, '_visibility', $visibility );
					update_post_meta( $post_id, '_virtual', 'yes' );
					update_post_meta( $post_id, '_sku', 'course-' . $_POST['course_id'] );
					update_post_meta( $post_id, '_sold_individually', 'yes' );
				}
			}
		}
	}
}
