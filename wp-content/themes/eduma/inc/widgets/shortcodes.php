<?php
include_once( THIM_DIR . '/inc/widgets/login-popup/login-popup.php' );
require_once THIM_DIR . 'inc/widgets/form-login-register.php';

add_filter( 'thim_register_shortcode', 'thim_register_elements' );

if ( ! function_exists( 'thim_register_elements' ) ) {
	/**
	 * @param $elements
	 *
	 * @return mixed
	 */
	function thim_register_elements() {

		// elements want to add
		$elements = array(
			'general'                      => array(
				'button',
				'accordion',
				'carousel-categories',
				'carousel-post',
				'countdown-box',
				'counters-box',
				'empty-space',
				'gallery-images',
				'gallery-posts',
				'google-map',
				'heading',
				'icon-box',
				'image-box',
				'landing-image',
				'link',
				'list-post',
				'login-form',
				'multiple-images',
				'single-images',
				'social',
				'tab',
				'testimonials',
				'timetable',
				'twitter',
				'video',
				'navigation-menu',
			),
			'LearnPress'                   => array(
				'course-categories',
				'courses',
				'courses-searching',
				'list-instructors',
				//				'one-course-instructors',
			),
			'LP_Co_Instructor_Preload'     => array(
				'one-course-instructors',
			),
			'LP_Addon_Collections_Preload' => array(
				'courses-collection',
			),
			'THIM_Our_Team'                => array(
				'our-team',
			),
			'Thim_Portfolio'               => array(
				'portfolio',
			),
			'WPEMS'                        => array(
				'tab-event',
				'list-event',
			),
		);

		return $elements;
	}
}

add_filter( 'thim_shortcode_group_name', 'thim_shortcode_group_name' );

if ( ! function_exists( 'thim_shortcode_group_name' ) ) {
	function thim_shortcode_group_name() {
		return esc_html__( 'Thim Shortcodes', 'eduma' );
	}
}
// change folder shortcodes to widgets
if ( ! function_exists( 'thim_custom_folder_shortcodes' ) ) {
	function thim_custom_folder_shortcodes() {
		return 'widgets';
	}
}
add_filter( 'thim_custom_folder_shortcodes', 'thim_custom_folder_shortcodes' );
// don't support folder groups
add_filter( 'thim_support_folder_groups', '__return_false' );

add_filter( 'thim_ekit/elementor/widgets/list', 'register_list_shortcode' );
function register_list_shortcode( $widgets ) {
	$data = array(

		'button',
		'accordion',
		'carousel-categories',
		'carousel-post',
		'countdown-box',
		'counters-box',
		'empty-space',
		'gallery-images',
		'gallery-posts',
		// 'google-map',
		'heading',
		'icon-box',
		'image-box',
		'landing-image',
		'link',
		'list-post',
		'login-form',
		'login-popup',
		'multiple-images',
		'single-images',
		// 'social',
		'tab',
		'testimonials',
		'timetable',
		'twitter',
		'video',
		'navigation-menu',
 		'page-title'
	);

	if ( class_exists( 'LearnPress' ) ) {
		$data = array_merge( $data, array(
				'course-categories',
				'courses',
				'courses-searching',
				'list-instructors',
			)
		);
	}
	if ( class_exists( 'LP_Co_Instructor' ) ) {
		$data = array_merge( $data, array(
			'one-course-instructors',
		) );
	}
	if ( class_exists( 'LP_Addon_Collections' ) ) {
 		$data = array_merge( $data, array(
			'courses-collection',
		) );
	}
	if ( class_exists( 'THIM_Our_Team' ) ) {
		$data = array_merge( $data, array(
			'our-team',
		) );
	}
	if ( class_exists( 'Thim_Portfolio' ) ) {
		$data = array_merge( $data, array(
			'portfolio',
		) );
	}
	if ( class_exists( 'WPEMS' ) ) {
		$data = array_merge( $data, array(
			'tab-event',
			'list-event',
		) );
	}

	return array_merge( $widgets, array( 'eduma' => $data ) );
}

