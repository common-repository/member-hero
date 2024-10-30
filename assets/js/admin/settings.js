(function ( $ ) {
	"use strict";

	// Color picker.
	$( '.colorpick' )

		.iris({
			change: function( event, ui ) {
				$( this ).parent().find( '.colorpickpreview' ).css({ backgroundColor: ui.color.toString() });
			},
			hide: true,
			border: true
		} )

		.on( 'click focus', function( event ) {
			event.stopPropagation();
			$( '.iris-picker' ).hide();
			$( this ).closest( 'td' ).find( '.iris-picker' ).show();
			$( this ).data( 'original-value', $( this ).val() );
		} )

		.on( 'change', function() {
			if ( $( this ).is( '.iris-error' ) ) {
				var original_value = $( this ).data( 'original-value' );

				if ( original_value.match( /^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/ ) ) {
					$( this ).val( $( this ).data( 'original-value' ) ).change();
				} else {
					$( this ).val( '' ).change();
				}
			}
		} );

	$( 'body' ).on( 'click', function() {
		$( '.iris-picker' ).hide();
	});

	// Selectize
	$( document.body ).on( 'memberhero-init-selectize', function() {
		$( '.memberhero-terms-search' ).each( function() {
			var the_term = $( this );
			the_term.selectize( {
				dropdownParent: 'body',
				valueField: 'id',
				labelField: 'title',
				searchField: 'title',
				options: [],
				create: false,
				render: {
					option: function (item, escape) {
						return '<div class="option">' + escape( item.title ) + '</div>';
					}
				},
				load: function( query, callback ) {
					if ( ! query.length ) return callback();
					$.ajax( {
						url: ajaxurl,
						type: 'get',
						dataType: 'json',
						data: {
							keyword: 	query,
							action: 	'memberhero_json_search_terms',
							security: 	memberhero_admin.nonces.json_search_terms,
							type:		the_term.attr( 'data-type' )
						},
						error: function() {
							callback();
						},
						success: function( res ) {
							callback( res );
						}
					} );
				}
			} );
		} );
	} ).trigger( 'memberhero-init-selectize' );

	// Conditional settings.
	$( document.body ).on( 'memberhero-init-conditional-settings', function() {
		$( '.memberhero [data-condition]' ).each( function() {
			$( this ).hide();
			var condition = $( this ).attr( 'data-condition' );
			var condition = condition.split( ':' );
			if ( $( '#' + condition[0] ).val() == condition[1] ) {
				$( this ).show();
			} else {
				$( this ).hide();
			}
		} );
	} ).trigger( 'memberhero-init-conditional-settings' );

	$( document.body ).on( 'change', '.memberhero select', function() {
		$( document.body ).trigger( 'memberhero-init-conditional-settings' );
	} );

} )(jQuery);