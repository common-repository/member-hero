(function($) {
	"use strict";

	// Tooltips
	$( document.body ).on( 'memberhero-init-tooltips', function() {
		$( '.memberhero .tips, .memberhero-help-tip' ).each( function() {
			var tip = $( this );
			tip.tipTip( {
				'attribute': 'data-tip',
				'defaultPosition': 'top',
				'edgeOffset': 3,
				'enter' : function() {
					$( '#tiptip_holder' ).css( { opacity: 1 } );
				},
				'delay' : 200
			} );
		} );

		$( '.memberhero-navbar-tip' ).each( function() {
			var tip = $( this );
			tip.tipTip( {
				'attribute': 'data-tip',
				'defaultPosition': 'bottom',
				'edgeOffset': 10,
				'enter' : function() {
					$( '#tiptip_holder' ).css( { opacity: 1 } );
				},
				'delay' : 200
			} );
		} );

		$( '.memberhero-tips' ).tipTip( {
			'attribute': 'data-tip',
			'edgeOffset': 5,
			'defaultPosition': 'top',
			'enter' : function() {
				$( '#tiptip_holder' ).css( { opacity: 1 } );
			},
			'delay' : 200
		} );
	} ).trigger( 'memberhero-init-tooltips' );

	// Selectize
	$( document.body ).on( 'memberhero-init-selects', function() {
		$( '.memberhero-select' ).selectize( {
			dropdownParent: 'body',
			allowEmptyOption: true,
		} );
	} ).trigger( 'memberhero-init-selects' );

	// Emojis
	$( document.body ).on( 'memberhero-init-emojis', function() {
		emojione.imagePathPNG = 'https://s.w.org/images/core/emoji/12.0.0-1/png/';
		emojione.imagePathSVG = 'https://s.w.org/images/core/emoji/12.0.0-1/svg/';
		$( '.memberhero-emoji' ).emojioneArea( {
			searchPlaceholder: memberhero_params.emoji.search,
			buttonTitle: memberhero_params.emoji.buttontitle,
			autocomplete: false,
			useInternalCDN: false
		} );
	} ).trigger( 'memberhero-init-emojis' );

	// Toggles
	$( document.body ).on( 'memberhero-init-toggles', function() {
		$( '.memberhero-toggle' ).each( function() {
			var cb = $( this ).parents( 'fieldset' ).find( 'input:checkbox' ).attr( 'id' );
			$( this ).toggles( {
				'width': 60,
				'height': 20,
				'text': {
					on: memberhero_params.yes,
					off: memberhero_params.no
				},
				'checkbox' : $( '#' + cb )
			} );
		} );
	} ).trigger( 'memberhero-init-toggles' );

	// Star rating.
	$( document.body ).on( 'memberhero-init-rating', function() {
		$( '.memberhero-star' ).each( function() {
			var memberhero_rate = $( this );
			var args = {
				starType: 'i',
				number: 5,
				space: false,
				scoreName: memberhero_rate.attr( 'data-key' ),
				readOnly: memberhero_rate.attr( 'data-readonly' ),
				mouseover: function(score, evt) {
					var hint = memberhero_rate.parents( 'fieldset' ).find( '.memberhero-star-hint' );
					if ( hint.length == 0 ) {
						return;
					}
					hint.html( $( evt.target ).attr( 'data-hint' ) );
				},
				mouseout: function(score, evt) {
					var hint = memberhero_rate.parents( 'fieldset' ).find( '.memberhero-star-hint' );
					if ( hint.length == 0 ) {
						return;
					}
					if ( ! score ) {
						hint.html( '' );
					} else {
						hint.html( memberhero_rate.find( 'i[data-alt=' + score + ']' ).attr( 'data-hint' ) );
					}
				}	
			}

			if ( memberhero_rate.attr( 'data-ratings' ) ) {
				args.hints = memberhero_rate.attr( 'data-ratings' ).split( ',' );
			}

			memberhero_rate.raty( args );
		} );
	} ).trigger( 'memberhero-init-rating' );

	// Set focus on password field when user is editing password.
	$( document.body ).on( 'memberhero-init-focus', function() {
		$( '.memberhero-edit-password' ).find( ':input[value=""]:enabled:visible:first' ).focus();
	} ).trigger( 'memberhero-init-focus' );

	// Sets initial global message.
	$( document.body ).on( 'memberhero-init-message', function() {
		var $saved;
		$( document.body ).append( '<div class="memberhero-globalmsg"></div>' );
		$saved = $( document ).memberhero_param( 'saved' );
		if ( $saved && $( 'div[role=alert]' ).length == 0 ) {
			$( document ).memberhero_message( memberhero_params.saved[ $saved ] );
		}
	} ).trigger( 'memberhero-init-message' );

	// Get pasted code from clipboard.
	$( document.body ).on( 'paste', '.memberhero-code-input input', function( event ) {
		var data = event.originalEvent.clipboardData.getData( 'text' );
		var split = data.split( '-' );
		var number = split[0] + split[1];
		var number = number.replace( /[^0-9]/g, '' );
		if ( number.length == 6 && /^\+?(0|[1-9]\d*)$/.test( parseInt( number ) ) ) {
			$( '.memberhero-code input:eq(0)' ).val( number.charAt( 0 ) );
			$( '.memberhero-code input:eq(1)' ).val( number.charAt( 1 ) );
			$( '.memberhero-code input:eq(2)' ).val( number.charAt( 2 ) );
			$( '.memberhero-code input:eq(3)' ).val( number.charAt( 3 ) );
			$( '.memberhero-code input:eq(4)' ).val( number.charAt( 4 ) );
			$( '.memberhero-code input:eq(5)' ).val( number.charAt( 5 ) );
			memberhero_check_confirmation_code();
		}
	} );

	// A function to detect if confirmation code was filled.
	function memberhero_validate_confirmation_code() {
		var isValid = true;
		$( '.memberhero-code-input input' ).each( function() {
			if ( $( this ).val() === '' )
				isValid = false;
		} );
		return isValid;
	}

	// Get the confirmation code input.
	function memberhero_get_confirmation_code() {
		var code = '';
		$( '.memberhero-code input' ).each( function () {
			code = code + $( this ).val();
		} );
		if ( code == memberhero_confirmation_code ) {
			return false;
		} else {
			memberhero_confirmation_code = code;
		}
		return code;
	}

	// If confirmation code is filled, submit the ajax request to check it.
	$( '.memberhero-code-input input' ).on( 'keyup, blur', function() {
		if ( memberhero_validate_confirmation_code() ) {
			memberhero_check_confirmation_code();
		}
	} );

	// Check the confirmation code with AJAX.
	function memberhero_check_confirmation_code() {
		if ( memberhero_get_confirmation_code() == false ) {
			return false;
		}
		var container = $( '.memberhero-code' ),
			cb = $( '.memberhero-code-callback' ),
			data = {
				user_id: container.attr( 'data-user_id' ),
				confirmation_code: memberhero_confirmation_code,
				action: 'memberhero_check_confirmation_code',
				security: memberhero_params.nonces.confirmation_code
			};

		cb.find( '.memberhero-info-area' ).hide();
		cb.find( '.memberhero-info' ).css( { 'display' : 'inline-block' } );
		cb.find( '.memberhero-error' ).hide();

		$.post( memberhero_params.ajaxurl, data, function( response ) {
			if ( response.error ) {
				cb.find( '.memberhero-info' ).hide();
				cb.find( '.memberhero-error' ).css( { 'display' : 'inline-block' } );
			} else {
				cb.find( 'span' ).hide();
				window.location.href = response.redirect;
			}
		} );
	}

	// Clicks in the body to hide dropdown.
	$( document.body ).on( 'click', function( event ) {
		var visible_dropdown = $( '.memberhero-dropdown:visible' ),
			hide_dropdown = true;

		if ( $( event.target ).hasClass( 'blocker' ) || $( '.blocker' ).has( event.target ).length > 0 ) {
			if ( visible_dropdown.parents( '.memberhero-modal' ).length == 0 ) {
				hide_dropdown = false;
			}
		}

		if ( hide_dropdown ) {
			$( document ).memberhero_hide_menu();
		}

		// Hide popups.
		if ( $( '.memberhero-popup' ).is( ':visible' ) ) {
			if ( $( event.target ).parents( '.memberhero-popup' ).length == 0 ) {
				$( '.memberhero-popup' ).hide();
			}
		}
	} );

	// AJAX search users.
	function memberhero_ajax_search_user( theinput ) {
		if ( theinput.val().length > 2 ) {
			$.ajax( {
				url: memberhero_params.ajaxurl,
				data: { keyword: theinput.val(), security: memberhero_params.nonces.search_user, action: 'memberhero_search_user' },
				type: 'post',
				success: function( response ) {
					theinput.parents( '.memberhero-modal' ).find( '.memberhero-search-user-ajax' ).html( response );
				}
			} );
		}
	}

	// Body events
	$( document.body )

		// Search for people.
		.on( 'keyup', '.memberhero-search-user', function( event ) {
			memberhero_ajax_search_user( $( this ) );
		} )

		// No href, assume ajax action and do not jump.
		.on( 'click', '.memberhero a[href=#], .memberhero-modal a[href=#]', function( event ) {
			event.preventDefault();
		} )

		// Make the icon trigger input field focus.
		.on( 'click', '.memberhero-icon', function( event ) {
			$( this ).parents( 'fieldset' ).find( 'label' ).trigger( 'click' );
		} )

		// Opens / Init a dropdown menu.
		.on( 'click', '.memberhero-dropdown-init', function( event ) {
			event.stopPropagation();
			$( document ).memberhero_open_menu( $( this ).attr( 'rel' ), $( this ) );
		} )

		// To stop events inside the drop down menu.
		.on( 'click', '.memberhero-dropdown', function( event ) {
			event.stopPropagation();
		} )

		// This hides any active drop down menu.
		.on( 'click', 'a[rel=memberhero_dropdown_hide]', function() {
			$( document ).memberhero_hide_menu();
		} )

		// Listens to checkbox changes.
		.on( 'change', '.memberhero-field input[type=checkbox]', function() {
			if ( $( this ).is( ':checked' ) ) {
				$( this ).parents( 'fieldset' ).find( '.memberhero-toggle' ).toggles( true );
			} else {
				$( this ).parents( 'fieldset' ).find( '.memberhero-toggle' ).toggles( false );
			}
		} )

		// Auto-type username in Account page.
		.on( 'keyup', '.user_login_field input[type=text]', function() {
			var field = $( this ).parents( 'fieldset' );
			if ( field.find( '.memberhero-ajax' ).length ) {
				field.find( '.memberhero-ajax' ).html( $( this ).val() );
			}
		} )

		// Toggle password visibility.
		.on( 'click', '.memberhero-pw-visible', function( event ) {
			$( this ).memberhero_toggle_password();
		} )

		// Send form using anchor that act as submit.
		.on( 'click', 'a.memberhero-button.main:not(.disabled,.memberhero-crop-cover,.memberhero-follow)', function( event ) {
			if ( ! $( this ).hasClass( 'memberhero-modal-open' ) ) {
				$( this ).parents( '.memberhero' ).find( 'form' ).submit();
			}
		} )

		// Form submit handler.
		.on( 'submit', '.memberhero form', function( event ) {
			$( this ).memberhero_form( event );
		} )

		// Fired when modal is opened.
		.on( $.memberheromodal.OPEN, '.memberhero-modal', function( event, modal ) {

			// Add username to modal where possible.
			if ( memberhero_username ) {
				$( event.target ).find( 'b' ).html( memberhero_username );
				$( event.target ).find( 'a.memberhero-button[data-user_id]' ).attr( 'data-user_id', memberhero_user_id );
				$( document ).memberhero_hide_menu();
			}
			if ( $( event.target ).find( 'input[type=text]:first:visible' ).length ) {
				$( event.target ).find( 'input[type=text]:first:visible' ).focus();
			} else if ( $( event.target ).find( 'a.memberhero-button:first:visible' ).length ) {
				$( event.target ).find( 'a.memberhero-button:first:visible' ).focus();
			}
		} )

		// Fired when modal is about to close.
		.on( $.memberheromodal.BEFORE_CLOSE, '.memberhero-modal', function( event, modal ) {
			$( event.target ).find( 'a.memberhero-button' ).removeClass( 'disabled' );
		} )

		// Open a modal.
		.on( 'click', '.memberhero-modal-open', function() {
			var modal = '#' + $( this ).attr( 'rel' );

			if ( $( this ).attr( 'data-user' ) ) {
				memberhero_username = $( this ).attr( 'data-user' );
				memberhero_user_id = $( this ).attr( 'data-user_id' );
			} else if ( $( this ).attr( 'data-user_id' ) ) {
				memberhero_user_id = $( this ).attr( 'data-user_id' );
			}

			if ( $( this ).parent().hasClass( 'memberhero-image' ) ) {
				memberhero_image = $( this ).find( 'img' );
				if ( $( this ).parents( 'fieldset' ).find( 'label' ).length ) {
					memberhero_image_title = $( this ).parents( 'fieldset' ).find( 'label' ).html();
				} else {
					memberhero_image_title = $( this ).parents( '.memberhero' ).find( '.memberhero-profile-name a' ).html();
				}
			}

			$( modal ).memberheromodal();
		} )

		// Fire AJAX action.
		.on( 'click', '.memberhero-ajax-action:not(.memberhero-disabled)', function( event ) {
			event.stopPropagation();
			var $this = $( this ),
				ajax = $this.attr( 'data-action' ),
				data = {
					user_id: $this.attr( 'data-user_id' ) ? $this.attr( 'data-user_id' ) : 0,
					action: 'memberhero_' + ajax,
					security: memberhero_params.nonces[ ajax ]
				};

			if ( $this.hasClass( 'memberhero-hide-controls' ) ) {
				$this.parents( '.memberhero-controls' ).remove();
			}

			if ( $this.attr( 'data-remove' ) === 'true' ) {
				$this.fadeOut();
			}

			// Modal trigger.
			if ( $this.attr( 'data-onmodal' ) === 'true' ) {
				var modal = $this.parents( '.memberhero-modal' );
				modal.find( '.memberhero-error' ).remove();
				modal.find( 'input[type=text]' ).each( function( t, v ) {
					data[ $( this ).attr( 'name' ) ] = $( this ).val();
				} );
				$this.prepend( '<span class="memberhero-ripple"></span>' ).addClass( 'memberhero-disabled' );
				modal.find( '.modal-body, .modal-footer' ).addClass( 'memberhero-nointeract' );
			}

			$.post( memberhero_params.ajaxurl, data, function( response ) {
				// ajax events on modal.
				if ( response._error ) {
					$this.parents( '.memberhero-modal' ).find( '.modal-header' ).after( '<div class="memberhero-error" role="alert">' + response._error + '</div>' );
				}
				// when acting on modal. remove pre-interactions.
				if ( $this.attr( 'data-onmodal' ) === 'true' ) {
					$this.removeClass( 'memberhero-disabled' ).find( '.memberhero-ripple' ).remove();
					$this.parents( '.memberhero-modal' ).find( '.modal-body, .modal-footer' ).removeClass( 'memberhero-nointeract' );
				}
				// standard ajax function.
				$( document ).memberhero_after_ajax( response );
				// reload ready.
				if ( $this.attr( 'data-reload' ) === 'true' ) {
					location.reload();
				}
			} );
		} )

		// Open item permalinks.
		.on( 'click', '.memberhero-item[data-permalink!=""]', function( event ) {
			var $perma = $( this ).attr( 'data-permalink' );
			if ( $perma ) {
				window.location.href = $perma;
			}
		} )

		// Clicking inside sublink to trigger main anchor link.
		.on( 'click', '.memberhero-item-sublink', function( event ) {
			event.stopPropagation();
			var parentlink = $( this ).parents( '.memberhero-item' ).find( 'a.memberhero-item-head-anchor' );
			window.location.href = parentlink.attr( 'href' );
		} )

		// Clicking in item text links.
		.on( 'click', '.memberhero-item-text a', function( event ) {
			event.stopPropagation();
		} )

		// Trigger a popup view
		.on( 'click', '.memberhero-menu-trigger', function( e ) {
			e.preventDefault();
			var $this = $( this ),
				notify = $( document.body ).find( '.memberhero-popup' );

			if ( notify.length == 0 ) {
				$( document.body ).append( '<div class="memberhero-popup"></div>' );
				var popup = $( document.body ).find( '.memberhero-popup' );
				var data = {
					user_id:	$this.find( '.memberhero-menu-icon' ).attr( 'data-user_id' ),
					action:  	'memberhero_' + $this.find( '.memberhero-menu-icon' ).attr( 'data-action' ),
					security: 	memberhero_params.nonces[ $this.find( '.memberhero-menu-icon' ).attr( 'data-action' ) ],
				};

				$( document ).memberhero_resize_popup( popup, $this );

				popup.attr( 'data-rel', $this.find( '.memberhero-menu-icon' ).attr( 'data-rel' ) );
				popup.html( '<div class="memberhero-ripple"></div>' );

				$.post( memberhero_params.ajaxurl, data, function( response ) {
					popup.addClass( 'not-empty' ).html( response );
				} );

			} else {
				if ( notify.is( ':visible' ) ) {
					notify.hide();
				} else {
					notify.show();
					$( document ).memberhero_resize_popup( notify, $this );
				}
			}

			return false;
		} )

		// Loading more lists/items.
		.on( 'click', '.memberhero-ajax-load', function( event ) {
			event.preventDefault();
			var $this 	= $( this ),
				$type 	= $this.attr( 'data-type' ),
				$number = $this.attr( 'data-number' ),
				$offset = $this.attr( 'data-offset' ),
				$total 	= $this.attr( 'data-total' ),
				$owner 	= $this.attr( 'data-owner' ),
				$nonce  = $this.attr( 'data-nonce' );

			var data = { owner: $owner, type: $type, number: $number, offset: $offset, total: $total, owner: $owner, security: memberhero_params.nonces[ 'load_' + $nonce ], action: 'memberhero_load_' + $nonce };

			$this.addClass( 'memberhero-nointeract' ).html( '<div class="memberhero-ripple"></div>' );

			$.post( memberhero_params.ajaxurl, data, function( response ) {
				$this.parents( '.memberhero-section-footer' ).replaceWith( response );
			} );

			return false;
		} )

		// Add image to the modal.
		.on( $.memberheromodal.BEFORE_OPEN, '#memberhero-modal-view-image', function( event, modal ) {
			var original_width = $( memberhero_image ).get( 0 ).naturalWidth;
			$( event.target ).css( { 'max-width' : original_width } );
			$( event.target ).find( '.modal-background' ).css( {
				'background-image' : 'url( ' + memberhero_image.attr( 'src' ) + ')',
				'max-width' : original_width,
			} ).html( memberhero_image.clone() );

			$( event.target ).find( 'h3' ).html( memberhero_image_title );
		} )
		
		// Toggle div content
		.on( 'click', '.memberhero-togglediv', function( event ) {
			event.preventDefault();
			$( $( this ).attr( 'href' ) ).show();
			$( '#' + $( this ).attr( 'data-div' ) ).hide();
		} )

		// Code auto tab.
		.on( 'keyup', '.memberhero-modal .memberhero-code input[type=text]', function( event ) {
			if ( this.value.length == this.maxLength ) {
				var name = $( this ).attr( 'name' ).replace( /\D/g, '' );
				var next = 'ga' + ( parseInt( name ) + 1 );
				$( this ).parents( '.memberhero-code' ).find( 'input[name=' + next + ']' ).focus();
			}
		} );

} )(jQuery);

jQuery( window ).resize( function() {

	jQuery( '.memberhero-popup:visible' ).each( function() {
		$this = $( '.memberhero-menu-trigger .memberhero-menu-icon[data-rel="' + jQuery( this ).attr( 'data-rel' ) + '"]' ).parents( '.memberhero-menu-trigger' );
		jQuery( document ).memberhero_resize_popup( jQuery( this ), $this );
	} );

} );