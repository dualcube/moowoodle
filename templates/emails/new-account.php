<?php
/**
 * New account email (html)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
global $DC_Woodle;
?>

<?php do_action('woocommerce_email_header', $email_heading); ?>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<tbody>
		<tr>
			<td scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Email', $DC_Woodle->text_domain ); ?></td>
			<td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $credential['email']; ?></td>
		</tr>
		<tr>
			<td scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Username', $DC_Woodle->text_domain ); ?></td>
			<td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $credential['username']; ?></td>
		</tr>
		<tr>
			<td scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Password', $DC_Woodle->text_domain ); ?></td>
			<td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $credential['password']; ?></td>
		</tr>
		<tr>
			<td colspan="2" scope="col" style="text-align:left; border: 1px solid #eee;"><a href="<?php echo woodle_get_settings( 'access_url', 'dc_woodle_general' ); ?>"><?php _e( 'Login Now', $DC_Woodle->text_domain ); ?></a></td>
		</tr>
	</tbody>
	<tfoot></tfoot>
</table>
<?php do_action( 'woocommerce_email_footer' ); ?>