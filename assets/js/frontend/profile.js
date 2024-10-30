var $memberhero_crop,
	$memberhero_cover_crop;

(function($) {
	"use strict";

	// File inputs.
	$( '.memberhero-fileinput' ).each( function() {
		var $input	 = $( this ),
			$label	 = $input.next( 'label' ),
			labelVal = $label.html();

		$input.on( 'change', function( event ) {
			var fileName = '';

			if( this.files && this.files.length > 1 ) {
				fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
			} else if ( event.target.value ) {
				fileName = event.target.value.split( '\\' ).pop();
			}

			if ( fileName ) {
				$label.find( 'span' ).html( fileName );
				$label.attr( 'title', fileName );
			} else {
				$label.html( labelVal );
				$label.removeAttr( 'title' );
			}
		} );

		// Firefox bug fix
		$input
		.on( 'focus', function(){ $input.addClass( 'has-focus' ); })
		.on( 'blur', function(){ $input.removeClass( 'has-focus' ); });
	} );

	// Body events
	$( document.body )

		// Init the avatar crop zone.
		.on( $.memberheromodal.BEFORE_OPEN, '#memberhero-modal-photo', function( event, modal ) {
			$memberhero_crop = $( '.memberhero-cropper' ).croppie( {
				viewport: {
					width: 240,
					height: 240,
					type: 'circle'
				},
				boundary: {
					width: 300,
					height: 300
				}
			} );
		} )

		// Reset avatar and crop when modal is closed.
		.on( $.memberheromodal.BEFORE_CLOSE, '#memberhero-modal-photo', function( event, modal ) {
			$( '.memberhero-input-avatar' ).parent().trigger( 'reset' );
			$memberhero_crop.croppie( 'destroy' );
		} )

		// A listener for avatar upload.
		.on( 'change', '.memberhero-input-avatar', function( event ) {

			// Open the crop modal.
			$( '#memberhero-modal-photo' ).memberheromodal();

			// Read file.
			var reader = new FileReader();
			reader.onload = function( event ) {
				$memberhero_crop.croppie( 'bind', {
					url: event.target.result
				} ).then( function() {
					$memberhero_crop.croppie( 'setZoom', '0' );
				} ).catch( function( e ) {
					if ( e.type == 'error' ) {
						$( '#memberhero-modal-error' ).memberheromodal();
					}
				} );
			}
			reader.readAsDataURL( this.files[0] );

		} )

		// A listener for header photo upload.
		.on( 'change', '.memberhero-input-cover', function( event ) {

			$( document ).memberhero_hide_menu();

			$memberhero_cover_crop = $( '.memberhero-cover-cropper' ).croppie( {
				viewport: {
					width: '100%',
					height: '100%',
					type: 'square'
				},
				boundary: {
					width: '100%',
					height: '100%',
				}
			} );

			$( '.memberhero-cover-cropper-holder' ).show();
			$( '.memberhero-cover-cropper-actions' ).appendTo( $( '.memberhero-cover-cropper-holder .cr-slider-wrap' ) ).find( '.memberhero-button.main' ).focus();

			// Read file.
			var reader = new FileReader();
			reader.onload = function( event ) {
				$memberhero_cover_crop.croppie( 'bind', {
					url: event.target.result
				} ).then( function() {
					$memberhero_cover_crop.croppie( 'setZoom', '0' );
				} ).catch( function( e ) {
					if ( e.type == 'error' ) {
						$( document ).memberhero_reset_cover();
						$( '#memberhero-modal-error' ).memberheromodal();
					}
				} );
			}
			reader.readAsDataURL( this.files[0] );

		} )

		// Reset header photo.
		.on( 'click', '.memberhero-cancel-cover', function( event ) {
			$( document ).memberhero_reset_cover();
		} )

		// Saves the profile photo.
		.on( 'click', '.memberhero-crop-avatar:not(.disabled)', function( event ) {
			$( '#memberhero-modal-photo, .blocker' ).addClass( 'memberhero-modal-offcanvas' );
			$( document ).memberhero_hide_menu();
			$( '.memberhero-profile-photo-link' ).addClass( 'memberhero-offcanvas' );
			$( '.memberhero-profile-photo-edit-overlay' ).addClass( 'memberhero-offcanvas' ).append( '<div class="memberhero-ripple"></div>' );
			$( document ).memberhero_save_crop( event, $memberhero_crop, 400, 400, 'save_avatar' );
		} )

		// Saves the cover photo.
		.on( 'click', '.memberhero-crop-cover:not(.disabled)', function( event ) {
			$( event.target ).parents( '.memberhero-cover-cropper-holder' ).addClass( 'memberhero-offcanvas' ).append( '<div class="memberhero-ripple"></div>' );
			$( document ).memberhero_save_crop( event, $memberhero_cover_crop, 1500, 555, 'save_cover' );
		} )

		// Fixes hover for upload button in dropdown menu.
		.on( 'mouseenter', '.memberhero-dropdown-upload', function() {
			$( this ).addClass( 'memberhero-dropdown-hover' );
		} ).on( 'mouseleave', '.memberhero-dropdown-upload', function() {
			$( this ).removeClass( 'memberhero-dropdown-hover' );
		} );

})(jQuery);