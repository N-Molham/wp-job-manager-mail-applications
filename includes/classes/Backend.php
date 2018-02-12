<?php namespace WP_Job_Manager_Mail_Applications;

use WP_Post;

/**
 * Backend logic
 *
 * @package WP_Job_Manager_Mail_Applications
 */
class Backend extends Component {

	/**
	 * @var bool
	 */
	protected $_meta_box_rendered;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init() {
		parent::init();

		add_action( 'add_meta_boxes', [ $this, 'register_mail_meta_box' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'load_assets' ] );

		add_filter( 'job_manager_job_applications_admin_actions', [ $this, 'add_send_mail_job_applications_admin_action' ], 20, 2 );

	}

	/**
	 * @param array   $actions
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	public function add_send_mail_job_applications_admin_action( $actions, $post ) {

		$actions = array_merge( [
			'mail_application' => [
				'action' => 'mail-application',
				'name'   => __( 'Mail Application', WPJM_MA_DOMAIN ),
				'url'    => '#mail-application-' . $post->ID,
			],
		], $actions );

		echo '<div class="hidden">';
		$this->render_mail_application_meta_box( $post );
		echo '</div>';

		return $actions;
	}

	/**
	 * @param string $post_type
	 *
	 * @return void
	 */
	public function register_mail_meta_box( $post_type = '' ) {

		if ( 'job_application' !== $post_type ) {
			return;
		}

		$this->load_assets();

		add_meta_box( 'wpjm_ma_mail_application', __( 'Mail Application', WPJM_MA_DOMAIN ), [ $this, 'render_mail_application_meta_box' ], $post_type, 'side' );
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	public function render_mail_application_meta_box( $post ) {

		$saved_emails = get_option( 'wpjm_ma_emails', [] );

		wpjm_ma_view( 'meta_box', compact( 'post', 'saved_emails' ) );

	}

	/**
	 * @return void
	 */
	public function load_assets() {

		if ( 'job_application' !== get_current_screen()->post_type ) {
			return;
		}

		$assets_path    = untrailingslashit( WPJM_MA_URI ) . '/assets/';
		$assets_version = Helpers::assets_version();

		// Select2 library
		wp_register_style( 'select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css' );
		wp_register_script( 'select2-script', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js', [ 'jquery' ] );

		// webui popover
		wp_register_style( 'jquery-webui-popover-style', Helpers::enqueue_path() . 'css/jquery.webui-popover.css', null, $assets_version );
		wp_register_script( 'jquery-webui-popover-script', Helpers::enqueue_path() . 'js/jquery.webui-popover.js', [ 'jquery' ], $assets_version );

		// main
		wp_enqueue_style( 'wpjm-ma-style', $assets_path . 'dist/css/admin.css', [ 'select2-style', 'jquery-webui-popover-style' ], $assets_version );
		wp_enqueue_script( 'wpjm-ma-script', Helpers::enqueue_path() . 'js/admin.js', [ 'select2-script', 'jquery-webui-popover-script' ], $assets_version, true );

		// localization
		wp_localize_script( 'wpjm-ma-script', 'wpjm_ma', [
			'i18n' => [
				'placeholder' => __( 'Select recipient(s)', WPJM_MA_DOMAIN ),
			],
		] );

	}

	/**
	 * Get the default email content
	 * @return string
	 */
	public function get_job_application_email_content() {
		$message = <<<EOF
Hello

A candidate ([from_name]) has submitted their application for the position "[job_title]".

=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

[message]

=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

[meta_data]

You can contact them directly at: [from_email]
EOF;

		return $message;
	}
}
