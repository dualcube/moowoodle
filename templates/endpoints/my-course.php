<?php
/**
 * My Course (endpoint)
 */

defined( 'ABSPATH' ) || exit;

// Extract the argument provided by template loader.
extract( $args );

// Render template
if ( count( $customer_orders ) > 0 ) {
    ?>
    <p>
        <div class="auto">
            <table id="moowoodle_table" class="table table-bordered responsive-table moodle-linked-courses widefat">
                <thead>
                    <tr>
                        <?php foreach ( $table_heading as $heading_key => $heading_value ) : ?>
                            <th><?php echo $heading_value; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ( $customer_orders as $order ) {
                        // Get unenrolled course
                        $unenrolled_course  = $order->get_meta( '_course_unenroled', true );
                        $unenrolled_courses = $unenrolled_course ? explode( ',', $unenrolled_course ) : [];
                        
                        foreach ( $order->get_items() as $enrolment ) {
                            $moodle_course_id = get_post_meta( $enrolment->get_product_id(), 'moodle_course_id', true );
                            $enrolment_date   = $order->get_meta( 'moodle_user_enrolment_date', true );
                            $linked_course_id = get_post_meta( $enrolment->get_product_id(), 'linked_course_id', true );

                            if ( $linked_course_id && !in_array( $moodle_course_id, $unenrolled_courses ) ) :
                                ?>
                                <tr>
                                    <td><?php echo( get_the_title( $enrolment->get_product_id() ) ); ?></td>
                                    <td><?php echo( $customer->user_login ); ?></td>
                                    <?php if( $password ) :?>
                                    <td><?php echo( $password ); ?></td>
                                    <?php endif; ?>
                                    <td>
                                        <?php
                                        if ( ! empty( $enrolment_date ) ) {
                                            echo( get_date_from_gmt( gmdate( 'M j, Y-H:i', $enrolment_date ) ) );
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button type="button" class="button-tri">
                                            <?php echo apply_filters( 'moodle_course_view_url', MooWoodle()->course->get_course_url( $moodle_course_id, 'View' ), $moodle_course_id ); ?>
                                            <span class="dashicons dashicons-visibility"></span>
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            endif;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </p>
    <?php
} else {
    ?>
        <h3><?php _e( 'You have no Course.', 'moowoodle' ); ?></h3>
        <h3><?php _e( 'Kindly purchase a Course and come back here to see your course.', 'moowoodle' ); ?></h3>
    <?php
}
