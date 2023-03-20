<?php
/**
 * Template Name: Home Page
 *
 **/
get_header();?>
	<div id="main-home-content" class="home-content home-page container" role="main">
		<?php
		// Start the Loop.
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
		?>
		<?php global $woocommerce; ?>
<a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>">
<?php
echo sprintf(_n('%d item', '%d sản phẩm', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?> - <?php echo $woocommerce->cart->get_cart_total();
?>
</a>
	</div><!-- #main-content -->
<?php get_footer();
