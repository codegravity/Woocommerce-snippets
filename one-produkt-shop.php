<?php
/*  misc functions for simple 1 product web shop */
/* to be added to functions.php file  */

/* -----------  Woocommerce functions ----------- */
// remove cart when purchase complete a short code for paysson checkout
// check if checkout page
function cg_paysson_cart_func( $atts ){
	if ( is_Page('kassan')){
			if ( is_wc_endpoint_url( 'order-received' ) ) {
				$message = " proccessing order";
				#echo "<script type='text/javascript'>alert('$message');</script>";
			}else{
				return '<div style="max-width: 800px; margin: 0 auto;">' . do_shortcode( '[woocommerce_cart]' ) . '</div>';
			}	
	}
}
add_shortcode( 'cg_paysson_woo_cart', 'cg_paysson_cart_func' );

// remove bradcrumbs
add_action('template_redirect', 'remove_shop_breadcrumbs' );
function remove_shop_breadcrumbs(){
 
	if (is_single()) {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
	}
 
}

// remove sidebar
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar',10);

// remove product tabs
//add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

//function woo_remove_product_tabs( $tabs ) {
//
//    unset( $tabs['description'] );      	// Remove the description tab
//    unset( $tabs['reviews'] ); 			// Remove the reviews tab
//    unset( $tabs['additional_information'] );  	// Remove the additional information tab
//
//    return $tabs;
//
//}
// remove product meta categories etc etc
//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

// add direct to checkout
add_filter ('add_to_cart_redirect', 'redirect_to_checkout');
function redirect_to_checkout() {
    global $woocommerce;
    //$checkout_url = $woocommerce->cart->get_checkout_url(); // to normal kassan
	//$checkout_url = '/kassa/'; // to normal kassan
	//$checkout_url = '/klarna-checkout/';	
	$checkout_url = '/kassan/';	
	return $checkout_url;
}

// add to cart text adjust
add_filter( 'add_to_cart_text', 'woo_custom_cart_button_text' );                                // < 2.1
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );    // 2.1 +
function woo_custom_cart_button_text() {

        return __( 'KÖP', 'woocommerce' );

}
add_filter( 'woocommerce_product_add_to_cart_text', 'woo_archive_custom_cart_button_text' );    // 2.1 +
function woo_archive_custom_cart_button_text() {
 
        return __( 'KÖP', 'woocommerce' );
 
}


// shortcode to köp

function cg_product_add_to_cart( $atts ) {
		global $wpdb, $post;

		if ( empty( $atts ) ) {
			return '';
		}

		$atts = shortcode_atts( array(
			'id'         => '',
			'class'      => '',
			'quantity'   => '1',
			'sku'        => '',
			'style'      => 'border:3px solid #57c32f; padding: 2.2em 1.5em;',
			'show_price' => 'true'
		), $atts, 'product_add_to_cart' );

		if ( ! empty( $atts['id'] ) ) {
			$product_data = get_post( $atts['id'] );
		} elseif ( ! empty( $atts['sku'] ) ) {
			$product_id   = wc_get_product_id_by_sku( $atts['sku'] );
			$product_data = get_post( $product_id );
		} else {
			return '';
		}

		$product = is_object( $product_data ) && in_array( $product_data->post_type, array( 'product', 'product_variation' ) ) ? wc_setup_product_data( $product_data ) : false;

		if ( ! $product ) {
			return '';
		}

		$styles = empty( $atts['style'] ) ? '' : ' style="' . esc_attr( $atts['style'] ) . '"';

		ob_start();
		?>
       <!-- <div class="corner-shout"></div>-->
        <div>
		<p class="product corner-shout2 woocommerce add_to_cart_inline <?php echo esc_attr( $atts['class'] ); ?>"<?php echo $styles; ?>>
			<?php if ( 'true' == $atts['show_price'] ) : ?>
				<span class="cg-pris">PRIS: </span><span class="cg-price-style"><?php echo $product->get_price_html(); ?></span>
			<?php endif; ?>    
            </br>  
			<span class="cg-skickas">Skickas normalt inom en arbetsdag</span>
         </br>
         	<span id="kopbtn">
				<?php woocommerce_template_loop_add_to_cart( array( 'quantity' => $atts['quantity'] ) ); ?>
			</span>
		</p>
		</div>
		<?php

		// Restore Product global in case this is shown inside a product post
		wc_setup_product_data( $post );

		return ob_get_clean();
	}	
add_shortcode( 'cg_add_to_cart', 'cg_product_add_to_cart' );	
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

// define the wc_add_to_cart_message 
function empty_wc_add_to_cart_message( $message, $product_id ) { 
    return ''; 
}; 
add_filter( 'wc_add_to_cart_message', 'empty_wc_add_to_cart_message', 10, 2 );


function remove_added_to_cart_notice()
{
    $notices = WC()->session->get('wc_notices', array());
    foreach( $notices['success'] as $key => &$notice){
        if( strpos( $notice, 'has been added' ) !== false){
            $added_to_cart_key = $key;
            break;
        }
    }
    unset( $notices['success'][$added_to_cart_key] );
    WC()->session->set('wc_notices', $notices);
}
add_action('woocommerce_before_single_product','remove_added_to_cart_notice',1);
add_action('woocommerce_shortcode_before_product_cat_loop','remove_added_to_cart_notice',1);
add_action('woocommerce_before_shop_loop','remove_added_to_cart_notice',1);

function custom_override_checkout_fields( $fields ) {
  #  unset($fields['billing']['billing_first_name']);
  #  unset($fields['billing']['billing_last_name']);
    unset($fields['billing']['billing_company']);
#    unset($fields['billing']['billing_address_1']);
 #   unset($fields['billing']['billing_address_2']);
#    unset($fields['billing']['billing_city']);
#    unset($fields['billing']['billing_postcode']);
#    unset($fields['billing']['billing_country']);
 #   unset($fields['billing']['billing_state']);
#    unset($fields['billing']['billing_phone']);
#    unset($fields['order']['order_comments']);
 #   unset($fields['billing']['billing_address_2']);
 #   unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_company']);
#    unset($fields['billing']['billing_last_name']);
#    unset($fields['billing']['billing_email']);
#    unset($fields['billing']['billing_city']);

  unset($fields['shipping']['shipping_company']);
    return $fields;
}

// will produce code before the form
// gets the cart template and outputs it before the form
add_action('woocommerce_before_checkout_form','show_cart_summary',9);

function show_cart_summary( ) {
  wc_get_template_part( 'cart/cart' );
}

