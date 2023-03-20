<?php
/**
 * Template for displaying course content within the loop.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/content-course.php
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$user = LP_Global::user();

$theme_options_data = get_theme_mods();
 
$class = isset( $theme_options_data['thim_learnpress_cate_grid_column'] ) && $theme_options_data['thim_learnpress_cate_grid_column'] ? 'course-grid-' . $theme_options_data['thim_learnpress_cate_grid_column'] : 'course-grid-3';

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$class .= ' lpr_course';

$thim_show_course_meta = apply_filters( 'thim_show_course_meta', true );
if ( class_exists( 'LP_Addon_Coming_Soon_Courses' ) ) {
	$instance_addon = LP_Addon_Coming_Soon_Courses::instance();
	if ( $instance_addon->is_coming_soon( get_the_ID() ) ) {
		$thim_show_course_meta = false;
	}
}

?>

<div id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>

	<?php
	// @deprecated
	do_action( 'learn_press_before_courses_loop_item' );
	?>

	<div class="course-item">

		<?php
		// @since 4.0.0
		//do_action( 'learn-press/before-courses-loop-item' );
		?>

		<?php
		// @thim
		do_action( 'thim_courses_loop_item_thumb' );
		?>

		<div class="thim-course-content">
			<?php
 			/**
			 * @hooked thim_learnpress_loop_item_title - 10
			 * @hooked learn_press_courses_loop_item_instructor - 5
			 */
			do_action( 'learnpress_loop_item_title' );
 
			do_action( 'learnpress_loop_item_desc' );
			?>

			<?php
			if ( $thim_show_course_meta ) {
				do_action( 'learnpress_loop_item_course_meta' );
			} else {
				echo '<div class="message message-warning learn-press-message coming-soon-message">' . esc_html__( 'Coming soon', 'eduma' ) . '</div>';
			}
 
			?>
		</div>

		<?php
		// @since 4.0.0
		//do_action( 'learn-press/after-courses-loop-item' );
		?>

	</div>

	<?php
	// @deprecated
	do_action( 'learn_press_after_courses_loop_item' );
	?>

</div>
