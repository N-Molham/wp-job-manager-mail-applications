<?php namespace WP_Job_Manager_Mail_Applications;

use WP_Post;

/**
 * Backend logic
 *
 * @package WP_Job_Manager_Mail_Applications
 */
class Backend extends Component {
	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init() {
		parent::init();

		add_action( 'add_meta_boxes', [ $this, 'register_mail_meta_box' ] );
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
	 * @param array   $meta_box
	 *
	 * @return void
	 */
	public function render_mail_application_meta_box( $post, $meta_box ) {

		$saved_emails = get_option( 'wpjm_ma_emails', [] );
		
		wpjm_ma_view( 'meta_box', compact( 'post', 'meta_box', 'saved_emails' ) );

	}

	/**
	 * @return void
	 */
	public function load_assets() {

		$assets_path = untrailingslashit( WPJM_MA_URI ) . '/assets/';

		// Select2 library
		wp_register_style( 'select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css' );
		wp_register_script( 'select2-script', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js', [ 'jquery' ] );

		// main
		wp_enqueue_style( 'wpjm-ma-style', $assets_path . 'dist/css/admin.css', [ 'select2-style' ], Helpers::assets_version() );
		wp_enqueue_script( 'wpjm-ma-script', Helpers::enqueue_path() . 'js/admin.js', [ 'select2-script' ], Helpers::assets_version(), true );

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
