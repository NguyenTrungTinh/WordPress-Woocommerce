<?php

require THIM_DIR . 'inc/admin/require-thim-core.php';

if ( class_exists( 'TP' ) ) {
	require THIM_DIR . 'inc/admin/plugins-require.php';
	require THIM_DIR . 'inc/admin/customizer-options.php';
	require THIM_DIR . 'inc/widgets/shortcodes.php';
	require_once THIM_DIR . 'inc/widgets/class-extend-icons.php';
}


require THIM_DIR . 'inc/libs/Tax-meta-class/Tax-meta-class.php';
require THIM_DIR . 'inc/tax-meta.php';

/**
 * @param $tabs
 *
 * @return array
 */
function thim_widget_group( $tabs ) {
	$tabs[] = array(
		'title'  => esc_html__( 'Thim Widget', 'eduma' ),
		'filter' => array(
			'groups' => array( 'thim_widget_group' )
		)
	);

	return $tabs;
}

/**
 * Compile Sass from theme customize.
 */
// add_filter( 'thim_core_config_sass', 'thim_theme_options_sass' );

function thim_theme_options_sass() {
	$dir = THIM_DIR . 'assets/sass/';

	return array(
		'dir'  => $dir,
		'name' => '_style-options.scss',
	);
}

//Filter meta-box
add_filter( 'thim_metabox_display_settings', 'thim_add_metabox_settings', 100, 2 );
if ( ! function_exists( 'thim_add_metabox_settings' ) ) {

	function thim_add_metabox_settings( $meta_box, $prefix ) {
		if ( defined( 'THIM_CORE_VERSION' ) && version_compare( THIM_CORE_VERSION, '1.0.3', '>' ) ) {
			if ( isset( $_GET['post'] ) ) {
				if ( $_GET['post'] == get_option( 'page_on_front' ) || $_GET['post'] == get_option( 'page_for_posts' ) ) {
					return false;
				}
			}
		}

		$meta_box['post_types'] = array( 'page', 'post', 'lp_course', 'our_team', 'testimonials', 'product', 'tp_event', 'portfolio' );


		$prefix                      = 'thim_mtb_';
		$meta_box['tabs']['related'] = array(
			'label' => __( 'Related posts', 'eduma' ),
		);
		$meta_box['tabs']            = array(
			'title'  => array(
				'label' => __( 'Featured Title Area', 'eduma' ),
				'icon'  => 'dashicons-admin-appearance',
			),
			'layout' => array(
				'label' => __( 'Layout', 'eduma' ),
				'icon'  => 'dashicons-align-left',
			),
		);

		$meta_box['fields'] = array(
			/**
			 * Custom Title and Subtitle.
			 */
			array(
				'name' => __( 'Custom Title and Subtitle', 'thim-core' ),
				'id'   => $prefix . 'using_custom_heading',
				'type' => 'checkbox',
				'std'  => false,
				'tab'  => 'title',
			),
			array(
				'name'   => __( 'Hide Title and Subtitle', 'thim-core' ),
				'id'     => $prefix . 'hide_title_and_subtitle',
				'type'   => 'checkbox',
				'std'    => false,
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name'   => __( 'Custom Title', 'thim-core' ),
				'id'     => $prefix . 'custom_title',
				'type'   => 'text',
				'desc'   => __( 'Leave empty to use post title', 'thim-core' ),
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name'   => __( 'Color Title', 'thim-core' ),
				'id'     => $prefix . 'text_color',
				'type'   => 'color',
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name'   => __( 'Subtitle', 'thim-core' ),
				'id'     => 'thim_subtitle',
				'type'   => 'text',
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name'   => __( 'Color Subtitle', 'thim-core' ),
				'id'     => $prefix . 'color_sub_title',
				'type'   => 'color',
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name'   => __( 'Hide Breadcrumbs', 'thim-core' ),
				'id'     => $prefix . 'hide_breadcrumbs',
				'type'   => 'checkbox',
				'std'    => false,
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),

			array(
				'name'             => __( 'Background Image', 'thim-core' ),
				'id'               => $prefix . 'top_image',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
				'tab'              => 'title',
				'hidden'           => array( $prefix . 'using_custom_heading', '!=', true ),
			),
			array(
				'name'   => __( 'Background color', 'thim-core' ),
				'id'     => $prefix . 'bg_color',
				'type'   => 'color',
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name'   => __( 'Background color opacity', 'thim-core' ),
				'id'     => $prefix . 'bg_opacity',
				'type'   => 'number',
				'desc'	=> __( 'input color opacity: Ex: 0.1 ', 'thim-core' ),
				'std'    => 1,
				'step'   => '0.1',
				'min'    => 0,
				'max'    => 1,
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),

			/**
			 * Custom layout
			 */
			array(
				'name' => __( 'Use Custom Layout', 'thim-core' ),
				'id'   => $prefix . 'custom_layout',
				'type' => 'checkbox',
				'tab'  => 'layout',
				'std'  => false,
			),
			array(
				'name'    => __( 'Select Layout', 'thim-core' ),
				'id'      => $prefix . 'layout',
				'type'    => 'image_select',
				'options' => array(
					'sidebar-left'  => THIM_URI . 'images/layout/sidebar-left.jpg',
					'full-content'  => THIM_URI . 'images/layout/body-full.jpg',
					'sidebar-right' => THIM_URI . 'images/layout/sidebar-right.jpg',
					'full-width'    => THIM_URI . 'images/layout/content-full.jpg',
				),
				'default' => 'sidebar-right',
				'tab'     => 'layout',
				'hidden'  => array( $prefix . 'custom_layout', '=', false ),
			),
			array(
				'name' => __( 'No Padding Content', 'thim-core' ),
				'id'   => $prefix . 'no_padding',
				'type' => 'checkbox',
				'std'  => false,
				'tab'  => 'layout',
			),
		);

		return $meta_box;
	}
}


