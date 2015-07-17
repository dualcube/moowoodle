<?php
/**
 * New account email (plain)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
global $DC_Woodle;
?>

<p><?php _e( 'Email: ', $DC_Woodle->text_domain ); echo $credential['email']; ?></p>
<p><?php _e( 'Username: ', $DC_Woodle->text_domain ); echo $credential['email']; ?></p>
<p><?php _e( 'Password: ', $DC_Woodle->text_domain ); echo $credential['email']; ?></p>
<p><a href="<?php echo woodle_get_settings( 'access_url', 'dc_woodle_general' ); ?>"><?php _e( 'Login Now', $DC_Woodle->text_domain ); ?></a></p>