<?php
/**
 * My Course (endpoint)
 */

defined('ABSPATH') || exit;

// Extract the argument provided by template loader.
extract($args);

// Render template
?>
<div class="auto">
    <?php if ( count($enrollments) > 0): ?>
        <p><?php _e( 'Total Courses: ', 'moowoodle-pro' ) ?><?php echo count($enrollments); ?></p>
    <?php endif; ?>

    <table id="moowoodle_table" class="moowoodle-table shop_table shop_table_responsive my_account_orders ">
        <thead>
            <tr>
                <?php foreach ($table_heading as $heading_value): ?>
                    <th class="woocommerce-orders-table__header-order-number"><?php echo $heading_value; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($enrollments) > 0) {
                foreach ($enrollments as $enrollment) {
                    $linked_course_id = $enrollment[ 'course_id' ];
                    $moodle_course_id = get_post_meta( $linked_course_id, 'moodle_course_id', true );
                    $item    		  = new \WC_Order_Item_Product( $enrollment[ 'item_id' ] );
                    $enrolment_date   = $enrollment[ 'date' ];

                    ?>
                        <tr>
                            <td class="woocommerce-orders-table__cell-order-number"><?php echo get_the_title($item->get_product_id()); ?></td>
                            <td class="woocommerce-orders-table__cell-order-number"><?php echo $customer->user_login; ?></td>
                            <?php if ($password): ?>
                                <td class="woocommerce-orders-table__cell-order-number"><?php echo $password; ?></td>
                            <?php endif; ?>
                            <td class="woocommerce-orders-table__cell-order-number">
                                <?php
                                if (!empty($enrolment_date)) {
                                    echo $enrolment_date;
                                }
                                ?>
                            </td>
                            <td class="woocommerce-orders-table__cell-order-number">
                                <a target="_blank" class="woocommerce-button wp-element-button moowoodle" href="
                                <?php echo apply_filters('moodle_course_view_url', MooWoodle()->course->get_course_url( $moodle_course_id ), $moodle_course_id); ?>
                                "><?php _e( 'View', 'moowoodle-pro' ) ?></a>
                            </td>
                        </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="5" class="no-data-row"><?php _e( 'You haven\'t purchase any course yet.', 'moowoodle-pro' ) ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>
