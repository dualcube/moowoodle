<?php
/**
 * New enrollment email (html)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
global $DC_Woodle;
?>

<?php do_action('woocommerce_email_header', $email_heading); ?>
<p><?php _e( 'Your are enrolled in ' . $enrollment['course_name'] . '.', $DC_Woodle->text_domain ) ; ?></p>
<p><a href="<?php echo $enrollment['course_url']; ?>"><?php _e( 'View Course', $DC_Woodle->text_domain ); ?></a></p>
<?php do_action( 'woocommerce_email_footer' ); ?>