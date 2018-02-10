<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 10-Feb-18
 * Time: 3:23 PM
 */
?>

<select id="mail-application-recipients" multiple="multiple"></select>

<button type="button" class="button button-primary button-large" 
        data-post="<?php echo esc_attr( $post->ID ); ?>" 
        data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpjm_ma_' . $post->ID ) )?>">
	<span class="spinner"></span>
	<?php _e( 'Send Application', WPJM_MA_DOMAIN ); ?>
</button>