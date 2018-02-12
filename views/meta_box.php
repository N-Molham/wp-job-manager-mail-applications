<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 10-Feb-18
 * Time: 3:23 PM
 */
?>
<div id="mail-application-<?php echo $post->ID; ?>" class="wpjm-ma-mail-application">
	<label for="mail-application-recipients-<?php echo $post->ID; ?>"><?php _e( 'Select recipient(s) and/or add new ones', WPJM_MA_DOMAIN ); ?></label>

	<select id="mail-application-recipients-<?php echo $post->ID; ?>" multiple="multiple"><?php
		foreach ( $saved_emails as $email ) {
			echo '<option value="', esc_attr( $email ), '">', $email, '</option>';
		}
		?></select>

	<button type="button" class="button button-primary button-large"
	        data-action="wpjm_mail_application"
	        data-application="<?php echo esc_attr( $post->ID ); ?>"
	        data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpjm_mail_application' ) ) ?>">
		<span class="spinner"></span>
		<?php _e( 'Send Application', WPJM_MA_DOMAIN ); ?>
	</button>
</div>