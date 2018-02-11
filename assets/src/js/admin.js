/**
 * Created by Nabeel on 2016-02-02.
 */
(function ( w, $, undefined ) {
	$( function () {

		var $recipients = $( '#mail-application-recipients' ).select2( {
			width: '100%',
			tags : true
		} );

		$( '#wpjm_ma_mail_application' ).on( 'click', 'button.button-primary', function ( e ) {

			var $this      = $( e.currentTarget ),
			    recipients = $.map( $recipients.select2( 'data' ), function ( item ) {
				    return item.text;
			    } );

			if ( !recipients.length ) {
				return true;
			}

			var request_args = $.extend( {}, $this.data(), { recipients: recipients } );

			$this.prop( 'disabled', true ).addClass( 'loading' );

			$.post( ajaxurl, request_args, function ( response ) {
				alert( response.data );
			} ).always( function () {
				$this.prop( 'disabled', false ).removeClass( 'loading' );
			} );
		} );

	} );
})( window, jQuery );