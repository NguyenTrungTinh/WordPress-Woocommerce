<?php

/* Decrease time place order */
add_filter( 'woocommerce_defer_transactional_emails','__return_true' );
/* Decrease time place order */

add_filter( 'woocommerce_quantity_input_min', 'woocommerce_quantity_min_10', 9999, 2 );
   
function woocommerce_quantity_min_10( $min, $product ) {  
      if ( 850 === $product->get_id() ) {
        // $min = ceil( 2400000 / $product->get_price() );
      }
	  elseif ( 839 === $product->get_id() ) {
         $min = ceil( 1800000 / $product->get_price() );
      }
	 elseif ( 838 === $product->get_id() ) {
         $min = ceil( 50000 / $product->get_price() );
      }
	 elseif ( 833 === $product->get_id() ) {
         $min = ceil( 370000 / $product->get_price() );
      }
	elseif ( 834 === $product->get_id() ) {
         $min = ceil( 370000 / $product->get_price() );
      }
	elseif ( 829 === $product->get_id() ) {
         $min = ceil( 380000 / $product->get_price() );
      }
	elseif ( 830 === $product->get_id() ) {
         $min = ceil( 425000 / $product->get_price() );
      }
	elseif ( 831 === $product->get_id() ) {
         $min = ceil( 340000 / $product->get_price() );
      }
	elseif ( 832 === $product->get_id() ) {
         $min = ceil( 340000 / $product->get_price() );
      }
	elseif ( 828 === $product->get_id() ) {
         $min = ceil( 480000 / $product->get_price() );
      }
	elseif ( 991 === $product->get_id() ) {
         $min = ceil( 900000 / $product->get_price() );
      }
 
   return $min;
 
}

add_filter( 'woocommerce_cart_item_quantity', 'woocommerce_quantity_min_10_cart', 9999, 3 );
   
function woocommerce_quantity_min_10_cart( $product_quantity, $cart_item_key, $cart_item ) {  
    
   $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    
   $min = 0;
 
   if ( 850 === $_product->get_id() ) {
      //   $min = ceil( 2400000 / $_product->get_price() );
      }
	  elseif ( 839 === $_product->get_id() ) {
         $min = ceil( 1800000 / $_product->get_price() );
      }
	 elseif ( 838 === $_product->get_id() ) {
         $min = ceil( 50000 / $_product->get_price() );
      }
	 elseif ( 833 === $_product->get_id() ) {
         $min = ceil( 370000 / $_product->get_price() );
      }
	elseif ( 834 === $_product->get_id() ) {
         $min = ceil( 370000 / $_product->get_price() );
      }
	elseif ( 829 === $_product->get_id() ) {
         $min = ceil( 380000 / $_product->get_price() );
      }
	elseif ( 830 === $_product->get_id() ) {
         $min = ceil( 425000 / $_product->get_price() );
      }
	elseif ( 831 === $_product->get_id() ) {
         $min = ceil( 340000 / $_product->get_price() );
      }
	elseif ( 832 === $_product->get_id() ) {
         $min = ceil( 340000 / $_product->get_price() );
      }
	elseif ( 828 === $_product->get_id() ) {
         $min = ceil( 480000 / $_product->get_price() );
      }
	elseif ( 991 === $_product->get_id() ) {
         $min = ceil( 900000 / $_product->get_price() );
      }
    
   $product_quantity = woocommerce_quantity_input( array(
      'input_name'   => "cart[{$cart_item_key}][qty]",
      'input_value'  => $cart_item['quantity'],
      'max_value'    => $_product->get_max_purchase_quantity(),
      'min_value'    => $min,
      'product_name' => $_product->get_name(),
   ), $_product, false ); 
    
   return $product_quantity;
 
}


add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}
add_filter('woocommerce_available_variation', 'variation_price_custom_suffix', 10, 3 );
function variation_price_custom_suffix( $variation_data, $product, $variation ) {
    $variation_data['price_html'] .= ' <span class="price-suffix">' . __("Giá: ", "woocommerce") . '</span>';

    return $variation_data;
}