/**
 * List child themes.
 *
 * @return array
 */
function thim_eduma_list_child_themes() {
	return array(
		'eduma-child'              => array(
			'name'       => 'Eduma Child',
			'slug'       => 'eduma-child',
			'screenshot' => 'https://plugins.thimpress.com/downloads/images/eduma-child.png',
			'source'     => 'https://github.com/ThimPressWP/demo-data/raw/master/eduma/child-themes/eduma-child/eduma-child.zip',
			'version'    => '1.0.0'
		),
		// 'eduma-child-kindergarten' => array(
		// 	'name'       => 'Eduma Child Kindergarten',
		// 	'slug'       => 'eduma-child-kindergarten',
		// 	'screenshot' => 'https://plugins.thimpress.com/downloads/images/eduma-child-kindergarten.png',
		// 	'source'     => 'https://github.com/ThimPressWP/demo-data/raw/master/eduma/child-themes/eduma-child-kindergarten/eduma-child-kindergarten.zip',
		// 	'version'    => '1.1.0'
		// ),
		// 'eduma-child-udemy'        => array(
		// 	'name'       => 'Eduma Child New Education',
		// 	'slug'       => 'eduma-child-udemy',
		// 	'screenshot' => 'https://updates.thimpress.com/wp-content/uploads/2019/08/eduma-demo-edume.jpg',
		// 	'source'     => 'https://github.com/ThimPressWP/demo-data/raw/master/eduma/child-themes/eduma-child-udemy/eduma-child-udemy.zip',
		// 	'version'    => '1.0.0'
		// ),
		// 'eduma-child-instructor'   => array(
		// 	'name'       => 'Eduma Child New Instructor',
		// 	'slug'       => 'eduma-child-instructor',
		// 	'screenshot' => 'https://updates.thimpress.com/wp-content/uploads/2019/08/eduma-demo-instructor.jpg',
		// 	'source'     => 'https://github.com/ThimPressWP/demo-data/raw/master/eduma/child-themes/eduma-child-instructor/eduma-child-instructor.zip',
		// 	'version'    => '1.0.0'
		// ),

		// 'eduma-child-crypto'    => array(
		// 	'name'       => 'Eduma Child Crypto',
		// 	'slug'       => 'eduma-child-crypto',
		// 	'screenshot' => 'https://updates.thimpress.com/wp-content/uploads/2019/10/eduma-demo-crypto.jpg',
		// 	'source'     => 'https://github.com/ThimPressWP/demo-data/raw/master/eduma/child-themes/eduma-child-crypto/eduma-child-crypto.zip',
		// 	'version'    => '1.0.0'
		// ),
		// 'eduma-child-new-art'   => array(
		// 	'name'       => 'Eduma Child New Art',
		// 	'slug'       => 'eduma-child-new-art',
		// 	'screenshot' => 'https://updates.thimpress.com/wp-content/uploads/2019/10/eduma-demo-new-art.jpg',
		// 	'source'     => 'https://github.com/ThimPressWP/demo-data/raw/master/eduma/child-themes/eduma-child-new-art/eduma-child-new-art.zip',
		// 	'version'    => '1.0.0'
		// ),
		// 'eduma-child-kid-art'   => array(
		// 	'name'       => 'Eduma Child Kid Art',
		// 	'slug'       => 'eduma-child-kid-art',
		// 	'screenshot' => 'https://updates.thimpress.com/wp-content/uploads/2019/10/eduma-demo-kid-art.jpg',
		// 	'source'     => 'https://github.com/ThimPressWP/demo-data/raw/master/eduma/child-themes/eduma-child-kid-art/eduma-child-kid-art.zip',
		// 	'version'    => '1.0.0'
		// ),
		// 'eduma-child-tech-camp' => array(
		// 	'name'       => 'Eduma Child Tech Camp',
		// 	'slug'       => 'eduma-child-tech-camps',
		// 	'screenshot' => 'https://updates.thimpress.com/wp-content/uploads/2019/10/eduma-demo-tech-camp.jpg',
		// 	'source'     => 'https://github.com/ThimPressWP/demo-data/raw/master/eduma/child-themes/eduma-child-tech-camp/eduma-child-tech-camps.zip',
		// 	'version'    => '1.0.0'
		// ),
	);
}

add_filter( 'thim_core_list_child_themes', 'thim_eduma_list_child_themes' );
