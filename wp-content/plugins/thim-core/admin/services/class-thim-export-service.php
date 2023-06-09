<?php

/**
 * Class Thim_Export_Service.
 *
 * @since 0.5.0
 */
class Thim_Export_Service {
	/**
	 * Send file download.
	 *
	 * @param        $content
	 * @param string $file_name
	 * @param string $type
	 *
	 * @since 0.5.0
	 *
	 */
	private static function _send_download_file( $content, $file_name = 'test.dat', $type = 'text/plain' ) {
		if ( headers_sent() ) {
			wp_die( __( 'Something went wrong', 'thim-core' ) );
		}

		header( "Content-type: $type" );
		header( "Content-Disposition: attachment; filename=$file_name" );

		echo $content;
		die();
	}

	/**
	 * Export file settings.dat
	 *
	 * @since 0.5.0
	 */
	public static function settings() {
		$options = array();

		/**
		 * Export basic settings.
		 */
		$basic_settings = Thim_Importer_Service::get_key_basic_settings();
		foreach ( $basic_settings as $basic_setting ) {
 			$options[$basic_setting] = get_option( $basic_setting );
   		}
		/**
		 * Convert page id settings to page slug settings.
		 */
		$settings_key = Thim_Importer_Service::get_key_page_id_settings();
		foreach ( $settings_key as $key ) {
			$page_id = get_option( $key );
			if ( ! empty( $page_id ) ) {
				$path = get_page_uri( $page_id );

				if ( ! empty( $path ) ) {
					$options[$key] = $path;
				}
			}
		}
		 
		//tuanta export thim elementor kit mega menu options
		if( apply_filters('thim_importer_thim_enable_mega_menu', false )){
			$menu_primary_id = get_theme_mod('nav_menu_locations');
			$options['thim_enable_mega_menu'] = get_term_meta( $menu_primary_id['primary'], 'thim_enable_mega_menu', true);
 		}
		$text = maybe_serialize( $options );
		// $text = $options ;
		self::_send_download_file( $text, 'settings.dat', 'text/plain' );
	}

	/**
	 * Export file settings.dat
	 *
	 * @since 0.5.0
	 */
	public static function widgets() {
		$sidebars_array = get_option( 'sidebars_widgets' );
		$sidebar_export = $posted_array = array();
		foreach ( $sidebars_array as $sidebar => $widgets ) {
			if ( $sidebar == 'wp_inactive_widgets' ) {
				continue;
			}
			if ( ! empty( $widgets ) && is_array( $widgets ) ) {
				foreach ( $widgets as $sidebar_widget ) {
					$sidebar_export[$sidebar][] = $sidebar_widget;
					$posted_array[]             = $sidebar_widget;
				}
			}
		}

		$widgets = array();
		foreach ( $posted_array as $k => $v ) {
			$widget['type']       = trim( substr( $v, 0, strrpos( $v, '-' ) ) );
			$widget['type-index'] = trim( substr( $v, strrpos( $v, '-' ) + 1 ) );
			$widgets[]            = $widget;
		}

		$widgets_array = array();
		foreach ( $widgets as $widget ) {
			$widget_val                                            = get_option( 'widget_' . $widget['type'] );
			$widget_val                                            = apply_filters( 'widget_data_export', $widget_val, $widget['type'] );
			$multiwidget_val                                       = $widget_val['_multiwidget'];
			$widgets_array[$widget['type']][$widget['type-index']] = $widget_val[$widget['type-index']];
			if ( isset( $widgets_array[$widget['type']]['_multiwidget'] ) ) {
				unset( $widgets_array[$widget['type']]['_multiwidget'] );
			}

			$widgets_array[$widget['type']]['_multiwidget'] = $multiwidget_val;
		}
		unset( $widgets_array['export'] );
		$export_array = array( $sidebar_export, $widgets_array );

		$text = json_encode( $export_array );

		self::_send_download_file( $text, 'widget_data.json', 'text/plain' );
	}

	/**
	 * Export file theme options
	 *
	 * @since 0.5.0
	 */
	public static function theme_options() {
		$theme_options = get_theme_mods();
		 
 		$text          = maybe_serialize( $theme_options );
		self::_send_download_file( $text, 'theme_options.dat', 'text/plain' );
	}

	/**
	 * Export content (xml)
	 *
	 * @since 1.0.0
	 */
	public static function content() {
		$url = admin_url( 'export.php?download=true&content=all&cat=0&post_author=0&post_start_date=0&post_end_date=0&post_status=publish&page_author=0&page_start_date=0&page_end_date=0&page_status=publish&attachment_start_date=0&attachment_end_date=0&thim_export=1' );

		thim_core_redirect( $url );
	}

	/**
	 * Show php information.
	 *
	 * @since 0.8.9
	 */
	public static function php_info() {
		phpinfo();
		exit();
	}

	/**
	 * Export system status.
	 *
	 * @since 1.2.0
	 */
	public static function system_status() {
		$text = Thim_System_Status::get_draw_system_status();

		self::_send_download_file( $text, 'system_status.txt', 'text/plain' );
	}
}