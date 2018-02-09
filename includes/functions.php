<?php
/**
 * Created by Nabeel
 * Date: 2016-01-22
 * Time: 2:38 AM
 *
 * @package WP_Job_Manager_Mail_Applications
 */

use WP_Job_Manager_Mail_Applications\Component;
use WP_Job_Manager_Mail_Applications\Plugin;

if ( ! function_exists( 'wp_job_manager_mail_applications' ) ):
	/**
	 * Get plugin instance
	 *
	 * @return Plugin
	 */
	function wp_job_manager_mail_applications() {
		return Plugin::get_instance();
	}
endif;

if ( ! function_exists( 'wpjm_ma_component' ) ):
	/**
	 * Get plugin component instance
	 *
	 * @param string $component_name
	 *
	 * @return Component|null
	 */
	function wpjm_ma_component( $component_name ) {
		if ( isset( wp_job_manager_mail_applications()->$component_name ) ) {
			return wp_job_manager_mail_applications()->$component_name;
		}

		return null;
	}
endif;

if ( ! function_exists( 'wpjm_ma_view' ) ):
	/**
	 * Load view
	 *
	 * @param string  $view_name
	 * @param array   $args
	 * @param boolean $return
	 *
	 * @return void
	 */
	function wpjm_ma_view( $view_name, $args = null, $return = false ) {
		if ( $return ) {
			// start buffer
			ob_start();
		}

		wp_job_manager_mail_applications()->load_view( $view_name, $args );

		if ( $return ) {
			// get buffer flush
			return ob_get_clean();
		}
	}
endif;

if ( ! function_exists( 'wpjm_ma_version' ) ):
	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	function wpjm_ma_version() {
		return wp_job_manager_mail_applications()->version;
	}
endif;