if ( ! function_exists( 'thim_ekit_get_widget_template' ) ) {
	function thim_ekit_get_widget_template( $widget_base, $args = array(), $template_name = 'base' ) {
		if ( is_array( $args ) && isset( $args ) ) {
			extract( $args );
		}

		if ( false === strpos( $template_name, '.php' ) ) {
			$template_name .= '.php';
		}

		$parent_path = get_template_directory() . '/inc/widgets/' . $widget_base . '/tpl/' . $template_name;
		$child_path  = get_stylesheet_directory() . '/inc/widgets/' . $widget_base . '/' . $template_name;

		if ( file_exists( $child_path ) ) {
			$template_path = $child_path;
		} elseif ( file_exists( $parent_path ) ) {
			$template_path = $parent_path;
		} else {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_name ), '1.0.0' );

			return;
		}

		require $template_path;
	}
}

add_action( 'elementor/widgets/widgets_registered', function ( $widgets_manager ) {
	$elements = array(
		'button',
		'accordion',
		'carousel-categories',
		'carousel-post',
		'countdown-box',
		'counters-box',
		'empty-space',
		'gallery-images',
		'gallery-posts',
		'google-map',
		'heading',
		'icon-box',
		'image-box',
		'landing-image',
		'link',
		'list-post',
		'login-form',
		//'login-popup',
		'multiple-images',
		'single-images',
		'social',
		'tab',
		'testimonials',
		'timetable',
		'twitter',
		'video',
		'navigation-menu',

		'course-categories',
		'courses',
		//			'courses-searching',
		'list-instructors',
		'one-course-instructors',
		'courses-collection',
		'our-team',
		'portfolio',
		'tab-event',
		'list-event'
	);
	// disable elements when depends plugin not active

	foreach ( $elements as $element ) {
		$widgets_manager->unregister_widget_type( 'wp-widget-' . $element );
	}
}, 200 );

// disable mega menu in thim-core
if ( class_exists( 'Thim_EL_Kit' ) ) {
	$ekits_module_settings = get_option( 'thim_ekits_module_settings' );
	if ( ( ! $ekits_module_settings || ! empty( $ekits_module_settings['megamenu'] ) ) ) {
		add_filter( 'thim-support-mega-menu', '__return_false' );
	}
}

// Template kit footer and header
/*
thim_above_footer_area_fnc
*/
add_action('thim_ekit/header_footer/template/before_footer','thim_above_footer_area_fnc');
/*
thim_footer_bottom
*/
add_action('thim_ekit/header_footer/template/after_footer',function() { echo '</div>'; }, 1 );
add_action('thim_ekit/header_footer/template/after_footer','thim_footer_bottom', 5 );
add_action('thim_ekit/header_footer/template/after_footer',function() { echo '</div></div>'; }, 10 );
/*--
thim_print_preload
*/
add_action('thim_ekit/header_footer/template/before_header', 'thim_print_preload', 5);
add_action('thim_ekit/header_footer/template/before_header',function() { echo '<div id="wrapper-container" class="wrapper-container"><div class="content-pusher">'; } , 10);
add_action('thim_ekit/header_footer/template/after_header',function() { echo '<div id="main-content">'; } , 5);


function thim_review_meta_data_widget_course($opt ){
	if (  class_exists( 'LP_Addon_Course_Review' )){
		$opt['review_course'] 		= esc_html__( 'Review', 'eduma' );
	}

 	return $opt;
}
add_filter ('learn-thim-kits-lp-meta-data','thim_review_meta_data_widget_course',100);
 
add_filter( 'thim-kits-extral-meta-data', 'thim_kits_meta_data_course_ratting', 100, 3);
function thim_kits_meta_data_course_ratting( $string, $meta_data, $settings){
	if ( class_exists( 'LP_Addon_Course_Review' ) && in_array( 'review_course', $meta_data )){
		$course_rate  = learn_press_get_course_rate(get_the_ID() );
		?>
		<span class="course-review">
			 <?php thim_print_rating( $course_rate ); ?>
		</span>
		<?php
	}
   
	return $string;
}