function flash_off(){
	return false;	
}
add_filter('woocommerce_sale_flash','flash_off');

function remove_dashboard($items)
{
    unset($items['dashboard']);
	unset($items['edit-account']);
	unset($items['downloads']);
	unset($items['edit-address']);
    return $items;
}
add_filter('woocommerce_account_menu_items','remove_dashboard');

/* Edit name stock*/
function custom_get_availability_text( $availability, $product ) {
   $stock = $product->get_stock_quantity();
   if ( $product->is_in_stock() && $product->managing_stock() ) $availability = 'Còn lại: ' .'<b>'. $stock .'</b>'. '';
   return $availability;
}
add_filter( 'woocommerce_get_availability_text', 'custom_get_availability_text', 99, 2 );

/*Woocommerce minicart*/
add_filter('woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');
function woocommerce_header_add_to_cart_fragment( $fragments ) {
 global $woocommerce;
 ob_start();
 ?>
 <a class="cart-contents" href="<?php echo wc_get_checkout_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>"><?php echo sprintf(_n('%d sản phẩm', '%d sản phẩm', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?> - <?php echo $woocommerce->cart->get_cart_total(); ?>
 <span class="txt-cart">Xem giỏ hàng</span>
</a>
 <?php
 $fragments['a.cart-contents'] = ob_get_clean();
 return $fragments;
}

function header_top()
{
	$content = '';
	$content .= '<div class="wrapper-home">';
	$content .= '<div class="banner-header"><img src="https://lahacafe.vn/banhang/wp-content/uploads/2022/09/banner.jpg" /></div>';
	$content .= '<div class="logo-home"><img src="https://lahacafe.vn/banhang/wp-content/uploads/2022/09/logo-lahacafe.png" /></div>';
	$content .= '<p class="title-home">LAHA CAFE COMPANY</p>';
	$content .= '<p class="description-home">Chính sách bán hàng: <br>+ Thanh toán trước khi giao hàng. <br>+ Freeship đơn hàng từ 3.000.000đ. <br>+ Đơn dưới 3.000.000đ ship cố định 50.000đ.</p>';
	$content .= '</div>';
	return $content;
}
add_shortcode('header_shortcode','header_top');

/* function product_laha($args, $content)
{
    $args = array(
        'post_type' => 'product',
        'product_cat' => $args['cat']
        );
    global $product;
    global $woocommerce;
    $query = new WP_Query($args);
    $content = '';
    $content .= '<div class="wrapper-grid row">';
    
    if ($query -> have_posts()){
        
        while ($query -> have_posts())
        {
            $query -> the_post();
            global $product;
		//	$smell = get_field('mui-vi', $query->post->ID);
             $content .= '<div class="template-1">';
				$content .= '<a href="'. get_permalink() .'">';
			//	$content .=  get_the_post_thumbnail($query->post->ID, array(100, 100)) ;
				$content .= '<div class="product-wrapper">';
				$content .= '<div class="fs-v-wrap">';
				?>
		<?php // do_action( 'woocommerce_before_shop_loop_item' ); ?>
        <div class="product_thumb">
		<?php
		do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
			<?php
				wp_enqueue_script( 'magnific-popup');
				wp_enqueue_script( 'flexslider' );
				wp_enqueue_script( 'wc-add-to-cart-variation' );
				echo '<div class="quick-view" data-prod="' . esc_attr( $query->post->ID ) . '"><a href="javascript:;"><i class="tk tk-eye fa-fw"></i></a></div>';
			
			?>
            <a href="<?php // echo get_the_permalink(); ?>" class="link-images-product"></a>
            </div>
             <div class="product__title">
				 <a href="#"><div class="product-title"><?php the_title(); ?></div></a>

			<div class="block-after-title">
			<?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
			</div>
			  <div class="description">
				<?php // echo apply_filters( 'woocommerce_short_description', thim_excerpt( 30 ) ); ?>
            </div>
			<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
        </div>
				<?php
			//	$content .=  '<div class="product-title">'.get_the_title().'</div>';
				$content .= '</a>';
			//	$content .= '<div class="product-price fs-v grid-price">';
			//	$content .= $product->get_price_html();
		//		$content .= '<a class="recent-btn button product_type_simple add_to_cart_button ajax_add_to_cart" href="/banhang/?add-to-cart='.$query->post->ID.'"><div class="btn-contact"><span>Mua ngay</span></div></a>';
      //          $content .= '<a class="quick-view-link"><i class="fas fa-plus-circle"></i></a>'; 
		//		$content .= '</div>';
				$content .= '</div>';
		
				$content .= '</div>';
				$content .= '</div>';
        }
   
    $content .= '</div>';
    	   wp_reset_postdata();
    }
    ?>
    
    <?php
        
        return $content;
}
add_shortcode('product_laha_shortcode','product_laha'); */

/*
add_action( 'template_redirect', 'empty_cart_redirection' );
function empty_cart_redirection(){
    if( WC()->cart->is_empty() && is_checkout() ){
        wp_safe_redirect( esc_url( home_url( '/' ) ) );
        exit;
    }
} */

//add_action( 'template_redirect', 'empty_cart_redirection' );

function thim_child_enqueue_styles() {
	wp_enqueue_style( 'thim-parent-style', get_template_directory_uri() . '/style.css', array(), THIM_THEME_VERSION  );
	$style_version = filemtime(get_stylesheet_directory().'/style.css');
	wp_enqueue_style('thim-style', get_stylesheet_uri(), array(), $style_version);

	wp_register_script('script', get_stylesheet_directory_uri() . '/assets/js/custom-script.js');
	wp_enqueue_script('script');
	wp_register_script('ajax-script', get_stylesheet_directory_uri() . '/assets/js/ajax.js');
	wp_enqueue_script('ajax-script');

	wp_enqueue_style('fancy-box-css','https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css');
		
	wp_register_script('fancy-box-js','https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js');
	wp_enqueue_script('fancy-box-js');
	
	wp_enqueue_style('goong-css','https://cdn.jsdelivr.net/npm/@goongmaps/goong-js@1.0.9/dist/goong-js.css');
		
	wp_register_script('goong-js','https://cdn.jsdelivr.net/npm/@goongmaps/goong-js@1.0.9/dist/goong-js.js');
	wp_enqueue_script('goong-js');
	
	wp_enqueue_style('goong-geo-css','https://cdn.jsdelivr.net/npm/@goongmaps/goong-geocoder/dist/goong-geocoder.css');
		
	wp_register_script('goong-geo-js','https://cdn.jsdelivr.net/npm/@goongmaps/goong-geocoder/dist/goong-geocoder.min.js');
	wp_enqueue_script('goong-geo-js');

}

add_action( 'wp_enqueue_scripts', 'thim_child_enqueue_styles', 1000 );
/*
function woo_plus_minus(){
	if(is_product() || is_cart() || is_single() || is_page()):
	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
	jQuery('<div class="quantity-nav"><div class="quantity-button quantity-down">-</div><div class="quantity-button quantity-up">+</div></div>').insertAfter('.quantity input');
    jQuery('.quantity').each(function() {
      var spinner = jQuery(this),
        input = spinner.find('input[type="number"]'),
        btnUp = spinner.find('.quantity-up'),
        btnDown = spinner.find('.quantity-down'),
        min = input.attr('min'),
        max = input.attr('max');

      btnUp.click(function() {
        var oldValue = parseFloat(input.val());
        if (oldValue >= max) {
          var newVal = oldValue;
        } else {
          var newVal = oldValue + 1;
        }
        spinner.find("input").val(newVal);
        spinner.find("input").trigger("change");
      });

      btnDown.click(function() {
        var oldValue = parseFloat(input.val());
        if (oldValue <= min) {
          var newVal = oldValue;
        } else {
          var newVal = oldValue - 1;
        }
        spinner.find("input").val(newVal);
        spinner.find("input").trigger("change");
      });

    });
			});
		jQuery(document.body).on('removed_from_cart updated_cart_totals', function () {
   		jQuery('<div class="quantity-nav"><div class="quantity-button quantity-up">+</div><div class="quantity-button quantity-down">-</div></div>').insertAfter('.quantity input');
    jQuery('.quantity').each(function() {
      var spinner = jQuery(this),
        input = spinner.find('input[type="number"]'),
        btnUp = spinner.find('.quantity-up'),
        btnDown = spinner.find('.quantity-down'),
        min = input.attr('min'),
        max = input.attr('max');

      btnUp.click(function() {
        var oldValue = parseFloat(input.val());
        if (oldValue >= max) {
          var newVal = oldValue;
        } else {
          var newVal = oldValue + 1;
        }
        spinner.find("input").val(newVal);
        spinner.find("input").trigger("change");
      });

      btnDown.click(function() {
        var oldValue = parseFloat(input.val());
        if (oldValue <= min) {
          var newVal = oldValue;
        } else {
          var newVal = oldValue - 1;
        }
        spinner.find("input").val(newVal);
        spinner.find("input").trigger("change");
      });

    });
		});
		</script>
	<?php
	endif;
}
add_action('wp_footer','woo_plus_minus'); */

add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single' ); 
function woocommerce_add_to_cart_button_text_single() {
return __( 'Chọn', 'woocommerce' ); 
}

function cart_auto_update(){
	if(is_cart()):
	?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		var timeout;
		jQuery('div.woocommerce').on('change', '.qty', function(){
		if(timeout !== undefined){
			clearTimeout(timeout);
		}
		jQuery("[name='update_cart']").removeAttr('disable');
		timeout = setTimeout(function(){
			jQuery("[name='update_cart']").trigger("click");
		}, 100);
		
	    });
			});
	</script>
	<?php
	endif;
}
add_action('wp_footer','cart_auto_update');
/*Woo billing*/
function checkout_fields($fields){
	unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_first_name']);
	unset($fields['billing']['billing_country']);
	unset($fields['billing']['billing_city']);
	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_address_2']);
	$fields['billing']['billing_last_name'] = array(
	'label' => __('Họ và tên', 'eduma'),
	'placeholder' => _x('Nhập họ và tên của bạn','placeholder','eduma'),
	'class' => array('form-row-wide'),
	'required' => true,
	'clear' => true
	);
	$fields['billing']['billing_last_name']['priority'] = '30';
	$fields['billing']['billing_address_1']['placeholder'] = 'Chọn địa chỉ của bạn';
//	$fields['billing']['billing_address_1']['type'] = 'hidden';
    $fields['billing']['billing_address_1']['priority'] = '20';
	$fields['billing']['billing_phone']['placeholder'] = 'Nhập số điện thoại';
	$fields['billing']['billing_email']['placeholder'] = 'Nhập địa chỉ email';
	$fields['billing']['billing_email']['required'] = false;
	return $fields;
}
add_filter('woocommerce_checkout_fields','checkout_fields',99);
add_filter( 'woocommerce_product_tabs', 'my_remove_all_product_tabs', 98 );
 
