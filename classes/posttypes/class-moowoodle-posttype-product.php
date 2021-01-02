<?php
class MooWoodle_PostType_Product {

	public function __construct() {
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
		add_action( 'save_post_product', array( &$this, 'save_product_callback' ) );
		
	}
		
	/**
   * Creates custom meta box for product types.
   *
   * @access public
   * @return void
   */  
  public function add_meta_boxes() {
  	add_meta_box( 'select_courses', __( 'Moodle Linked Course', 'moowoodle' ), array( &$this, 'add_product_meta_boxes' ), 'product', 'normal' );
  }
	
  /**
   * Add meta box content.
   *
   * @access public
   * @return void
   */ 
	public function add_product_meta_boxes() {
		global $post;
		
		$course_id = get_post_meta( $post->ID, 'moodle_course_id', true );
		$linked_course_id = get_post_meta( $post->ID, 'linked_course_id', true );
		$post_id = moowoodle_get_post_by_moodle_id( $course_id, 'course' );
		
		$courses = get_posts( array( 'post_type' => 'course', 'numberposts' => -1, 'post_status' => 'publish' ) );
		
		?>
		<p>
			<label for="courses"><?php esc_html_e( 'Linked Course', 'moowoodle' ); ?></label>
			<select id="courses-select" name="course_id">
				<option value="0"><?php esc_html_e( 'Select course...', 'moowoodle' ); ?></option>
				<?php
				if( ! empty( $courses ) ) {
					foreach($courses as $course) {
						$id = get_post_meta( $course->ID, 'moodle_course_id', true );
						$course_short_name = get_post_meta( $course->ID, '_course_short_name', true );
						$category_id = get_post_meta( $course->ID, '_category_id', true );
						$term_id = moowoodle_get_term_by_moodle_id( $category_id, 'course_cat', 'moowoodle_term' );
						$course_category_path = get_term_meta( $term_id, '_category_path', true );
						$category_ids = explode( '/', $course_category_path );
						$course_path = array();
						
						if( ! empty( $category_ids ) ) {
							foreach( $category_ids as $cat_id ) {
								if( ! empty( $cat_id ) ) {
									$term_id = moowoodle_get_term_by_moodle_id( intval( $cat_id ), 'course_cat', 'moowoodle_term' );
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
							
							<option value="<?php echo esc_attr( $id ); ?>" <?php if( ! empty( $linked_course_id ) && $id == $linked_course_id) echo 'selected="selected"'; ?>><?php echo $course_name; if( ! empty( $course_short_name ) ) echo " ( " . esc_html( $course_short_name ) . " )"; ?></option>
							<?php
						}

					}
				}
				?>
			</select>
		</p>   
    <?php
    	echo esc_html_e( "Cannot find your course in this list?", "moowoodle");
    ?>
     <a href="<?php echo esc_url( get_site_url() ) ?>/wp-admin/admin.php?page=moowoodle-synchronization" target="_blank"><?php esc_html_e('Synchronize Moodle Courses from here.', 'moowoodle'); ?></a>
    <?php

    // Nonce field (for security)
    echo '<input type="hidden" name="product_meta_nonce" value="' . wp_create_nonce() . '">';

	}	

	
	/**
   * Save product meta.
   *
   * @access public
   * @param int $post_id
   * @return void
   */  
	public function save_product_callback( $post_id ) {
		
		// Security check
	    if ( ! isset( $_POST[ 'product_meta_nonce' ] ) ) {
	        return $post_id;
	    }

	    //Verify that the nonce is valid.
	    if ( ! wp_verify_nonce( $_POST[ 'product_meta_nonce' ] ) ) {
	        return $post_id;
	    }

	    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	        return $post_id;
	    }

	    if ( ! current_user_can( 'edit_product', $post_id ) ) {
	        return $post_id;
	    }

	    if( isset($_POST[ 'course_id' ]) ) {
        	update_post_meta( $post_id, 'linked_course_id', wp_kses_post( $_POST[ 'course_id' ] ) );
	    }

	}
}
