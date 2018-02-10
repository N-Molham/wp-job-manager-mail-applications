/**
 * Created by Nabeel on 2016-02-02.
 */
(function ( w, $, undefined ) {
	$( function () {

		$( '#mail-application-recipients' ).select2( {
			width: '100%',
			tags : true
		} );

	} );
})( window, jQuery );