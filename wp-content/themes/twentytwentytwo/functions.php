<?php
/**
 * Twenty Twenty-Two functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Two
 * @since Twenty Twenty-Two 1.0
 */

function header_top()
{
	$content = '';
	$content .= '<div class="wrapper-home">';
	$content .= '<div class="banner-header"><img src="https://lahacafe.vn/banhang/wp-content/uploads/2022/09/banner.jpg" /></div>';
	$content .= '<div class="logo-home"><img src="https://lahacafe.vn/banhang/wp-content/uploads/2022/09/logo-lahacafe.png" /></div>';
	$content .= '<p class="title-home">LAHA CAFE COMPANY</p>';
	$content .= '<p class="description-home">– Số tài khoản: Hoàng Văn Việt – 161122448888 – ACB CN Nhiêu Lộc <br>Chính sách bán hàng: <br>+ Thanh toán trước khi giao hàng. <br>+ Freeship đơn hàng từ 2.000.000đ. <br>+ Đơn dưới 2.000.000đ ship cố định 50.000đ.</p>';
	$content .= '</div>';
	return $content;
}
add_shortcode('header_shortcode','header_top');

function product_laha($args, $content)
{
    $args = array(
        'post_type' => 'product',
        'product_cat' => $args['cat']
        );
    $query = new WP_Query($args);
    $content = '';
    $content .= '<div class="wrapper-grid">';
    
    if ($query -> have_posts()){
        
        while ($query -> have_posts())
        {
            $query -> the_post();
            global $product;
		//	$smell = get_field('mui-vi', $query->post->ID);
             $content .= '<div class="template-1">';
				$content .= '<a href="'. get_permalink() .'">';
				$content .=  get_the_post_thumbnail($query->post->ID, array(120, 120)) ;
				
				$content .= '<div class="product-wrapper">';
		//		$content .= '<img class="voucher" src="'.get_field('voucher', $query->post->ID).'" />';
				$content .= '<div class="fs-v-wrap">';
				
				$content .=  '<div class="fs-v-title" style="">'.get_the_title().'</div>';
				$content .= '</a>';
				$content .= '<div class="product-price fs-v grid-price">';
				$content .= $product->get_price_html();
//				$content .= '<a class="recent-btn button product_type_simple add_to_cart_button ajax_add_to_cart" href="/?add-to-cart='.$query->post->ID.'"><div class="btn-contact"><span>Mua ngay</span></div></a>';
				$content .= '</div>';
				$content .= '</div>';
				$content .= '<div class="fs-v-price woocommerce">';
			//	$content .= '<div style="height:26px;" class="woocommerce fs-reviews">'. wc_get_rating_html($product->get_average_rating()) .'</div>';
				$content .= '</a>';
				$content .= '</div>';
				$content .= '</div>';
				$content .= '</div>';
        }
        
   
    $content .= '</div>';
    }
        
        return $content;
}
add_shortcode('product_laha_shortcode','product_laha');

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
add_action('wp_footer','woo_plus_minus'); 
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
		}, 1000);
		
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
	$fields['billing']['billing_address_1']['placeholder'] = 'Nhập địa chỉ của bạn';
	$fields['billing']['billing_phone']['placeholder'] = 'Nhập số điện thoại';
	$fields['billing']['billing_email']['placeholder'] = 'Nhập địa chỉ email';
	return $fields;
}
add_filter('woocommerce_checkout_fields','checkout_fields',99);
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
   wp_safe_redirect( get_permalink( wc_get_page_id( 'shop' ) ) ); 
        exit;
    }
}
add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false' );
add_filter( 'woocommerce_checkout_update_order_review_expired', '__return_false' );




if ( ! function_exists( 'twentytwentytwo_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_support() {

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );

	}

endif;

add_action( 'after_setup_theme', 'twentytwentytwo_support' );

if ( ! function_exists( 'twentytwentytwo_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_styles() {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style(
			'twentytwentytwo-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'twentytwentytwo-style' );

	}

endif;

add_action( 'wp_enqueue_scripts', 'twentytwentytwo_styles' );

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';
