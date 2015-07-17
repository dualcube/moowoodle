<?php
/**
 * New enrollment email (plain)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
global $DC_Woodle;
?>

<p><?php _e( 'Your are enrolled in ' . $enrollment['course_name'] . '.', $DC_Woodle->text_domain ) ; ?></p>
<p><a href="<?php echo $enrollment['course_url']; ?>"><?php _e( 'View Course', $DC_Woodle->text_domain ); ?></a></p>