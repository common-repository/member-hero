<?php
/**
 * Lifetime access popup.
 */

$memberhero_signup_nonce = wp_create_nonce( 'memberhero-signup-nonce' ); ?>
<style>
    .memberhero-hidden{

      overflow: hidden;
    }
    .memberhero-popup-overlay .memberhero-internal-message{
      margin: 3px 0 3px 22px;
      display: none;
    }

  .memberhero-popup-overlay{

    background: rgba(0,0,0, .4);
    position: fixed;
    top:0;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 1000000;
    overflow: auto;
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .memberhero-popup-overlay.memberhero-active{
    opacity: 1;
    visibility: visible;
  }
  .memberhero-serveypanel{
    width: 600px;
    background: #fff;
    margin: 0 auto 0;
    border-radius: 3px;
  }
  .memberhero-popup-header{
    background: #f1f1f1;
    padding: 20px;
    border-bottom: 1px solid #ccc;
  }
  .memberhero-popup-header h2{
    margin: 0;
	background: url(<?php echo memberhero()->plugin_url(); ?>/assets/images/menu.png) no-repeat left center;
	background-size: 24px 24px;
	min-height: 24px;
	line-height: 24px;
	padding-left: 35px;
  }
  .memberhero-popup-body{
      padding: 10px 20px;
  }
  .memberhero-popup-footer{
    background: #f9f3f3;
    padding: 10px 20px;
    border-top: 1px solid #ccc;
  }
  .memberhero-popup-footer:after{

    content:"";
    display: table;
    clear: both;
  }
  .action-btns{
    float: right;
  }
  .memberhero-anonymous{

    display: none;
  }

  .memberhero-spinner{
	  display: none;
    margin-right: 10px;
  }
  .memberhero-spinner img{
    margin-top: 5px;
  }
  .memberhero-popup-header{
    background: none;
        padding: 18px 15px;
    -webkit-box-shadow: 0 0 8px rgba(0,0,0,.1);
    box-shadow: 0 0 8px rgba(0,0,0,.1);
    border: 0;
}
.memberhero-popup-body h3{
    margin-top: 0;
    margin-bottom: 30px;
        font-weight: 700;
    font-size: 15px;
    color: #495157;
    line-height: 1.4;
}
.memberhero-reason{
    font-size: 13px;
    color: #6d7882;
    margin-bottom: 15px;
}
.memberhero-reason input[type="radio"]{
margin-right: 15px;
}
.memberhero-popup-body{
padding: 30px 30px 0;

}
.memberhero-popup-footer{
background: none;
    border: 0;
    padding: 29px 39px 39px;
}

.memberhero-popup-field {
	color: #6d7882;
	padding: 0 0 20px;
}

.memberhero-popup-field input[type=text] {
	width: 100%;
	display: block;
}

.memberhero-radio label {
	margin: 0 10px;
}
.memberhero-error {
	color: #ff3000;
	padding: 0 0 20px;
	display: none;
}
</style>
<div class="memberhero-popup-overlay memberhero-popup">
  <div class="memberhero-serveypanel">
    <form action="#" method="post" id="memberhero-signup-form">
    <div class="memberhero-popup-header">
      <h2><?php _e( 'Get free lifetime access to Member Hero Pro', 'memberhero' ); ?></h2>
    </div>
    <div class="memberhero-popup-body">
		 <div class="memberhero-error"></div>
      <h3><?php _e( 'Sign up for free lifetime access to Member Hero Pro.', 'memberhero' ); ?></h3>
      <input type="hidden" class="memberhero_signup_nonce" name="memberhero_signup_nonce" value="<?php echo $memberhero_signup_nonce; ?>">
		<div class="memberhero-popup-field">
			<input type="text" name="customer_email" id="customer_email" value="" placeholder="<?php esc_attr_e( 'Email', 'memberhero' ); ?>" required />
		</div>
		<div class="memberhero-popup-field">
			<input type="text" name="customer_name" id="customer_name" value="" placeholder="<?php esc_attr_e( 'Name', 'memberhero' ); ?>" required />
		</div>
		<div class="memberhero-popup-field memberhero-radio">
			<?php _e( 'I use Member Hero for:', 'memberhero' ); ?>
			<label>
				<span>
					<input type="radio" name="customer_use" value="myself" checked > <?php _e( 'Myself', 'memberhero' ); ?>
				</span>
			</label>
			<label>
				<span>
					<input type="radio" name="customer_use" value="my_clients"> <?php _e( 'My clients', 'memberhero' ); ?>
				</span>
			</label>
		</div>
		<div class="memberhero-popup-field">
			<label>
				<span>
					<input type="checkbox" name="customer_signup" id="customer_signup" value="yes"> <?php _e( 'Please sign me up for lifetime access to the new premium Member Hero when it&rsquo;s launched.', 'memberhero' ); ?>
				</span>
			</label>
		</div>
    </div>
    <div class="memberhero-popup-footer">
  
      <div class="action-btns">
        <span class="memberhero-spinner"><img src="<?php echo admin_url( '/images/spinner.gif' ); ?>" alt=""></span>
        <input type="submit" class="button button-primary button-signup memberhero-popup-allow-signup" value="<?php _e( 'Sign me up!', 'memberhero'); ?>" disabled="disabled">
        <a href="#" class="button button-secondary memberhero-popup-button-close"><?php _e( 'Cancel', 'memberhero' ); ?></a>

      </div>
    </div>
  </form>
    </div>
  </div>


  <script>
    (function( $ ) {

      $(function() {

        $(document).on('click', '.memberhero-get-access', function(e){
          e.preventDefault();
          $('.memberhero-popup-overlay').addClass('memberhero-active');
          $('body').addClass('memberhero-hidden');
		  return false;
        });
        $(document).on('click', '.memberhero-popup-button-close', function () {
          close_popup();
        });
        $(document).on('click', ".memberhero-serveypanel",function(e){
            e.stopPropagation();
        });

        $(document).click(function(){
          close_popup();
        });

        $('input[type="checkbox"][name="customer_signup"]').on('click', function(event) {
			if ( $( this ).is( ':checked' ) ) {
				 $(".memberhero-popup-allow-signup").removeAttr('disabled');
			} else {
				$(".memberhero-popup-allow-signup").attr('disabled', 'disabled');
			}
        });

        $(document).on('submit', '#memberhero-signup-form', function(event) {
          event.preventDefault();

		var customer_email 	= $('input[type="text"][name="customer_email"]').val();
		var customer_name 	= $('input[type="text"][name="customer_name"]').val();
		var customer_use 	= $('input[type="radio"][name="customer_use"]:checked').val();
		var signup_nonce 	= $('.memberhero_signup_nonce').val();

          $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
				action        	: 'memberhero_access_signup',
				customer_email 	: customer_email,
				customer_name 	: customer_name,
				customer_use  	: customer_use,
				security      	: signup_nonce
            },
            beforeSend: function(){
              $(".memberhero-spinner").show();
			  $(".memberhero-error").hide();
              $(".memberhero-popup-allow-signup").attr("disabled", "disabled");
            },
			success: function( response ) {
				if ( response.error ) {
					$(".memberhero-spinner").hide();
					$(".memberhero-popup-allow-signup").removeAttr('disabled');
					$(".memberhero-error").show().html( response.error );
				} else {
					close_popup();
					$( '.mhero-dynamic-msg' ).html( response.message );
				}
			}
          });

        });

        function close_popup() {
		  $(".memberhero-spinner").hide();
		  $(".memberhero-error").hide();
          $('.memberhero-popup-overlay').removeClass('memberhero-active');
          $('#memberhero-signup-form').trigger("reset");
          $(".memberhero-popup-allow-signup").attr('disabled', 'disabled');
          $('body').removeClass('memberhero-hidden');
        }
        });

        })( jQuery ); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
  </script>
