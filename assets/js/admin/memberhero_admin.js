( function ( $ ) {
	"use strict";

	// Date picker
	var memberhero_datepicker = $( '.memberhero_datepicker' );
	if ( memberhero_datepicker.length > 0 ) {
		var dateFormat = 'mm/dd/yy';
		memberhero_datepicker.datepicker( {
			dateFormat: dateFormat
		} );
	}

	// Tabbed Panels
	$( document.body ).on( 'memberhero-init-tabbed-panels', function() {
		$( 'ul.memberhero-tabs' ).show();
		$( document.body ).on( 'click', 'ul.memberhero-tabs a', function( event ) {
			event.preventDefault();
			var panel_wrap = $( this ).closest( 'div.panel-wrap' );
			$( 'ul.memberhero-tabs li', panel_wrap ).removeClass( 'active' );
			$( this ).parent().addClass( 'active' );
			$( 'div.panel', panel_wrap ).hide();
			$( $( this ).attr( 'href' ) ).show();
			$( document.body ).trigger( 'memberhero-init-fields' );
		} );
		$( 'div.panel-wrap' ).each( function() {
			$( this ).find( 'ul.memberhero-tabs li' ).eq( 0 ).find( 'a' ).click();
		} );
	} ).trigger( 'memberhero-init-tabbed-panels' );

	// Conditional fields
	$( document.body ).on( 'memberhero-init-fields', function() {
		$( '.memberhero_options_panel:visible [class*=show_if_]' ).conditional();
	} ).trigger( 'memberhero-init-fields' );

	// Toggles
	$( document.body ).on( 'memberhero-init-toggles', function() {
		$( '.memberhero-toggle' ).each( function() {
			var cb = $( this ).parents( 'div.form-field' ).find( 'input[type=checkbox]' );
			$( this ).toggles( {
				'width': 60,
				'height': 20,
				'text': {
					on: memberhero_admin.yes,
					off: memberhero_admin.no
				},
				'checkbox': cb
			} ).on( 'toggle', function(e, active) {
				$( document.body ).trigger( 'memberhero-init-fields' );
			} );
		} )
	} ).trigger( 'memberhero-init-toggles' );

	// Tooltips
	$( document.body ).on( 'memberhero-init-tooltips', function() {
		$( '.memberhero-help-tip, .tips' ).tipTip( {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		} );

		$( '.memberhero-top-tips' ).tipTip( {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200,
			'defaultPosition': 'top',
		} );
	} ).trigger( 'memberhero-init-tooltips' );

	// Selectize
	$( document.body ).on( 'memberhero-init-selects', function() {
		$( '.memberhero-select' ).selectize( {
			dropdownParent: 'body',
			allowClear: true,
		} );

		$( '.memberhero-select-multi' ).selectize( {
			dropdownParent: 'body',
			plugins: [ 'remove_button', 'drag_drop' ],
			delimiter: ',',
			persist: false,
			create: function( input ) {
				return {
					value: input,
					text: input
				}
			}
		} );

		$( '.memberhero-user-search' ).selectize( {
			dropdownParent: 'body',
			valueField: 'id',
			labelField:  'name',
			searchField: [ 'name', 'email' ],
			options: [],
			create: false,
			render: {
				option: function (item, escape) {
					return '<div class="option">' + escape( item.name ) + ' (#' + escape( item.id ) + ' &ndash; ' + escape( item.email ) + ')</div>';
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
						action: 	'memberhero_json_search_users',
						security: 	memberhero_admin.nonces.json_search_users
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
	} ).trigger( 'memberhero-init-selects' );

	// Show additional title action if required
	$( document.body ).on( 'memberhero-init-action-button', function() {
		var el = $( '.memberhero-page-title-action' );
		$( '.page-title-action' ).after( el );
		el.show();
	} ).trigger( 'memberhero-init-action-button' );

	// Document triggers.
	$( document.body )

		// See more or less.
		.on( 'click', '.memberhero-togglemore', function( e ) {
			e.preventDefault();
			var el = $( this ),
				contain = el.parents( 'td' );
			if ( contain.find( 'span.hidden' ).length ) {
				el.appendTo( contain ).html( memberhero_admin.states.show_less );
				contain.find( 'span.hidden' ).removeClass( 'hidden' ).addClass( 'js-temp' );
			} else {
				el.html( memberhero_admin.states.show_more );
				contain.find( 'span.js-temp' ).addClass( 'hidden' ).removeClass( 'js-temp' );
			}
		} )

		// No links here.
		.on( 'click', ".memberhero_options_panel a[href=#], .memberhero-bld-topbar a[href=#], .memberhero-bld-row a[href=#], .memberhero-bld-new a[href=#], .memberhero-modal a[href=#], a[class^='duplicate_'].disabled", function(e) {
			e.preventDefault();
		} )

		// Listen to checkbox change.
		.on( 'change', '.options_group input[type=checkbox]', function() {
			if ( $( this ).is( ':checked' ) ) {
				$( this ).parents( '.form-field' ).find( '.memberhero-toggle:not(.disabled)' ).toggles( true );
			} else {
				$( this ).parents( '.form-field' ).find( '.memberhero-toggle:not(.disabled)' ).toggles( false );
			}
		} )

		// Listen to select change.
		.on( 'change', '.options_group select', function() {
			$( document.body ).trigger( 'memberhero-init-fields' );
		} )

		// Create items.
		.on( 'click', "a[href^='#memberhero-create-']", function( e ) {
			e.preventDefault();

			var el    = $( this ),
				type  = el.attr( 'href' ).split( '#memberhero-create-' )[1],
				label,
				nonce;

			if ( type == 'forms' ) {
				label = memberhero_admin.states.create_forms;
				nonce = memberhero_admin.nonces.create_forms;
			} else if ( type == 'fields' ) {
				label = memberhero_admin.states.create_fields;
				nonce = memberhero_admin.nonces.create_fields;
			} else if ( type == 'roles' ) {
				label = memberhero_admin.states.create_roles;
				nonce = memberhero_admin.nonces.create_roles;
			} else if ( type == 'lists' ) {
				label = memberhero_admin.states.create_lists;
				nonce = memberhero_admin.nonces.create_lists;
			}

			el.addClass( 'disabled' ).html( label );

			$.post( ajaxurl, { action: 'memberhero_create_' + type, security: nonce }, function( response ) {
				el.html( memberhero_admin.states.done_redirect );
				if ( response.redirect ) {
					window.location.href = response.redirect;
				} else {
					location.reload();
				}
			} );

		} )

		// Duplicate item.
		.on( 'click', "a[class^='duplicate_']:not(.disabled)", function( e ) {
			e.preventDefault();

			var el 		= $( this ),
				data 	= { action: 'memberhero_' + el.attr( 'class' ), security: el.getparam( '_wpnonce' ), id: el.getparam( 'post' ) };

			el.addClass( 'disabled' ).html( memberhero_admin.states.duplicating );

			$.post( ajaxurl, data, function(response) { location.reload(); } );

		} );

	// Auto-update text.
	$( document.body ).on( 'change', 'div[data-ajaxtext] input', function( event ) {
		var el = $( this ).parents( 'td' ),
			action = $( this ).parent().attr( 'data-ajaxtext' ),
			key	  = $( this ).attr( 'data-key' ),
			value = $( this ).val();

		el.find( '.mh-status-saving' ).show();
		$.post( ajaxurl, { action: action, key: key, value: value, security: memberhero_admin.nonces.ajaxnonce }, function(response) {
			el.find( '.mh-status-saving' ).hide();
			el.find( '.mh-status-saved' ).show().delay(1500).fadeOut(300);
		} );
	} );

} )(jQuery);