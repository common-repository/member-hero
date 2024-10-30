( function ( $ ) {
	"use strict";

	// Form Builder JS.
	var memberhero_builder = {

		// Defaults
		default_state: 	$( '.save_form' ).find( 'span' ).html(),
		save_btn: 		$( '.save_form' ).find( 'span' ),
		btn:			$( '.save_form' ),
		state:			$( '.memberhero-bld-topbar span.description' ),
		hidden_row:		$( '.memberhero-bld-row.hidden' ),
		new_row:		$( '.memberhero-bld-new' ),
		active:			null,
		autosave:		1,
		active_field:	null,
		active_row:     null,

		// Get form data
		getdata: function() {

			// Data objects
			var data = [];

			// Fields
			data['fields'] = [];
			$( '.memberhero-bld-col .memberhero-bld-elem:visible' ).each( function() {
				var thisdata = {};
				var that = $( this );
				$.each( memberhero_admin.metakeys, function( index, key ) {
					var attribute = that.attr( 'data-' + key );
					if ( attribute ) {
						thisdata[ key ] = attribute;
					}
				} );
				data.fields.push( {
					'data'	 : thisdata,
					'row' 	 : $( this ).parents( '.memberhero-bld-row' ).index( '.memberhero-bld-row' ),
					'col'	 : $( this ).parents( '.memberhero-bld-col' ).index() + 1
				} );
			} );

			// Rows data
			data['rows'] = [];
			$( '.memberhero-bld-row:not(.hidden)' ).each( function() {
				data.rows.push( {
					'index' : $( this ).index( '.memberhero-bld-row' ),
					'title' : $( this ).find( '.memberhero-bld-label' ).html(),
				} );
			} );

			// Columns
			data['cols'] = [];
			data.cols.push( { count: 0, layout: 0 } );
			$( '.memberhero-bld-row:not(.hidden)' ).each( function() {
				var size = [];
				$( this ).find( '.memberhero-bld-col' ).each( function() {
					size.push( $( this ).getsize() );
				} );
				data.cols.push( {
					'count'  : $( this ).find( '.memberhero-bld-col' ).length,
					'layout' : size
				} );
			} );

			// Global
			data = $.extend( {
				id: 		this.btn.attr( 'data-id' ),
				action: 	'memberhero_save_form',
				security: 	memberhero_admin.nonces.save_form,
				row_count:	$( '.memberhero-bld-row:not(.hidden)' ).length
			}, data );

			return data;
		},

		// Init
		freshstart: function() {
			if ( $( '.memberhero-bld-row' ).length == 0 ) {
				return true;
			}

			if ( this.autosave ) {
				this.btn.hide();
			}

			if ( $( '.memberhero-bld-row:not(.hidden)' ).length == 0 ) {
				var $new_row = this.hidden_row.clone().insertBefore( this.new_row ).show().removeClass( 'hidden' ).append( '<div class="memberhero-clear"></div>' );
				$new_row.find( '.memberhero-bld-elems' ).empty();
			}
		},

		// Add row
		add_row: function() {
			var $new_row = this.hidden_row.clone().insertBefore( this.new_row ).show().removeClass( 'hidden' ).append( '<div class="memberhero-clear"></div>' );
			$new_row.find( '.memberhero-bld-elems' ).empty();
			this.sortables();
			this.ready_save();
		},

		// Duplicate row
		duplicate_row: function( el ) {
			var $dup_row = el.clone().insertAfter( el );
			this.sortables();
			this.ready_save();
		},

		// Delete row
		delete_row: function( el ) {
			el.remove();
			this.ready_save();
		},

		// Duplicate field
		duplicate_field: function( el ) {
			var $dup_field = el.clone().insertAfter( el );
			this.sortables();
			this.ready_save();
		},

		// Delete element
		delete_field: function( el ) {
			el.remove();
			this.ready_save();
		},

		// Insert element
		insert: function( el ) {

			var placeholder = $( '.memberhero-bld-elem.hidden:first' );
			var $e = placeholder.clone().appendTo( this.active );
			var icon = el.attr( 'data-icon' ) ? el.attr( 'data-icon' ) : '';

			$e.html( $e.html().replace( /{label}/i, el.html() ) );
			$e.html( $e.html().replace( /{key}/i, el.attr( 'data-key' ) ) );

			if ( icon ) {
				$e.find( '.memberhero-bld-icon svg use' ).attr( 'xlink:href', memberhero_admin.svg + icon );
			}

			$.each( el.data(), function ( name, value ) {
				$e.attr( 'data-' + name, value );
			} );

			$e.show().removeClass( 'hidden' );

			this.sortables();
			this.ready_save();
		},

		// Sortables
		sortables: function() {
			// Reorder elements.
			$( '.memberhero-bld-elems' ).sortable({
				connectWith: '.memberhero-bld-elems',
				handle:	'.memberhero-move-field',
				placeholder: 'memberhero-placeholder',
				tolerance: 'pointer',
				appendTo: $( 'body' ),
				start: function( event, ui ) {
					ui.placeholder.height( ( ui.item.height() - 2 ) );
				},
				update: function( event, ui ) {
					memberhero_builder.ready_save();
				}
			});

			// Reorder rows.
			$( '#memberhero-form-builder .inside' ).sortable({
				items: '> .memberhero-bld-row',
				axis: 'y',
				handle:	'.memberhero-move-row',
				placeholder: 'memberhero-placeholder-row',
				tolerance: 'pointer',
				appendTo: $( 'body' ),
				start: function( event, ui ) {
					ui.placeholder.height( ( ui.item.height() - 2 ) );
				},
				update: function( event, ui ) {
					memberhero_builder.ready_save();
				}
			});
		},

		// Toggle
		toggle: function( el ) {
			if ( el.attr( 'data-toggled' ) == 1 ) {
				el.attr( 'data-toggled', 0 ).find( '.memberhero-bld-col, .memberhero-bld-add, .memberhero-bld-bar' ).show();
				el.find( '.memberhero-bld-cols' ).height( 'auto' );
				el.find( '.memberhero-toggle-row svg use' ).attr( 'xlink:href', memberhero_admin.svg + 'chevron-up' );
			} else {
				el.attr( 'data-toggled', 1 ).find( '.memberhero-bld-col, .memberhero-bld-add, .memberhero-bld-bar' ).hide();
				el.find( '.memberhero-bld-cols' ).height( 20 );
				el.find( '.memberhero-toggle-row svg use' ).attr( 'xlink:href', memberhero_admin.svg + 'chevron-down' );
			}
		},

		// Ready to save
		ready_save: function() {
			$.memberheromodal.close();
			if ( this.autosave ) {
				this.save();
			} else {
				this.btn.removeClass( 'disabled' );
			}
		},

		// Saving in progress
		saving: function() {
			this.save_btn.html( memberhero_admin.states.saving_changes ).parent().addClass( 'disabled' );
			this.state.html( '<span class="spinner"></span>' + memberhero_admin.states.saving_changes );
			$( '#publish' ).attr( 'disabled', 'disabled' );
		},

		// Saved
		saved: function() {
			this.state.empty();
			this.save_btn.html( this.default_state );
			$( '#publish' ).removeAttr( 'disabled' );
		},

		// Save form
		save: function() {
			this.saving();
			$.ajax( {
				url: ajaxurl,
				data: this.getdata(),
				dataType: 'json',
				type: 'post',
				context: this,
				success: function() {
					setTimeout( () => { this.saved(); }, 1000 );
				}
			} );
		},

		// Add field
		add_field: function( el ) {
			var html = el.html(),
				data = $( '#memberhero-add-field :input' ).serialize();

			el.addClass( 'disabled' ).html( memberhero_admin.states.saving_changes );

			$.ajax( {
				url: ajaxurl,
				data: data + '&action=memberhero_add_field&security=' + memberhero_admin.nonces.add_field,
				dataType: 'json',
				type: 'post',
				context: this,
				success: function( res ) {
					el.removeClass( 'disabled' ).html( html );
					if ( res.errors ) {
						$( 'span.error' ).remove();
						$( ':input' ).removeClass( 'error' );
						$.each( res.errors, function(key, error) {
							$( '#' + key ).addClass( 'error' ).parents( '.form-field' ).append( '<span class="error">' + error + '</span>' );
							$( 'span.error' ).animate( { 'opacity' : 1, 'top' : '0' }, 250 );
						} );
					} else {
						this.insert( $( res.data ) );
					}
				}
			} );
		},

		// Clears modal settings.
		clear_modal_settings: function( el ) {
			var m = $( '#memberhero-add-field' );

			// Change heading.
			m.find( 'h3' ).html( memberhero_admin.modal.creating );

			// Unlock key.
			m.find( '#key' ).removeAttr( 'disabled' );

			// Change buttons.
			m.find( '.button-primary' ).addClass( 'add_field' ).removeClass( 'save_field' ).html( memberhero_admin.modal.create_button );
			m.find( '.button-secondary' ).attr( 'rel', 'modal:open' );

			// Clear fields as much as we can.
			m.find( 'input[type=text], textarea' ).val( '' );
			m.find( 'select' ).each( function() {
				$( this )[0].selectize.setValue( '', true );
			} );

			// Set the field type.
			m.find( '#type' )[0].selectize.setValue( el.attr( 'data-type' ), true );	
		},

		// Edit row.
		edit_row: function( el ) {
			this.active_row = el;
			var m = $( '#memberhero-edit-row' );
			m.find( '#row_title' ).val( el.find( '.memberhero-bld-label:first' ).html() );
		},

		// Save row.
		save_row: function( el ) {
			var m = $( '#memberhero-edit-row' );
			var title = m.find( '#row_title' ).val();
			if ( title ) {
				el.find( '.memberhero-bld-label:first' ).html( title );
			} else {
				el.find( '.memberhero-bld-label:first' ).empty();
			}
			this.sortables();
			this.ready_save();
		},

		// Edit field.
		edit_field: function( el ) {
			this.active_field = el;
			this.add_modal_settings( el );
		},

		// Add settings to field modal.
		add_modal_settings: function( el ) {
			var m = $( '#memberhero-add-field' );

			// Change heading.
			m.find( 'h3' ).html( memberhero_admin.modal.editing.replace( '{field}', el.attr( 'data-label' ) ) );

			// Lock key.
			m.find( '#key' ).attr( 'disabled', 'disabled' );

			// Change buttons.
			m.find( '.button-primary' ).removeClass( 'add_field' ).addClass( 'save_field' ).html( memberhero_admin.modal.save_button );
			m.find( '.button-secondary' ).attr( 'rel', 'modal:close' );

			// loop through each data attribute and set the option in field settings modal.
			$.each( memberhero_admin.metakeys, function( index, key ) {
				var input = m.find( '#' + key );
				var value = el.attr( 'data-' + key );
				if ( input.length ) {
					var type = input.parents( '.form-field' ).attr( 'data-type' );
					if ( type == 'select' ) {
						if ( input.prop('multiple') ) {
							if ( value ) {
								var array = value.split(',');
								input[0].selectize.setValue( array, true );
							}
						} else {
							input[0].selectize.setValue( value, true );
						}
					} else if ( type == 'switch' ) {
						if ( value == 1 ) {
							input.parents( '.form-field' ).find( '.memberhero-toggle' ).toggles( true );	
						} else {
							input.parents( '.form-field' ).find( '.memberhero-toggle' ).toggles( false );	
						}
					} else if ( type == 'textarea' ) {
						if ( value ) {
							input.val( value.replace( /\+/g, '\n' ) );
						} else {
							input.val( '' );
						}
					} else {
						input.val( value );
					}
				}
			} );
		},

		// Save field.
		save_field: function( el ) {
			this.save_modal_settings( el );
			this.sortables();
			this.ready_save();
		},

		// Save settings from field modal.
		save_modal_settings: function( el ) {
			var m = $( '#memberhero-add-field' );
			$.each( memberhero_admin.metakeys, function( index, key ) {
				var input = m.find( '#' + key );
				var value = '';
				if ( input.length ) {
					var type = input.parents( '.form-field' ).attr( 'data-type' );
					if ( type == 'switch' ) {
						value = ( input.parents( '.form-field' ).find( ':checkbox' ).is( ':checked' ) ) ? 1 : '';
					} else {
						value = input.val();
					}
					if ( key == 'label' ) {
						el.find( '.memberhero-bld-label' ).html( value );
					}
					if ( key == 'icon' ) {
						if ( value ) {
							el.find( '.memberhero-bld-icon svg use' ).attr( 'xlink:href', memberhero_admin.svg + value );
						} else {
							el.find( '.memberhero-bld-icon svg use' ).attr( 'xlink:href', memberhero_admin.svg + '' );
						}
					}
					if ( type == 'textarea' ) {
						value = value.replace( /\n/g, '+' );
					}
					el.attr( 'data-' + key, value );
				}
			} );
		}

	}

	// Init builder
	$( document.body ).on( 'memberhero-init-builder', function() {
		memberhero_builder.freshstart();
		memberhero_builder.sortables();
	} ).trigger( 'memberhero-init-builder' );

	// Document triggers.
	$( document.body )

		// Delete field.
		.on( 'click', '.memberhero-delete-field', function() {
			memberhero_builder.delete_field( $( this ).parents( '.memberhero-bld-elem' ) );
		} )

		// Delete row.
		.on( 'click', '.memberhero-delete-row', function() {
			memberhero_builder.delete_row( $( this ).parents( '.memberhero-bld-row' ) );
		} )

		// Add row.
		.on( 'click', '.memberhero-add-row', function() {
			memberhero_builder.add_row();
		} )

		// Insert field.
		.on( 'click', '.insert_field', function() {
			memberhero_builder.insert( $( this ) );
		} )

		// Add element.
		.on( 'click', '.memberhero-add-element', function() {
			memberhero_builder.active = $( this ).parents( '.memberhero-bld-col' ).find( '.memberhero-bld-elems' );
		} )

		// Toggle row.
		.on( 'click', '.memberhero-toggle-row', function() {
			memberhero_builder.toggle( $( this ).parents( '.memberhero-bld-row' ) );
		} )

		// Duplicate row.
		.on( 'click', '.memberhero-duplicate-row', function() {
			memberhero_builder.duplicate_row( $( this ).parents( '.memberhero-bld-row' ) );
		} )

		// Duplicate field.
		.on( 'click', '.memberhero-duplicate-field', function() {
			memberhero_builder.duplicate_field( $( this ).parents( '.memberhero-bld-elem' ) );
		} )

		// Save form.
		.on( 'click', '.save_form:not(.disabled)', function() {
			memberhero_builder.save();
		} )

		// Ajax save field.
		.on( 'click', '.add_field:not(.disabled)', function() {
			memberhero_builder.add_field( $( this ) );
		} )

		// New field.
		.on( 'click', '.new_field', function() {
			memberhero_builder.clear_modal_settings( $( this ) );
		} )

		// Edit field.
		.on( 'click', '.memberhero-edit-field', function() {
			memberhero_builder.edit_field( $( this ).parents( '.memberhero-bld-elem' ) );
		} )

		// Save field.
		.on( 'click', '.save_field', function() {
			memberhero_builder.save_field( memberhero_builder.active_field );
		} )

		// Edit row.
		.on( 'click', '.memberhero-edit-row', function() {
			memberhero_builder.edit_row( $( this ).parents( '.memberhero-bld-row' ) );
		} )

		// Save row.
		.on( 'click', '.save_row', function() {
			memberhero_builder.save_row( memberhero_builder.active_row );
		} )

		// Before field modal is open.
		.on( $.memberheromodal.BEFORE_OPEN, '#memberhero-add-field', function(e, modal) {
			$( '.memberhero-modal span.error' ).remove();
			$( '.memberhero-modal :input' ).removeClass( 'error' );
			$( '.memberhero-modal div.panel-wrap' ).each( function() {
				$( this ).find( 'ul.memberhero-tabs li' ).eq( 0 ).find( 'a' ).click();
			});
		} )

		// After field modal is open.
		.on( $.memberheromodal.OPEN, '#memberhero-add-field', function(e, modal) {
			$( document.body ).trigger( 'memberhero-init-fields' );
		} );

} )(jQuery);