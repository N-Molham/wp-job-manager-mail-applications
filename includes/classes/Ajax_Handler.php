<?php namespace WP_Job_Manager_Mail_Applications;

/**
 * AJAX handler
 *
 * @package WP_Job_Manager_Mail_Applications
 */
class Ajax_Handler extends Component {
	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init() {
		parent::init();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$action = filter_var( isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '', FILTER_SANITIZE_STRING );
			if ( method_exists( $this, $action ) ) {
				// hook into action if it's method exists
				add_action( 'wp_ajax_' . $action, [ &$this, $action ] );
			}
		}
	}

	/**
	 * @return void
	 */
	public function wpjm_mail_application() {

		check_ajax_referer( 'wpjm_mail_application', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			$this->error( __( 'Insufficient permissions!', WPJM_MA_DOMAIN ) );
		}

		// sanitize inputs
		$application_id = absint( filter_input( INPUT_POST, 'application', FILTER_SANITIZE_NUMBER_INT ) );
		$recipients     = array_map( 'is_email', (array) filter_input( INPUT_POST, 'recipients', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY ) );

		// validation
		$recipients = array_filter( $recipients );
		if ( empty( $application_id ) || empty( $recipients ) ) {
			$this->error( __( 'Invalid inputs!', WPJM_MA_DOMAIN ) );
		}

		$application = get_post( $application_id );
		if ( empty( $application ) || 'job_application' !== $application->post_type ) {
			$this->error( __( 'Invalid application!', WPJM_MA_DOMAIN ) );
		}

		$existing_shortcode_tags = $GLOBALS['shortcode_tags'];
		remove_all_shortcodes();

		$job_id      = $application->post_parent;
		$attachments = get_post_meta( $application_id, '_attachment_file', true );

		job_application_email_add_shortcodes( [
			'application_id'      => $application_id,
			'job_id'              => $job_id,
			'user_id'             => get_post_meta( $application_id, '_candidate_user_id', true ),
			'candidate_name'      => $application->post_title,
			'candidate_email'     => get_post_meta( $application_id, '_candidate_email', true ),
			'application_message' => $application->post_content,
			'meta'                => [
				__( 'Phone', WPJM_MA_DOMAIN ) => get_post_meta( $application_id, 'Phone', true ),
			],
		] );

		$subject = do_shortcode( get_job_application_email_subject() );
		$message = do_shortcode( get_job_application_email_content() );
		$message = str_replace( "\n\n\n\n", "\n\n", implode( "\n", array_map( 'trim', explode( "\n", $message ) ) ) );
		$is_html = ( $message != strip_tags( $message ) );

		// Does this message contain formatting already?
		if ( $is_html && false === strpos( $message, '<p' ) && false === strpos( $message, '<br' ) ) {
			$message = nl2br( $message );
		}

		$GLOBALS['shortcode_tags'] = $existing_shortcode_tags;

		$headers = [
			$is_html ? 'Content-Type: text/html' : 'Content-Type: text/plain',
			'charset=utf-8',
		];

		$recipients = array_map( function ( $send_to ) use ( $job_id, $application_id ) {
			return apply_filters( 'create_job_application_notification_recipient', $send_to, $job_id, $application_id );
		}, $recipients );

		wp_mail(
			$recipients,
			apply_filters( 'create_job_application_notification_subject', $subject, $job_id, $application_id ),
			apply_filters( 'create_job_application_notification_message', $message ),
			apply_filters( 'create_job_application_notification_headers', $headers, $job_id, $application_id ),
			apply_filters( 'create_job_application_notification_attachments', $attachments, $job_id, $application_id )
		);

		$saved_emails = array_unique( array_merge( get_option( 'wpjm_ma_emails', [] ), $recipients ) );
		update_option( 'wpjm_ma_emails', $saved_emails );

		$this->success( __( 'Application sent to: ', WPJM_MA_DOMAIN ) . implode( ', ', $recipients ) );
	}

	/**
	 * AJAX Debug response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function debug( $data ) {
		// return dump
		$this->error( $data );
	}

	/**
	 * AJAX Debug response ( dump )
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $args
	 *
	 * @return void
	 */
	public function dump( $args ) {
		// return dump
		$this->error( print_r( func_num_args() === 1 ? $args : func_get_args(), true ) );
	}

	/**
	 * AJAX Error response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function error( $data ) {
		wp_send_json_error( $data );
	}

	/**
	 * AJAX success response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function success( $data ) {
		wp_send_json_success( $data );
	}

	/**
	 * AJAX JSON Response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $response
	 *
	 * @return void
	 */
	public function response( $response ) {
		// send response
		wp_send_json( $response );
	}
}
