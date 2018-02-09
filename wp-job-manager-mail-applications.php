<?php namespace WP_Job_Manager_Mail_Applications;
/**
 * Plugin Name: WP Job Manager - Mail Applications
 * Description: Quick way to mail job application with stored maillist
 * Version: 1.0.0
 * Author: Nabeel Molham
 * Author URI: https://nabeel.molham.me/
 * Text Domain: wp-job-manager-mail-applications
 * Domain Path: /languages
 * License: GNU General Public License, version 3, http://www.gnu.org/licenses/gpl-3.0.en.html
 */

if ( ! defined( 'WPINC' ) ) {
	// Exit if accessed directly
	die();
}

/**
 * Constants
 */

// plugin master file
define( 'WPJM_MA_MAIN_FILE', __FILE__ );

// plugin DIR
define( 'WPJM_MA_DIR', plugin_dir_path( WPJM_MA_MAIN_FILE ) );

// plugin URI
define( 'WPJM_MA_URI', plugin_dir_url( WPJM_MA_MAIN_FILE ) );

// localization text Domain
define( 'WPJM_MA_DOMAIN', 'wp-job-manager-mail-applications' );

require_once WPJM_MA_DIR . 'includes/classes/Singular.php';
require_once WPJM_MA_DIR . 'includes/helpers.php';
require_once WPJM_MA_DIR . 'includes/functions.php';

/**
 * Plugin main component
 *
 * @package WP_Job_Manager_Mail_Applications
 */
class Plugin extends Singular {
	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Backend
	 *
	 * @var Backend
	 */
	public $backend;

	/**
	 * Backend
	 *
	 * @var Frontend
	 */
	public $frontend;

	/**
	 * Backend
	 *
	 * @var Ajax_Handler
	 */
	public $ajax;

	/**
	 * Initialization
	 *
	 * @return void
	 */
	protected function init() {
		// load language files
		add_action( 'plugins_loaded', [ &$this, 'load_language' ] );

		// autoloader register
		spl_autoload_register( [ &$this, 'autoloader' ] );

		// modules
		$this->ajax     = Ajax_Handler::get_instance();
		$this->backend  = Backend::get_instance();
		$this->frontend = Frontend::get_instance();

		// plugin loaded hook
		do_action_ref_array( 'wpjm_ma_loaded', [ &$this ] );
	}

	/**
	 * Load view template
	 *
	 * @param string $view_name
	 * @param array  $args ( optional )
	 *
	 * @return void
	 */
	public function load_view( $view_name, $args = null ) {
		// build view file path
		$__view_name     = $view_name;
		$__template_path = WPJM_MA_DIR . 'views/' . $__view_name . '.php';
		if ( ! file_exists( $__template_path ) ) {
			// file not found!
			wp_die( sprintf( __( 'Template <code>%s</code> File not found, calculated path: <code>%s</code>', WPJM_MA_DOMAIN ), $__view_name, $__template_path ) );
		}

		// clear vars
		unset( $view_name );

		if ( ! empty( $args ) ) {
			// extract passed args into variables
			extract( $args, EXTR_OVERWRITE );
		}

		/**
		 * Before loading template hook
		 *
		 * @param string $__template_path
		 * @param string $__view_name
		 */
		do_action_ref_array( 'wpjm_ma_load_template_before', [ &$__template_path, $__view_name, $args ] );

		/**
		 * Loading template file path filter
		 *
		 * @param string $__template_path
		 * @param string $__view_name
		 *
		 * @return string
		 */
		require apply_filters( 'wpjm_ma_load_template_path', $__template_path, $__view_name, $args );

		/**
		 * After loading template hook
		 *
		 * @param string $__template_path
		 * @param string $__view_name
		 */
		do_action( 'wpjm_ma_load_template_after', $__template_path, $__view_name, $args );
	}

	/**
	 * Language file loading
	 *
	 * @return void
	 */
	public function load_language() {
		load_plugin_textdomain( WPJM_MA_DOMAIN, false, dirname( plugin_basename( WPJM_MA_MAIN_FILE ) ) . '/languages' );
	}

	/**
	 * System classes loader
	 *
	 * @param $class_name
	 *
	 * @return void
	 */
	public function autoloader( $class_name ) {
		if ( strpos( $class_name, __NAMESPACE__ ) === false ) {
			// skip non related classes
			return;
		}

		$class_path = WPJM_MA_DIR . 'includes' . DIRECTORY_SEPARATOR . 'classes' . str_replace( [
				__NAMESPACE__,
				'\\',
			], [ '', DIRECTORY_SEPARATOR ], $class_name ) . '.php';

		if ( file_exists( $class_path ) ) {
			// load class file if found
			require_once $class_path;
		}
	}
}

// boot up the system
wp_job_manager_mail_applications();