function my_remove_all_product_tabs( $tabs ) {
  unset( $tabs['description'] );        // Remove the description tab
  unset( $tabs['reviews'] );       // Remove the reviews tab
  unset( $tabs['additional_information'] );    // Remove the additional information tab
  return $tabs;
}
/*Woo billing*/
// 1. Add Woocommerce cart page on the checkout page

add_action( 'woocommerce_before_checkout_form', 'add_cart_on_checkout', 5 );
 
function add_cart_on_checkout() {
 if ( is_wc_endpoint_url( 'order-received' ) ) return;
 echo do_shortcode('[woocommerce_cart]'); // Woocommerce cart page shortcode
}
// 2. Redirect cart page to checkout
add_action( 'template_redirect', function() {
  
// Replace "cart"  and "checkout" with cart and checkout page slug if needed
    if ( is_page( 'cart' ) ) {
        wp_redirect( '/checkout/' );
        die();
    }
} );
// Redirect to home url from empty Woocommerce checkout page

add_action( 'template_redirect', 'redirect_empty_checkout' );
 
function redirect_empty_checkout() {
    if ( is_checkout() && 0 == WC()->cart->get_cart_contents_count() && ! is_wc_endpoint_url( 'order-pay' ) && ! is_wc_endpoint_url( 'order-received' ) ) {
	$url = home_url();
  //wp_safe_redirect( get_permalink( wc_get_page_id( 'shop' ) ) ); 
	wp_redirect($url); 
    exit;
    }
}
add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false' );
add_filter( 'woocommerce_checkout_update_order_review_expired', '__return_false' );


