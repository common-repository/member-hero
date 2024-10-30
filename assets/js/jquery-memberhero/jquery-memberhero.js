// Here we can define global variables.
var memberhero_username 			= '';
var memberhero_image				= '';
var memberhero_image_title 		= '';
var memberhero_confirmation_code 	= '';
var memberhero_user_id     		= 0;

( function( $ ) {
	"use strict";
	$.memberhero_text_from_html = function( str ) {
		str = str
            .replace(/&#10;/g, '\n')
            .replace(/&#09;/g, '\t')
            .replace(/<img[^>]*alt="([^"]+)"[^>]*>/ig, '$1')
            .replace(/\n|\r/g, '')
            .replace(/<br[^>]*>/ig, '\n')
            .replace(/(?:<(?:div|p|ol|ul|li|pre|code|object)[^>]*>)+/ig, '<div>')
            .replace(/(?:<\/(?:div|p|ol|ul|li|pre|code|object)>)+/ig, '</div>')
            .replace(/\n<div><\/div>/ig, '\n')
            .replace(/<div><\/div>\n/ig, '\n')
            .replace(/(?:<div>)+<\/div>/ig, '\n')
            .replace(/([^\n])<\/div><div>/ig, '$1\n')
            .replace(/(?:<\/div>)+/ig, '</div>')
            .replace(/([^\n])<\/div>([^\n])/ig, '$1\n$2')
            .replace(/<\/div>/ig, '')
            .replace(/([^\n])<div>/ig, '$1\n')
            .replace(/\n<div>/ig, '\n')
            .replace(/<div>\n/ig, '\n\n')
            .replace(/<(?:[^>]+)?>/g, '')
            .replace(new RegExp('&#8203;', 'g'), '')
            .replace(/&nbsp;/g, ' ')
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/&quot;/g, '"')
            .replace(/&#x27;/g, "'")
            .replace(/&#x60;/g, '`')
            .replace(/&#60;/g, '<')
            .replace(/&#62;/g, '>')
            .replace(/&amp;/g, '&');
		return str;
	};
} ( jQuery ) );

/*
 * Set a global message.
 */
( function( $ ) {
	"use strict";
    $.fn.memberhero_message = function( message ) {
		$( '.memberhero-globalmsg' ).html( message ).stop( true ).css( { top: '-100px' } ).animate( { top: 0 }, 250 ).delay( 2500 ).animate( { top: '-100px' }, 250 );
	};
} ( jQuery ) );

/*
 * Get a query parameter from browser location.
 */
( function( $ ) {
	"use strict";
	$.fn.memberhero_param = function(sParam) {
		var sPageURL = window.location.search.substring(1),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
	};
} ( jQuery ) );

/*
 * Toggle password visibility.
 */
( function( $ ) {
	"use strict";
	$.fn.memberhero_toggle_password = function() {
		if ( this.hasClass( 'is-hidden' ) ) {
			this.find( 'svg use' ).attr( 'xlink:href', memberhero_params.svg + 'eye-off' );
			this.addClass( 'is-visible' ).removeClass( 'is-hidden' ).attr( 'data-tip', this.attr( 'data-hide') ).parents( '.memberhero-field' ).find( 'input[type=password]' ).attr( 'type', 'text' ).focus();
		} else {
			this.find( 'svg use' ).attr( 'xlink:href', memberhero_params.svg + 'eye' );
			this.addClass( 'is-hidden' ).removeClass( 'is-visible' ).attr( 'data-tip', this.attr( 'data-show') ).parents( '.memberhero-field' ).find( 'input[type=text]' ).attr( 'type', 'password' ).focus();
		}
		$( document.body ).trigger( 'memberhero-init-tooltips' );
	};
} ( jQuery ) );

/*
 * Opens a dropdown menus.
 */
( function( $ ) {
	"use strict";
	$.fn.memberhero_open_menu = function( elem, target ) {
		if ( $( document.body ).find( $( '#' + elem ) ).length ) {
			var dropdown = $( '#' + elem );
		} else {
			var dropdown = target.parents( '.memberhero-grid-item' ).find( '.' + elem );
			if ( dropdown.length == 0 ) {
				var dropdown = target.parents( '.memberhero' ).find( '.' + elem );
				if ( dropdown.length == 0 ) {
					var dropdown = target.parents( '.memberhero-modal' ).find( '.' + elem );
				}
			}
		}
		if ( dropdown.is( ':hidden' ) ) {
			$( '.memberhero-dropdown:visible' ).each( function() {
				$( this ).hide();
			} );
			dropdown.show( 0, function() {
				$( '#tiptip_holder' ).css( { opacity: 0 } );
				target.blur();
			} );
		} else {
			dropdown.hide();
		}
	};
} ( jQuery ) );

/*
 * Hide active dropdown menus.
 */
( function( $ ) {
	"use strict";
    $.fn.memberhero_hide_menu = function() {
		var dropdown = $( '.memberhero-dropdown:visible' );
		dropdown.hide();
	};
} ( jQuery ) );

/*
 * After an AJAX request.
 */
( function( $ ) {
	"use strict";
	$.fn.memberhero_after_ajax = function( response ) {
		if ( response.new_modal ) {
			$( document.body ).append( response.html );
			$( response.new_modal ).memberheromodal();
		}
		if ( response.keep_modal === undefined ) {
			$( '.memberhero-modal:visible' ).find( 'input[type=text]' ).val( '' );
			$.memberheromodal.close();
		}
		if ( response.keep_alive === undefined ) {
			$( document ).memberhero_hide_menu();
		}
		if ( response.message ) {
			$( document ).memberhero_message( response.message );
		} else if ( response.error ) {
			$( document ).memberhero_message( response.error );
		}
		if ( response.js_update ) {
			if ( response.js_update.child ) {
				$( response.js_update.parent ).find( response.js_update.child ).replaceWith( response.js_update.html );
			}
		}
		if ( response.js_remove ) {
			$( response.js_remove ).remove();
		}
		if ( response.toggle ) {
			$.each( response.toggle, function( elem, addclass ) {
				$( response.js_update.parent ).find( elem ).removeClass( elem.replace( '.', '' ) ).addClass( addclass );
			} );
		}
	};
} ( jQuery ) );

/*
 * Save a cropped image.
 */
( function( $ ) {
	"use strict";
	$.fn.memberhero_save_crop = function( event, the_crop, w, h, name ) {
		$( event.target ).addClass( 'disabled' );
		the_crop.croppie( 'result', {
			size: {
				width: w,
				height: h
			},
			circle: false,
		} ).then( function ( memberhero_image ) {

			var data = new FormData();
			data.append( 'user_id', $( event.target ).attr( 'data-user_id' ) );
			data.append( 'action', 'memberhero_' + name );
			data.append( 'security', memberhero_params.nonces[ name ] );
			data.append( 'memberhero_image', memberhero_image );

			$.ajax( {
				type: 'post',
				url: memberhero_params.ajaxurl,
				data: data,
				contentType: false,
				processData: false,
				success: function ( response ) {
					$( event.target ).removeClass( 'disabled' );
					$( document ).memberhero_after_ajax( response );
					if ( name == 'save_cover' ) {
						$( document ).memberhero_reset_cover();
					} else if ( name == 'save_avatar' ) {
						$( document ).memberhero_reset_avatar();
					}
				}
			} );

		} );
	};
} ( jQuery ) );

/*
 * Reset cover image.
 */
( function( $ ) {
	"use strict";
	$.fn.memberhero_reset_cover = function() {
		$( '.memberhero-cover-cropper-holder' ).find( '.memberhero-ripple' ).remove();
		$( '.memberhero-cover-cropper-holder' ).removeClass( 'memberhero-offcanvas' ).hide();
		$( '.memberhero-cover-cropper-holder' ).find( '.memberhero-button.main' ).removeClass( 'disabled' );
		$( '.memberhero-cover-cropper-actions' ).appendTo( $( '.memberhero-cover-cropper-holder' ) );
		$( '.memberhero-input-cover' ).parent().trigger( 'reset' );
		$memberhero_cover_crop.croppie( 'destroy' );
	};
} ( jQuery ) );

/*
 * Reset avatar image.
 */
( function( $ ) {
	"use strict";
	$.fn.memberhero_reset_avatar = function() {
		$( '.memberhero-profile-photo-link, .memberhero-profile-photo-edit-overlay' ).removeClass( 'memberhero-offcanvas' );
		$( '.memberhero-profile-photo-edit-overlay' ).find( '.memberhero-ripple' ).remove();
		$( '#memberhero-modal-photo, .blocker' ).removeClass( 'memberhero-modal-offcanvas' );
	};
} ( jQuery ) );

/*
 * Submits a standard or ajax form.
 */
( function( $ ) {
	"use strict";
	$.fn.memberhero_form = function( event ) {
		var elem = this;
		if ( elem.attr( 'data-ajax' ) != 'yes' ) {
			return true;
		}
		event.preventDefault();
		var container = elem.parents( '.memberhero' );
		if ( container.find( '.memberhero-button.main' ).hasClass( 'disabled' ) ) {
			return false;
		} else {
			container.find( '.memberhero-button.main' ).addClass( 'disabled' );
		}
		elem.find( '.memberhero-errors, .memberhero-error, .memberhero-message, .memberhero-info' ).remove();
		$.post( memberhero_params.ajaxurl, elem.serialize() + '&action=memberhero_send_form', function( response ) {
			elem.find( '.memberhero-invalid' ).removeClass( 'memberhero-invalid' );
			elem.find( '.memberhero-row:first' ).before( response.html );
			if ( response.error_fields ) {
				container.find( '.memberhero-button.main' ).removeClass( 'disabled' );
				$.each( response.error_fields, function( key, value ) {
					elem.find( value ).find( 'label, input' ).addClass( 'memberhero-invalid' );
				} );
			} else {
				if ( response.js_replace ) {
					container.html( response.js_replace );
				}
				if ( response.js_redirect ) {
					window.location.href = response.js_redirect;
				} else {
					container.find( '.memberhero-button.main' ).removeClass( 'disabled' );
					if ( response.cleardata ) {
						elem.find( ':input:not([type=hidden],[type=submit])' ).val( '' );
					}
				}
			}
		} ).fail( function( xhr, status, error ) {
			container.find( '.memberhero-button.main' ).removeClass( 'disabled' );
			elem.prepend( '<div class="memberhero-info">' + error + '</div>' );
		} );
	};
} ( jQuery ) );

/*
 * Submits a standard or ajax form.
 */
( function( $ ) {
	"use strict";
	$.fn.memberhero_resize_popup = function( elem, thelink ) {
		var leftpos = thelink.offset().left - ( elem.width() - 40 );
		if ( leftpos < 0 ) {
			elem.css( {
				'position' : 'fixed',
				'top' : '20px',
				'right' : '20px',
				'left' : 'auto',
				'bottom' : 'auto',
			} );
		} else {
			elem.css( {
				'position' : 'absolute',
				'top' : thelink.offset().top + thelink.innerHeight() + 'px',
				'left' : leftpos + 'px',
				'right' : 'auto',
				'bottom' : 'auto'
			} );
		}
	};
} ( jQuery ) );