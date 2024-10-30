( function( $, params, wp ) {
	"use strict";
	$( function() {

		$( document.body ).on( 'click', '.memberhero_ajax', function( event ) {
			event.preventDefault();
			var $this     = $( this ),
				$ajax_url = $( this ).attr( 'href' ),
				$spinner  = $this.parents( 'td' ).find( '.spinner' ),
				$updater  = $this.parents( 'td' ).find( '.updater' );

			$spinner.show();
			$updater.hide();

			$.get( $ajax_url, function( response ) {
				$spinner.hide();
				if ( response.updated ) {
					$updater.html( response.updated ).fadeIn().delay( 1000 ).fadeOut( 'fast' );
					$this.addClass( 'disabled' );
					if ( response.html ) {
						$this.replaceWith( response.html );
					}
				}
				$( document.body ).trigger( 'memberhero-init-tooltips' );
			} );

		} );

	} );
} ) ( jQuery, memberhero_users_params, wp );