add_filter( 'wc_add_to_cart_message_html', 'remove_add_to_cart_message' );
 
function remove_add_to_cart_message( $message ){
	return '';
}

 function product_laha($args, $content)
{
    $args = array(
        'post_type' => 'product',
        'product_cat' => $args['cat'],
		'posts_per_page' => -1,
		'meta_query' => array(
         array(
            'key' => '_stock_status',
            'value' => 'instock',
            'compare' => '=',
        )
    )    
        );
    global $product;
    global $woocommerce;
    $query = new WP_Query($args);
	 ?>
	<div class="wrapper-grid row">
	<?php

    if ($query -> have_posts())  {
       ?>
				
		<?php
        while ($query -> have_posts())
        {
            $query -> the_post();
		?>
		<?php // do_action( 'woocommerce_before_shop_loop_item' );  ?>
		<div class="template-1">
        <div class="product_thumb">
		<?php
		do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
			<?php
			//	wp_enqueue_script( 'magnific-popup');
			//	wp_enqueue_script( 'flexslider' );
			//	wp_enqueue_script( 'wc-add-to-cart-variation' );
			//	echo '<div class="quick-view" data-prod="' . esc_attr( $query->post->ID ) . '"><a href="javascript:;"><i class="tk tk-eye fa-fw"></i></a></div>';
			
			?>
            <a class="link-images-product"></a>
            </div>
             <div class="product__title">
			 <div class="product-title"><?php the_title(); ?></div>

			 <div class="block-after-title">
			 <?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
			 </div>
			 <div class="description">
				<?php // echo apply_filters( 'woocommerce_short_description', thim_excerpt( 30 ) ); ?>
             </div>
		
			 <?php 
			do_action( 'woocommerce_single_product_summary_quick' );
			//do_action( 'woocommerce_after_shop_loop_item' ); 
				 ?>
        	 </div>
		 	 </div>
				<?php
        		}  ?>
			<?php
    	   wp_reset_postdata();
    		} 
    ?>
				</div>
    <?php
        
        return $content;
}
add_shortcode('product_laha_shortcode','product_laha');


 
add_filter('woocommerce_bacs_accounts', '__return_false');
 
