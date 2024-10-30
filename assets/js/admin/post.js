( function ( $ ) {
	"use strict";

	// Function to control toggle.
	function memberhero_condition_toggle( elem ) {
		var id = elem.attr( 'id' );
		if ( elem.is( ':checked' ) ) {
			$( 'tr.memberhero-conditional[data-rule="' + id + ':checked"]' ).removeClass( 'memberhero-greyed' );
		} else {
			$( 'tr.memberhero-conditional[data-rule="' + id + ':checked"]' ).addClass( 'memberhero-greyed' );
		}
	}

	// Function toggle access options.
	function memberhero_access_toggle() {
		var val = $( '#memberhero-access' ).find( 'input[type=radio]:checked' ).val();
		if ( val == 'everyone' ) {
			$( '.memberhero_hide_if_everyone' ).addClass( 'memberhero-greyed' );
		} else {
			$( '.memberhero_hide_if_everyone' ).removeClass( 'memberhero-greyed' );
		}
		if ( val == 'members' ) {
			$( '.memberhero_show_if_logged' ).removeClass( 'memberhero-greyed' );
		} else {
			$( '.memberhero_show_if_logged' ).addClass( 'memberhero-greyed' );
		}
		if ( $( '#_memberhero_redirect' ).val() == 'custom' ) {
			$( '.memberhero_show_if_custom' ).removeClass( 'memberhero-greyed' );
		} else {
			$( '.memberhero_show_if_custom' ).addClass( 'memberhero-greyed' );
		}
	}

	// Init conditional settings.
	$( document.body ).on( 'memberhero-init-conditional-settings', function() {
		memberhero_access_toggle();
	} ).trigger( 'memberhero-init-conditional-settings' );

	// When settings are toggled.
	$( document.body ).on( 'change', '#memberhero-access input[type=radio], #_memberhero_redirect', function( event ) {
		memberhero_access_toggle();
	} );

	// Init conditional metabox options.
	$( document.body ).on( 'memberhero-init-conditional-metabox', function() {
		$( document.body ).find( 'input.memberhero-conditional' ).each( function() {
			memberhero_condition_toggle( $( this ) );
		} );
	} ).trigger( 'memberhero-init-conditional-metabox' );

	// When changes are made.
	$( document.body ).on( 'change', 'input.memberhero-conditional', function( event ) {
		memberhero_condition_toggle( $( this ) );
	} );

	// Dynamically load select dropdown items by change in another select.
	$( document.body ).on( 'change', 'select[data-ajaxchange]', function( event ) {
		var action = $( this ).attr( 'data-action' ),
			sector = $( this ).attr( 'data-ajaxchange' ),
			param1 = $( this ).val();

		$( '#' + sector ).parents( 'tr' ).addClass( 'memberhero-greyed' );
		$.post( memberhero_post.ajax_url, { action: action, param1: param1, security: memberhero_post.ajaxnonce }, function( response ) {
			$( '#' + sector ).find('option').not(':first').remove();
			if ( response ) {
				$( '#' + sector ).append( response );
			}
			$( '#' + sector ).parents( 'tr' ).removeClass( 'memberhero-greyed' );
		} );

	} );

} ) ( jQuery );