<?php
/**
 * Template Name: Home
 *
 **/
 get_header();?>
	<div id="main-home-content" class="home-content home-page container" role="main">
	    
		<?php if(wp_is_mobile()):?>
		<div class="row">
		<div class="container login-logout">
			<div class="log-fixed">
		    <?php if(!is_user_logged_in()): ?>
			<a href="<?php echo home_url();?>/dang-nhap" style="color:#000;" href="#"><i class="fa fa-user"></i> Đăng nhập</a>
			<?php  else : ?>
			<div class="user-logged"><a href="<?php echo home_url();?>/tai-khoan"><i class="fa fa-user"></i> +<span><?php echo do_shortcode('[idehweb_lwp_metas nicename="false" username="false" phone_number="true" email="false"]');?></span> | Quản lý đơn hàng</a></div>
			<?php endif;?>
		</div>
		</div>
		</div>
		<div class="row">
		    <div class="container header-wrap">
		<?php echo do_shortcode('[header_shortcode]');?>
		</div>
		</div>
		<div class="row">
			<div class="container">
			<div style="margin-bottom:2%;" class="list-info-order row">
			<a href="tel:0702077933"><div><i class="fa fa-phone"></i> Hotline/Zalo: 0702077933</div></a>	
				
			</div>
			</div>
		</div>
		<div class="row">
			<div class="container">
			<div class="list-info-order row">
			<a href="<?php echo home_url();?>/order-tracking/"><div><i class="fa fa-search"></i> Theo dõi tình trạng đơn hàng</div></a>	
			</div>
			</div>
		</div>
		<section id="header">
		<nav class="nav" id="main-nav">
		<span href="#header" class=""><i class="fa fa-bars"></i></span>
		<?php /* $args = array(
			'taxonomy'     => 'product_cat',
	  	 	'tax_query' => array(
        	array(
			'field'    => 'term_id',
            'terms'    => array(15,19),
            'operator' => 'NOT IN',
        )
    )
  );
 	$all_categories = get_categories( $args );
 	foreach ($all_categories as $cat) {
    if($cat->category_parent == 0) {
        $category_id = $cat->term_id;       
//        echo '<a href="'. get_term_link($cat->slug, 'product_cat') .'">'. $cat->name .'</a>';
		echo '<a href="'. get_term_link($cat->slug, 'product_cat') .'">'. $cat->name .'</a>';
    	}       
	} */
 ?>	
		<a href="#ca-phe-hat">Cà phê hạt</a>	
		<a style="display:none;" href="#ca-phe-bot">Cà phê bột</a>	
		<a href="#hat-ca-phe">Cà Phê Express</a>	
		<a href="#bot">Bột</a>
		<a href="#mut">Mứt</a>
		<a href="#tra">Trà</a>
		<a href="#qua-tang">Quà tặng</a>
		<a href="#nguyen-vat-lieu">Nguyên vật liệu</a>
		<a href="#cong-cu-dung-cu">Công cụ dụng cụ</a>
		<a href="#dong-phuc">Đồng phục</a>
  		</nav>
		</section>
		<section id="ca-phe-hat">
		<div class="row">
		<div class="title-menu container">CÀ PHÊ HẠT</div>
		</div>
		<div class="row">
		<div class="container">
		    <?php echo do_shortcode('[product_laha_shortcode cat="ca-phe-gia-si"]');?>
			<?php echo do_shortcode('[product_laha_shortcode cat="ca-phe-gia-le"]');?>
		</div>
		</div>
		
		</section>
		<section style="display:none;" id="ca-phe-bot">
		<div class="row">
		<div class="title-menu container">CÀ PHÊ BỘT</div>
		</div>
		<div class="row">
		<div class="container">
		    <?php // echo do_shortcode('[product_laha_shortcode cat="ca-phe-bot"]');?></div>
		</div>
		</section>
		<section id="hat-ca-phe">
		<div class="row">
		<div class="title-menu container">Cà Phê Express</div>
		</div>
		<div class="row">
		<div class="container">
		    <?php echo do_shortcode('[product_laha_shortcode cat="ca-phe-express"]');?></div>
		</div>
		</section>
		<section id="bot">
		<div class="row">
		<div class="title-menu container">BỘT</div>
		</div>
		<div class="row">
		<div class="container">
		    <?php echo do_shortcode('[product_laha_shortcode cat="bot"]');?></div>
		</div>
		</section>
		<section id="mut">
		<div class="row">
		<div class="title-menu container">MỨT</div>
		</div>
		<div class="row">
		<div class="container">
		    <?php echo do_shortcode('[product_laha_shortcode cat="mut"]');?></div>
		</div>
		</section>
		<section id="tra">
		<div class="row">
		<div class="title-menu container">TRÀ</div> 
		</div>
		<div class="row">
		<div class="container">
		    <?php echo do_shortcode('[product_laha_shortcode cat="tra-gia-le"]');?></div>
		</div>
		</section>
		<section id="qua-tang">
		<div class="row">
		<div class="title-menu container">QUÀ TẶNG</div> 
		</div>
		<div class="row">
		<div class="container">
		    <?php echo do_shortcode('[product_laha_shortcode cat="qua-tang"]');?></div>
		</div>
		</section>
		<section id="nguyen-vat-lieu">
		<div class="row">
		<div class="title-menu container">NGUYÊN VẬT LIỆU</div> 
		</div>
		<div class="row">
		<div class="container">
		    <?php echo do_shortcode('[product_laha_shortcode cat="nguyen-vat-lieu"]');?></div>
		</div>
		</section>
		<section id="cong-cu-dung-cu">
		<div class="row">
		<div class="title-menu container">CÔNG CỤ DỤNG CỤ</div> 
		</div>
		<div class="row">
		<div class="container">
		    <?php echo do_shortcode('[product_laha_shortcode cat="cong-cu-dung-cu"]');?></div>
		</div>
		</section>
		<section id="dong-phuc">
		<div class="row">
		<div class="title-menu container">ĐỒNG PHỤC</div> 
		</div>
		<div class="row">
		<div class="container">
		    <?php echo do_shortcode('[product_laha_shortcode cat="dong-phuc"]');?></div>
		</div>
		</section>
		<?php else: ?>
		<div>Mobile Access..</div>
		
		<?php endif;?>
		
		
		
		<?php if(wp_is_mobile()):?>
		<?php //if(WC()->cart->is_empty()): ?>
		<?php global $woocommerce; ?>
        <div class="cart-bottom"><a class="cart-contents" href="https://lahacafe.vn/banhang/thanh-toan/<?php //echo wc_get_checkout_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>">
            
        <?php echo sprintf(_n('%d sản phẩm', '%d sản phẩm', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?> - <?php echo $woocommerce->cart->get_cart_total();
        ?>
        <span class="txt-cart">Xem giỏ hàng</span>
        </a></div>
        <?php //  endif; ?>
        <?php endif; ?>
	</div><!-- #main-content -->
<?php  get_footer(); ?>
<script>
jQuery(document).ready(function(){
let caphehat = document.getElementById("ca-phe-hat");
let navbar = document.getElementById("main-nav");
let navPos = navbar.getBoundingClientRect().top;
let navbarLinks = document.querySelectorAll("section .nav a");

  window.addEventListener("scroll", e => {
  
  let scrollPos = window.scrollY;
  if (scrollPos > navPos) {
    navbar.classList.add('sticky');
    header.classList.add('navbarOffsetMargin');
  } else {
    navbar.classList.remove('sticky');
    header.classList.remove('navbarOffsetMargin');
  }
  
    navbarLinks.forEach(link => {
      let section = document.querySelector(link.hash);
      if (scrollPos + 150 > section.offsetTop && scrollPos + 150 < section.offsetTop + section.offsetHeight ) {
        link.classList.add("active");
      } else {
        link.classList.remove("active");
      }

    });
	var myScrollPos = jQuery('nav a.active').offset().left + jQuery('nav a.active').outerWidth(true)/2 + jQuery('.nav').scrollLeft() - jQuery('.nav').width()/5;
     //   console.log(myScrollPos);
        jQuery('.nav').scrollLeft(myScrollPos);
});
});
	
</script>
<script> 
document.addEventListener("DOMContentLoaded", function(event) { 
            var scrollpos = localStorage.getItem('scrollpos');
            if (scrollpos) window.scrollTo(0, scrollpos);
			
        });

      //  window.onbeforeunload = function(e) {
       //     localStorage.setItem('scrollpos', window.scrollY);
       // };
			window.onpagehide = function(e) {
            localStorage.setItem('scrollpos', window.scrollY);
			
        };

</script>