add_action( 'woocommerce_email_before_order_table', 'devvn_email_instructions', 10, 3 );
function devvn_email_instructions( $order, $sent_to_admin, $plain_text = false ) {
 
    if ( ! $sent_to_admin && 'bacs' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
        devvn_bank_details( $order->get_id() );
    }
 
}
 
add_action( 'woocommerce_thankyou_bacs', 'devvn_thankyou_page' );
function devvn_thankyou_page($order_id){
    devvn_bank_details($order_id);
}
 
function devvn_bank_details( $order_id = '' ) {
    $bacs_accounts = get_option('woocommerce_bacs_accounts');
    if ( ! empty( $bacs_accounts ) ) {
        ob_start();
        echo '<table class="bank-transfer">';
        ?>
        <tr>
            <td colspan="2" style="border: 1px solid #eaeaea;padding: 6px 10px;"><strong>Thông tin chuyển khoản</strong></td>
        </tr>
        <?php
        foreach ( $bacs_accounts as $bacs_account ) {
            $bacs_account = (object) $bacs_account;
            $account_name = $bacs_account->account_name;
            $bank_name = $bacs_account->bank_name;
            $stk = $bacs_account->account_number;
            $icon = $bacs_account->iban;
            ?>
            <tr>
                <td style="width:30%;"><?php if($icon):?><img src="<?php echo $icon;?>" alt=""/><?php endif;?></td>
                <td style="padding: 6px 10px;">
                    <div><strong>STK:</strong> <?php echo $stk;?></div><div><strong>Chủ tài khoản:</strong> <?php echo $account_name;?></div><div><strong>Ngân hàng:</strong> <?php echo $bank_name;?></div><div><strong>Nội dung:</strong> DH<?php echo $order_id;?></div>
                </td>
            </tr>
            <?php
        }
        echo '</table>';
        echo ob_get_clean();
    }
 
}

