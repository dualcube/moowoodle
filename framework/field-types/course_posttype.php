<?php

// from heading
$from_heading = apply_filters( 'moowoodle_courses_heading', 
                                    array(
                                           __( "Moodle Course Name", 'moowoodle' ),
                                           __( "Short Name", 'moowoodle' ),
                                           __( "Product Name", 'moowoodle' ),
                                           __( "Category", 'moowoodle' ),
                                           __( "Number of Enrolled User", 'moowoodle' ),
                                           __( "Date", 'moowoodle' ),  
                                           __( "Actions", 'moowoodlepro' ) . apply_filters('moowoodle_pro_sticker',' pro'),
                                        )
                                );

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<div class="auto">

    <table id="moowoodle_table" class="table table-bordered responsive-table moodle-linked-courses widefat">
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

        <tbody>
        <?php
        global $MooWoodle;
         $courses = get_posts( array( 'post_type' => 'course', 'numberposts' => -1, 'post_status' => 'publish' ) );
        
            if ( ! empty( $courses ) ) {
                foreach ( $courses as $course ) {
                    $id = get_post_meta( $course->ID, 'moodle_course_id', true );
                    $course_short_name = get_post_meta( $course->ID, '_course_short_name', true );
                    $product = get_posts( array( 'post_type' => 'product', 'numberposts' => -1, 'post_status' => 'publish' , 'name' => $course_short_name) );
                    $course_startdate = get_post_meta( $course->ID, '_course_startdate', true );
                    $course_enddate = get_post_meta( $course->ID, '_course_enddate', true );

                    $course_name = $course->post_title;
                    $visibility = get_post_meta( $course->ID, '_visibility', true );
                    $course_idnumber = get_post_meta( $course->ID, '_course_idnumber', true );

                    $category_id = get_post_meta( $course->ID, '_category_id', true );
                    $term_id = moowoodle_get_term_by_moodle_id( $category_id, 'course_cat', 'moowoodle_term' );
                    $course_category_path = get_term_meta( $term_id, '_category_path', true );
                    $category_ids = explode( '/', $course_category_path );
                    $course_path = array();
                        
                    if ( ! empty( $category_ids ) ) {
                        foreach ( $category_ids as $cat_id ) {
                            if ( ! empty( $cat_id ) ) {
                                $term_id = moowoodle_get_term_by_moodle_id( intval( $cat_id ), 'course_cat', 'moowoodle_term' );
                                $term = get_term( $term_id, 'course_cat' );
                                $course_path[] = $term->name;
                            }
                        }
                    }
                        
                    if ( ! empty( $course_path ) ) {
                        $course_path = implode( ' / ', $course_path );
                    }
                    
                    $term = get_term_by( 'id', $term_id, 'course_cat' );
                    $sort_url = '';
                    if ( ! empty( $term ) ) {
                        $sort_url = '<a href="' . admin_url( 'edit.php?course_cat=' . $term->slug . '&post_type=course' ) . '">' . $course_path . '</a>';
                    }





                    $moodle_url = '';
                    if($id){
                        $moodle_url = '<a href="' . $MooWoodle->options_general_settings[ "moodle_url" ]. 'course/edit.php?id=' . $id . '">' . $course_name . '</a>';
                        // $moodle_url = '<a href="' . admin_url( 'edit.php?course_cat=' . $term->slug . '&post_type=course' ) . '">' . $course_path . '</a>';
                    }
                    $product_name = '';
                    if($product){
                        $product_name = '<a href="' . admin_url(). 'post.php?post=' . $product[0]->ID . '&action=edit">' . $product[0]->post_title . '</a>';
                    }

                    $enroled_user = '';
                    $customer_orders = get_posts( array(
                      'numberposts' => -1,
                      'orderby' => 'date',
                      'order' => 'DESC',
                      'post_type' => 'shop_order',
                      'post_status' => 'wc-completed'
                    ) );
                    $count_enrolment = 0;
                    foreach ( $customer_orders as $customer_order ) {
                        $order = wc_get_order( $customer_order->ID );
                        foreach ( $order->get_items() as $enrolment ) {
                            $linked_course_id = get_post_meta( $enrolment->get_product_id(), 'linked_course_id', true );
                            if($linked_course_id == $id) $count_enrolment++;
                        }
                    }

                    $enroled_user= $count_enrolment;
                    $date = 'Start Date- ' . wp_date('F j, Y  ',$course_startdate);
                    if($course_enddate){
                        $date .= 'End Date- ' . wp_date('F j, Y  ',$course_enddate);
                    }
                   $table_body = '<td>use pro to access</td>'; 

               ?>
                <tr>
                    <td >
                        <?php echo $moodle_url; ?>
                    </td>
                    <td>
                        <?php echo $course_short_name; ?>
                    </td>
                    <td>
                        <?php echo $product_name; ?> 
                    </td>
                    <td>
                        <?php echo $sort_url; ?>
                    </td>
                    <td>
                        <?php echo $enroled_user; ?>
                    </td>
                    <td>
                        <?php echo $date; ?>
                    </td>
                    <?php echo apply_filters('moowoodle_courses_body', $table_body , $course, $product); ?>
                </tr>
                 <?php
                }
            }
        ?>
        </tbody>
    </table>
</div>
<br>

<script>
    //moowoodle_table
$(document).ready(function() {
        var myTable = $("#moowoodle_table").DataTable({
          paging: false,
          searching: true,
          info: false,
        });
        myTable
          .columns()
          .flatten()
          .each(function (colID) {
            //Manage column is not filterable
            if(colID == 4 || colID == 6) return;
            // Create the select list in the
            // header column header
            // On change of the list values,
            // perform search operation
            var mySelectList = $("<select />")
              .appendTo(myTable.column(colID).header())
              .on("change", function () {
                myTable.column(colID).search($(this).val());
 
                // update the changes using draw() method
                myTable.column(colID).draw();
              });
 
            // Get the search cached data for the
            // first column add to the select list
            // using append() method
            // sort the data to display to user
            // all steps are done for EACH column
          mySelectList.append(
              $('<option value="">select</option>')
            );
            myTable
              .column(colID)
              .cache("search")
              .unique()
              .sort()
              .each(function (param) {
                mySelectList.append(
                  $('<option value="' + param + '">'
                    + param + "</option>")
                );
              });
          });
    
    
});
</script>
<?php
$suffix = defined( 'MOOWOODLE_SCRIPT_DEBUG' ) && MOOWOODLE_SCRIPT_DEBUG ? '' : '.min';
wp_enqueue_style( 'woocommerce_course_css', $MooWoodle->plugin_url . 'framework/field-types/css/course_posttype' . $suffix . '.css', array(), $MooWoodle->version );