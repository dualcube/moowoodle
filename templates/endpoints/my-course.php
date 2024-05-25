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
    <?php if (count($customer_orders) > 0): ?>
        <p>Total Courses: <?php echo count($customer_orders); ?></p>
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
            if (count($customer_orders) > 0) {
                foreach ($customer_orders as $order) {
                    // Get unenrolled course
                    $unenrolled_course = $order->get_meta('_course_unenroled', true);
                    $unenrolled_courses = $unenrolled_course ? explode(',', $unenrolled_course) : [];

                    foreach ($order->get_items() as $enrolment) {
                        $moodle_course_id = get_post_meta($enrolment->get_product_id(), 'moodle_course_id', true);
                        $enrolment_date = $order->get_meta('moodle_user_enrolment_date', true);
                        $linked_course_id = get_post_meta($enrolment->get_product_id(), 'linked_course_id', true);

                        if ($linked_course_id && !in_array($moodle_course_id, $unenrolled_courses)):
            ?>
                            <tr>
                                <td class="woocommerce-orders-table__cell-order-number"><?php echo get_the_title($enrolment->get_product_id()); ?></td>
                                <td class="woocommerce-orders-table__cell-order-number"><?php echo $customer->user_login; ?></td>
                                <?php if ($password): ?>
                                    <td class="woocommerce-orders-table__cell-order-number"><?php echo $password; ?></td>
                                <?php endif; ?>
                                <td class="woocommerce-orders-table__cell-order-number">
                                    <?php
                                    if (!empty($enrolment_date)) {
                                        echo get_date_from_gmt(gmdate('M j, Y-H:i', $enrolment_date));
                                    }
                                    ?>
                                </td>
                                <td class="woocommerce-orders-table__cell-order-number">
                                    <?php echo apply_filters('moodle_course_view_url', MooWoodle()->course->get_course_url($moodle_course_id, 'View'), $moodle_course_id); ?>
                                </td>
                            </tr>
                            <?php
                        endif;
                    }
                }
            } else {
                ?>
                <tr>
                    <td colspan="5" class="no-data-row">You haven't purchase any course yet.</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>
