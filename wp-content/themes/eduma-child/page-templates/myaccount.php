<?php
/**
 * Template Name: Account Page
 *
 **/
get_header();?>
<div id="main-account-content" class="account-page container" role="main">
	<a href="<?php echo home_url();?>"><div class="head-title"><i class="fa fa-long-arrow-left"></i> <span>Tài khoản</span></div></a>
		<?php global $woocommerce; ?>
		<div class="myaccount-page">
		    <i class="fa fa-user"></i>
		    <div>Xin chào,</div>
		    <?php if(!is_user_logged_in()): ?>
			<a href="<?php echo home_url();?>/dang-nhap" style="color:#000;" href="#"><i class="fa fa-user"></i> Đăng nhập</a>
			<?php  else : ?>
			<div class="user-logged"><?php 
			$current_user = wp_get_current_user(); 
			echo $current_user->user_lastname; ?></span></div>
			<div class="user-logged"><a href="<?php echo home_url();?>/tai-khoan"> +<span><?php echo do_shortcode('[idehweb_lwp_metas nicename="false" username="false" phone_number="true" email="false"]');?></span></a></div>
			<?php endif;?>
			
			<a style="color:#000;" href="<?php echo wp_logout_url(home_url());?>"><div class="myaccount-exit">ĐĂNG XUẤT</div></a>
			
			
		</div>
        <?php echo do_shortcode('[woocommerce_my_account]');?>
	</div><!-- #main-content -->
<?php get_footer();?>

