<?php 




// from heading
$from_heading = apply_filters( 'moowoodle_enrolment_heading',
                                    array(
                                           __( "Course Name", 'moowoodle' ),
                                           __( "Student Name", 'moowoodle' ),
                                           __( "Enrolled Date", 'moowoodle' ),
                                           __( "Manager", 'moowoodle' ), 
                                        )
                                );

?>
<script src=
"https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js">
    </script>
<div class="auto">
    <input id="searchInput" type="text" placeholder="Search here">
    <table class="table table-bordered responsive-table moowoodle-manage-enrollment widefat">
        <thead>
            <tr>
            <?php

            $visibility_status = array( 'visible' => 'Visible',
                                        'hidden' => 'Hidden'
                                    );

            foreach ( $from_heading as $key_heading => $value_heading ) {
            ?>
                <th>
                    <?php echo $value_heading; ?>
                </th>  
            <?php
            }
            ?>        
            </tr>
        </thead>
        <tbody id="moowoodle-manage-enrollment-tablebody">
<?php
$customer_orders = get_posts( array(
      'numberposts' => -1,
      'orderby' => 'date',
      'order' => 'DESC',
      'post_type' => 'shop_order',
      'post_status' => 'wc-completed'
    ) );

foreach ( $customer_orders as $customer_order ) {
    $order = wc_get_order( $customer_order->ID );
    $user = $order->get_user();
    $moowoodle_moodle_user_id = 0;
    $users = moowoodle_moodle_core_function_callback( 'get_moodle_users', array( 'criteria' => array( array( 'key' => 'email', 'value' => $user->user_email ) ) ) );
    if( ! empty( $users ) && ! empty( $users['users'] ) ) {
        $moowoodle_moodle_user_id = $users['users'][0]['id'];
    }
    $unenrolled_course = get_post_meta( $customer_order->ID, '_course_unenroled',true );
    $unenrolled_courses[] = null;
      if($unenrolled_course != null){
        $unenrolled_courses = str_contains($unenrolled_course,',') ? explode(',', $unenrolled_course) : array($unenrolled_course);
      }
    foreach ( $order->get_items() as $enrolment ) {
    $customer_id = (int)get_post_meta( $customer_order->ID, '_customer_user', true );
      $linked_course_id = get_post_meta( $enrolment->get_product_id(), 'linked_course_id', true );
      $course_link = get_moowoodle_course_url( $linked_course_id, 'View' );
      $enrolment_date = get_post_meta( $order->get_id(), 'moodle_user_enrolment_date', true );
      $product_course = get_post_meta( $enrolment->get_product_id(), 'moodle_course_id', true );
	if (!$product_course || in_array($linked_course_id,$unenrolled_courses)) continue;
 ?>
 <tr>
    <td>
      <?php _e( get_the_title( $enrolment->get_product_id() ) ) ?>
    </td>
    <td>
        <?php _e((get_user_by( 'id', $customer_id ))->user_login); ?>
    </td>
      <td>
          <?php
            if ( ! empty( $enrolment_date ) ) {
              _e( get_date_from_gmt( date( 'Y-m-d H:i:s', $enrolment_date ) ) );
            }                      
          ?>
      </td>
      <td>
          
          <form method="post">
            <input type="hidden" name="user_id" value="<?php echo $moowoodle_moodle_user_id ?>"/>
            <input type="hidden" name="order_id" value="<?php echo $customer_order->ID  ?>"/>
            <input type="hidden" name="course_id" value="<?php echo $linked_course_id ?>"/>
            <button type="submit" name="unenroll" class="button-secondary">Unenroll</button>
        </form>

      </td>
      <?php echo apply_filters('moowoodle_manage_enrollment_body', '' ,$customer_order); ?>
        </td>
      </tr>              
    <?php
    }
  }
  ?>           
</tbody>
</table>
</div>

<script>
$(document).ready(function() {
    var $rows = $('#moowoodle-manage-enrollment-tablebody tr');
    $('#searchInput').keyup(function() {
        var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

        $rows.show().filter(function() {
            var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(val);
        }).hide();
    });
});
</script>
<?php 