/*0d -> gia si*/
function devvn_wc_custom_get_price_html( $price, $product ) {
    if ( $product->get_price() == 0 ) {
        if ( $product->is_on_sale() && $product->get_regular_price() ) {
            $regular_price = wc_get_price_to_display( $product, array( 'qty' => 1, 'price' => $product->get_regular_price() ) );
 
            $price = wc_format_price_range( $regular_price, __( 'Free!', 'woocommerce' ) );
        } else {
            $price = '<span class="amount sale-p">' . __( 'Giá sỉ', 'woocommerce' ) . '</span>';
        }
    }
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'devvn_wc_custom_get_price_html', 10, 2 );

/*het hang*/
function devvn_oft_custom_get_price_html( $price, $product ) {
    if ( !is_admin() && !$product->is_in_stock()) {
       $price = '<span class="amount out-s">' . __( 'Hết hàng', 'woocommerce' ) . '</span>';
    }
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'devvn_oft_custom_get_price_html', 99, 2 );

/**/
/**
* Apply a coupon for minimum cart total
*/
/*
add_action( 'woocommerce_before_cart' , 'add_coupon_notice' );
add_action( 'woocommerce_before_checkout_form' , 'add_coupon_notice' );

function add_coupon_notice() {

        $cart_total = WC()->cart->get_subtotal();
        $minimum_amount = 2000000;
        $currency_code = get_woocommerce_currency();
        wc_clear_notices();

       if ( $cart_total < $minimum_amount ) {
            //  WC()->cart->remove_coupon( 'Freeship' );
        //    WC()->cart->apply_coupon( 'Freeship' );
       
              
        } else {
              //WC()->cart->apply_coupon( 'Freeship' );
              WC()->cart->remove_coupon( 'Freeship' );
        }
          wc_clear_notices();
} 
*/

/**
 * Add custom fee if more than three article
 * @param WC_Cart $cart
 */

function add_custom_fees( WC_Cart $cart ){
    $cart_total = WC()->cart->get_subtotal();
    $minimum_amount = 3000000;
    if( $cart_total < $minimum_amount ){
        $fee = 50000;
        $cart->add_fee( 'Đơn < 3.000.000đ phí',+ $fee);
        return;
    }
}
add_action('woocommerce_cart_calculate_fees' , 'add_custom_fees'); 

/*Change price variation*/
/*
function wc_wc20_variation_price_format( $price, $product ) {
 //Main Price
 $prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
 $price = $prices[0] !== $prices[1] ? sprintf( __( 'Giá từ: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
 
 // Sale Price
 $prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
 sort( $prices );
 $saleprice = $prices[0] !== $prices[1] ? wc_price( $prices[0] ) : wc_price( $prices[0] );
 
 if ( $price !== $saleprice ) {
 $price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
 }
 return $price;
}
add_filter( 'woocommerce_variable_sale_price_html', 'wc_wc20_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_wc20_variation_price_format', 10, 2 );
*/
/*
function logged_in_for_1_year( $expirein ) {
return 31556926; // 1 year in seconds
}
add_filter( 'auth_cookie_expiration', 'logged_in_for_1_year' ); */


add_filter('auth_cookie_expiration', function(){
return YEAR_IN_SECONDS * 2;
}); 

/* Link back lahacafe.vn
function renew_wp_cookie() {
    //Return early if its on login/logout page
  //  if(in_array($GLOBALS['pagenow'], array('wp-login.php'))) return;

    if (is_user_logged_in()) {
        $current_logged_user = get_current_user_id();
        wp_set_auth_cookie($current_logged_user, true);
    }

}

add_action('init', 'renew_wp_cookie'); */