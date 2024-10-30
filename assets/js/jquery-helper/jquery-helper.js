/*
 * Get a specific query param from URL.
 */
( function( $ ) {
    $.fn.getparam = function( p ) {
		var h = this.attr( 'href' );
        var r = new RegExp( '[\?&]' + p + '=([^&#]*)' ).exec( h );
		if ( r == null ) {
			return null;
		}
		return decodeURI( r[1] ) || 0;
    };
} ( jQuery ) );

/*
 * Get a grid size for column.
 */
( function( $ ) {
    $.fn.getsize = function() {
		o = null;
		$.each( this.attr( 'class' ).split( ' ' ),
			function( k, v ){
				if( v.indexOf( 'grid-' ) >-1 ){
					o = parseInt( v.replace( 'grid-', '' ) );
				}
			}
		);
		return o;
    };
} ( jQuery ) );

/*
 * Conditional fields.
 */
( function( $ ) {
	$.fn.conditional = function() {
		return this.each( function() {
			var el = $( this ),
				classes = el.attr( 'class' ).split(' '),
				hide;
			for ( i = 0; i < classes.length; i++ ) {
				if ( classes[i].match(/show_/) ) {
					c = classes[i].replace( 'show_if_', '' );
					s = c.split( '_eq_' );
					d = s[0];
					v = s[1];
					var e = $( '#' + d ),
						t = e.parents( 'div.form-field' ).attr( 'data-type' );
					if ( t == 'select' ) {
						if ( e.val() == v ) {

						} else {
							hide = 1;
						}
					}
					if ( t == 'switch' ) {
						if ( e.is( ':checked' ) ) {
							
						} else {
							hide = 1;
						}
					}
				}
			}
			if ( hide == 1 ) {
				el.addClass( 'hidden' );
			} else {
				el.removeClass( 'hidden' );
			}
		} );
	};
} ( jQuery ) );