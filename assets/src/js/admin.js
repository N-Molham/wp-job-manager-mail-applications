/**
 * Created by Nabeel on 2016-02-02.
 */
(function ( w, $, undefined ) {
	$( function () {

		$( '#the-list' ).find( 'a.button.icon-mail-application' ).each( function ( index, link ) {
			$( this ).webuiPopover( {
				url: link.hash
			} )
		} );

		$( '.wpjm-ma-mail-application' ).each( function () {
			var $application = $( this ),
			    $recipients  = $application.find( 'select' ).select2( {
				    width: '100%',
				    tags : true
			    } );

			$application.on( 'click', 'button.button-primary', function ( e ) {

				var $button    = $( e.currentTarget ),
				    recipients = $.map( $recipients.select2( 'data' ), function ( item ) {
					    return item.text;
				    } );

				if ( !recipients.length ) {
					return true;
				}

				var request_args = $.extend( {}, $button.data(), { recipients: recipients } );

				$button.prop( 'disabled', true ).addClass( 'loading' );

				$.post( ajaxurl, request_args, function ( response ) {
					alert( response.data );
				} ).always( function () {
					$button.prop( 'disabled', false ).removeClass( 'loading' );
				} );
			} );
		} );

	} );
})( window, jQuery );