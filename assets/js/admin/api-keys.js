(function( $ ) {
	"use strict";

	var APIView = Backbone.View.extend({
		/**
		 * Element
		 *
		 * @param {Object} '#key-fields'
		 */
		el: $( '#key-fields' ),

		/**
		 * Events
		 *
		 * @type {Object}
		 */
		events: {
			'click input#update_api_key': 'saveKey'
		},

		/**
		 * Initialize actions
		 */
		initialize: function(){
			_.bindAll( this, 'saveKey' );
		},

		/**
		 * Init jQuery.BlockUI
		 */
		block: function() {
			$( this.el ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		/**
		 * Remove jQuery.BlockUI
		 */
		unblock: function() {
			$( this.el ).unblock();
		},

		/**
		 * Save API Key using ajax
		 *
		 * @param {Object} e
		 */
		saveKey: function( e ) {
			e.preventDefault();

			var self = this;

			self.block();

			Backbone.ajax({
				method:   'POST',
				dataType: 'json',
				url:      memberhero_admin_api_keys.ajax_url,
				data:     {
					action:      'memberhero_update_api_key',
					security:    memberhero_admin_api_keys.update_api_nonce,
					key_id:      $( '#key_id', self.el ).val(),
					description: $( '#key_description', self.el ).val(),
					user:        $( '#key_user', self.el ).val(),
					permissions: $( '#key_permissions', self.el ).val()
				},
				success: function( response ) {
					$( '.memberhero-api-message', self.el ).remove();

					if ( response.success ) {
						var data = response.data;

						$( 'h2, h3', self.el ).first().append( '<div class="memberhero-api-message updated"><p>' + data.message + '</p></div>' );

						if ( 0 < data.consumer_key.length && 0 < data.consumer_secret.length ) {
							$( '#api-keys-options', self.el ).remove();
							$( 'p.submit', self.el ).empty().append( data.revoke_url );

							var template = wp.template( 'api-keys-template' );

							$( 'p.submit', self.el ).before( template({
								consumer_key:    data.consumer_key,
								consumer_secret: data.consumer_secret
							}) );
						} else {
							$( '#key_description', self.el ).val( data.description );
							$( '#key_user', self.el ).val( data.user_id );
							$( '#key_permissions', self.el ).val( data.permissions );
						}
					} else {
						$( 'h2, h3', self.el ).first().append( '<div class="memberhero-api-message error"><p>' + response.data.message + '</p></div>' );
					}

					self.unblock();
				}
			});
		}
	});

	new APIView();

})( jQuery );