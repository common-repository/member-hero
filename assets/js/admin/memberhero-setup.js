jQuery( function( $ ) {

	// Toggles
	$( document.body ).on( 'memberhero-init-toggles', function() {
		$( '.memberhero-toggle' ).each( function() {
			var cb = $( this ).parents( 'fieldset' ).find( 'input[type=checkbox]' );
			$( this ).toggles( {
				'width': 	50,
				'height': 	25,
				'text':		{},
				'checkbox': cb
			} );
		} )
	} ).trigger( 'memberhero-init-toggles' );

	// Tooltips
	$( document.body ).on( 'memberhero-init-tooltips', function() {
		$( '.memberhero-help-tip, .tips' ).tipTip( {
			'attribute': 	'data-tip',
			'fadeIn': 		50,
			'fadeOut': 		50,
			'delay': 		200
		} );
	} ).trigger( 'memberhero-init-tooltips' );

	// Progress
	$( document.body ).on( 'memberhero-init-progress', function() {
		if ( $( document.body ).find( '.memberhero-setup-checklist' ).length > 0 ) {
			var cl = $( document.body ).find( '.memberhero-setup-checklist' );
			cl.find( 'div' ).each( function( i ) {
				var row = $(this);
				setTimeout( function() {
					row.toggleClass('complete');
					if ( cl.find( 'div' ).length == ( i + 1 ) ) {
						cl.parents( 'form' ).find( 'button' ).removeAttr( 'disabled' );
					}
				}, 1000 * i );
			} );
		}
	} ).trigger( 'memberhero-init-progress' );

	// Document triggers.
	$( document.body )

		// Listen to checkbox change.
		.on( 'change', '.memberhero-setup-content input[type=checkbox]', function() {
			if ( $( this ).is( ':checked' ) ) {
				$( this ).parents( 'fieldset' ).find( '.memberhero-toggle' ).toggles( true );
			} else {
				$( this ).parents( 'fieldset' ).find( '.memberhero-toggle' ).toggles( false );
			}
		} );

} );