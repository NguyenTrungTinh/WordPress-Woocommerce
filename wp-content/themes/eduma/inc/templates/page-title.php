<?php
/**
 * Get ThemeOptions
 * @return array|mixed|void
 */

 function eduma_get_theme_mods( $name = '', $value_default = '' ) {
	$data = get_theme_mods();
	if ( isset( $data[$name] ) ) {
		return $data[$name];
	} else {
		return $value_default;
	}
}

add_action( 'thim_wrapper_loop_start', 'thim_wapper_page_title', 5 );
if ( ! function_exists( 'thim_wapper_page_title' ) ) :
	function thim_wapper_page_title() {
		global $wp_query, $post;
		$GLOBALS['post'] = @$wp_query->post;
		$wp_query->setup_postdata( @$wp_query->post );

		$theme_options_data = get_theme_mods();

		$custom_title        = $subtitle = $style_heading = $thim_custom_heading = $front_title = $text_color = $sub_color = $bg_color = $cate_top_image_src = $bg_opacity = $top_overlay_style = $style_content = $bg_color_overlay ='';
		$style_heading_title = 'style_heading_1';
		$hide_title          = $hide_breadcrumbs = 0;
		if ( is_single() ) {
			$typography = 'h2';
		} else {
			$typography = 'h1';
		}
		// color theme options
		$cat_obj = $wp_query->get_queried_object();

		if ( isset( $cat_obj->term_id ) ) {
			$cat_ID       = $cat_obj->term_id;
			$cat_taxonomy = $cat_obj->taxonomy;
		} else {
			$cat_ID       = '';
			$cat_taxonomy = '';
		}

		//Get $prefix
		$prefix = thim_get_prefix_page_title();

		//Get $prefix_inner
		$prefix_inner = thim_get_prefix_inner_page_title();

		//Background image default from customizer options
		if ( ! empty( $theme_options_data[$prefix . $prefix_inner . 'top_image'] ) ) {
			$cate_top_image = $theme_options_data[$prefix . $prefix_inner . 'top_image'];
			if ( is_numeric( $cate_top_image ) ) {
				$cate_top_attachment = wp_get_attachment_image_src( $cate_top_image, 'full' );
				$cate_top_image_src  = $cate_top_attachment[0];
			} else {
				$cate_top_image_src = $cate_top_image;
			}
		}

		//Hide breadcrumbs default from customizer options
		if ( ! empty( $theme_options_data[$prefix . $prefix_inner . 'hide_breadcrumbs'] ) ) {
			$hide_breadcrumbs = $theme_options_data[$prefix . $prefix_inner . 'hide_breadcrumbs'];
		}

		//Hide title default from customizer options
		if ( ! empty( $theme_options_data[$prefix . $prefix_inner . 'hide_title'] ) ) {
			$hide_title = $theme_options_data[$prefix . $prefix_inner . 'hide_title'];
		}

		//Get subtitle default from customizer options
		if ( ! empty( $theme_options_data[$prefix . $prefix_inner . 'sub_title'] ) ) {
			$subtitle = $theme_options_data[$prefix . $prefix_inner . 'sub_title'];
		}

		if ( ! empty( $theme_options_data[$prefix . $prefix_inner . 'title_color'] ) ) {
			$text_color = $theme_options_data[$prefix . $prefix_inner . 'title_color'];
		}

		if ( ! empty( $theme_options_data[$prefix . $prefix_inner . 'sub_title_color'] ) ) {
			$sub_color = $theme_options_data[$prefix . $prefix_inner . 'sub_title_color'];
		}

		if ( ! empty( $theme_options_data[$prefix . $prefix_inner . 'bg_color'] ) ) {
			$bg_color = $theme_options_data[$prefix . $prefix_inner . 'bg_color'];
		}

		if ( ! empty( $theme_options_data[$prefix . $prefix_inner . 'style_heading_title'] ) ) {
			$style_heading_title = $theme_options_data[$prefix . $prefix_inner . 'style_heading_title'];
		}

		if ( get_post_type() == 'forum' || get_post_type() == 'topic' ) {
			if ( ! empty( $theme_options_data['thim_forum_cate_top_image'] ) ) {
				$cate_top_image = $theme_options_data['thim_forum_cate_top_image'];
				if ( is_numeric( $cate_top_image ) ) {
					$cate_top_attachment = wp_get_attachment_image_src( $cate_top_image, 'full' );
					$cate_top_image_src  = $cate_top_attachment[0];
				} else {
					$cate_top_image_src = $cate_top_image;
				}
			}
		}


		//Get by Tax-meta-class for categories & custom field for single
		if ( is_page() || is_single() ) {
			if ( get_post_type() == 'forum' || get_post_type() == 'topic' ) {
				$hide_title       = isset( $theme_options_data['thim_forum_cate_hide_title'] ) ? $theme_options_data['thim_forum_cate_hide_title'] : 0;
				$hide_breadcrumbs = isset( $theme_options_data['thim_forum_cate_hide_breadcrumbs'] ) ? $theme_options_data['thim_forum_cate_hide_title'] : 0;
			}
			$post_id = get_the_ID();
			if ( class_exists( 'BuddyPress' ) ) {
				$bp = buddypress();
				if ( $bp->current_component ) {
					$page_array = get_option( 'bp-pages' );
					if ( isset( $page_array[$bp->current_component] ) ) {
						$post_id = $page_array[$bp->current_component];
					}
				}
			}
			//Check using custom heading on single
			$using_custom_heading = get_post_meta( $post_id, 'thim_mtb_using_custom_heading', true );

			if ( $using_custom_heading ) {

				$hide_title       = get_post_meta( $post_id, 'thim_mtb_hide_title_and_subtitle', true );
				$hide_breadcrumbs = get_post_meta( $post_id, 'thim_mtb_hide_breadcrumbs', true );
				$custom_title     = get_post_meta( $post_id, 'thim_mtb_custom_title', true );
				$subtitle         = get_post_meta( $post_id, 'thim_subtitle', true );
				$bg_opacity       = get_post_meta( $post_id, 'thim_mtb_bg_opacity', true );

				$text_color_single = get_post_meta( $post_id, 'thim_mtb_text_color', true );
				if ( ! empty( $text_color_single ) && $text_color_single != '#' ) {
					$text_color = $text_color_single;
				}
				$sub_color_single = get_post_meta( $post_id, 'thim_mtb_color_sub_title', true );
				if ( ! empty( $sub_color_single ) && $sub_color_single != '#' ) {
					$sub_color = $sub_color_single;
				}
				$bg_color_single = get_post_meta( $post_id, 'thim_mtb_bg_color', true );
				if ( ! empty( $bg_color_single ) && $bg_color_single != '#' ) {
					$bg_color = $bg_color_single;
				}
				$cate_top_image = get_post_meta( $post_id, 'thim_mtb_top_image', true );
				if ( is_numeric( $cate_top_image ) ) {
					$post_page_top_attachment = wp_get_attachment_image_src( $cate_top_image, 'full' );
					$cate_top_image_src       = $post_page_top_attachment[0];
				}
			}
		} else if ( is_404() ) {
			if ( ! empty( $theme_options_data['thim_single_404_sub_title'] ) ) {
				$subtitle = $theme_options_data['thim_single_404_sub_title'];
			}
			if ( ! empty( $theme_options_data['thim_single_404_title_color'] ) ) {
				$text_color = $theme_options_data['thim_single_404_title_color'];
			}
			if ( ! empty( $theme_options_data['thim_single_404_sub_title_color'] ) ) {
				$sub_color = $theme_options_data['thim_single_404_sub_title_color'];
			}
			if ( ! empty( $theme_options_data['thim_single_404_bg_color'] ) ) {
				$bg_color = $theme_options_data['thim_single_404_bg_color'];
			}
			if ( ! empty( $theme_options_data['thim_single_404_top_image'] ) ) {
				$cate_top_image_src = $theme_options_data['thim_single_404_top_image'];
			}
		} else {
			$thim_custom_heading = get_term_meta( $cat_ID, 'thim_custom_heading', true );
			if ( $thim_custom_heading == 'custom' || $thim_custom_heading == 'on' ) {
				$text_color_cate = get_term_meta( $cat_ID, $prefix . '_cate_heading_text_color', true );
				$bg_color_cate   = get_term_meta( $cat_ID, $prefix . '_cate_heading_bg_color', true );
				$sub_color_cate  = get_term_meta( $cat_ID, $prefix . '_cate_sub_heading_bg_color', true );
				// reset default
				if ( ! empty( $text_color_cate ) && $text_color_cate != '#' ) {
					$text_color = $text_color_cate;
				}
				if ( ! empty( $bg_color_cate ) && $bg_color_cate != '#' ) {
					$bg_color = $bg_color_cate;
				}
				if ( ! empty( $sub_color_cate ) && $sub_color_cate != '#' ) {
					$sub_color = $sub_color_cate;
				}

				$subtitle = term_description( $cat_ID, $cat_taxonomy );

				$hide_breadcrumbs = get_term_meta( $cat_ID, $prefix . '_cate_hide_breadcrumbs', true );
				$hide_title       = get_term_meta( $cat_ID, $prefix . '_cate_hide_title', true );
				$cate_top_image   = get_term_meta( $cat_ID, $prefix . '_top_image', true );
				$bg_opacity       = get_term_meta( $cat_ID, $prefix . '_cate_heading_bg_opacity', true );

				if ( ! empty( $cate_top_image ) ) {
					$cate_top_image_src = $cate_top_image['url'];
				}
			}
		}

		//Check ssl for top image
		$cate_top_image_src = thim_ssl_secure_url( $cate_top_image_src );

		// css
		$top_site_main_style = ( $text_color != '' ) ? 'color: ' . $text_color . ';' : '';
	
		$sub_title_style = ( $sub_color != '' ) ? 'style="color:' . $sub_color . '"' : '';
		if ( ! empty( $bg_color ) ) {
			$top_overlay_style = 'background-color:' . $bg_color . ';';
 		}
		if ( ! empty( $bg_opacity ) ) {
			$top_overlay_style .= 'opacity:' . $bg_opacity . ';';
		}

		if ( is_front_page() || is_home() ) {
			if ( ! empty( $theme_options_data['thim_front_page_hide_breadcrumbs'] ) ) {
				$hide_breadcrumbs = '1';
			}
			if ( ! empty( $theme_options_data['thim_front_page_hide_title'] ) ) {
				$hide_title = '1';
			}
		}
		$top_site_main       = $style_h_3 = false;
		

		//set style heading title
		//thim_top_heading
		$top_heading_style = $no_padding ='';
		if(isset($theme_options_data['thim_top_heading'])){
			$top_heading_style = $theme_options_data['thim_top_heading'];
		}
		//
		if( isset( $_GET['breacrumb'] ) && ( $_GET['breacrumb'] === 'style-2' ) ) {
			$top_heading_style = 'style_2';
		}elseif( isset( $_GET['breacrumb'] ) && ( $_GET['breacrumb'] === 'style-3' ) ) {
			$top_heading_style = 'style_3';
		}
		// old options for course
		if($top_heading_style == '' && ! empty( thim_lp_style_single_course() )){
 			if ( thim_lp_style_single_course() == 'new-1' ) {
			   $top_heading_style ='style_2';
			  
			}elseif( thim_lp_style_single_course() == 'layout_style_2' ) {
			   $top_heading_style ='style_3'; 
			   $bg_color_overlay .= '--thim-padding-top-content:0px;';
			}
 		}
		 
	 	//
 		if ( $top_heading_style == 'style_2' ) {
			$top_overlay_style = '';
			 // fix if thim breacrumn color not config
			if(!isset($theme_options_data['thim_breacrumb_color'] )){
				$bg_color_overlay .= '--thim-breacrumb-color: #ccc;';
			}
			
			  $style_heading     = ' style_heading_2';
			if (thim_lp_style_single_course() == 'new-1' ) {
				$bg_color_overlay .= '--thim-courses-offset-top: -220px;';
				if ( is_single() && get_post_type() == 'lp_course' ) {
					$style_content = ' style_content_2';
					$typography = 'h1';
					$custom_title = get_the_title();
				}
			}
			
		} elseif ( $top_heading_style == 'style_3' ) {
			$style_heading = ' style_heading_3';
			
			if (isset($theme_options_data['thim_padding_top_content'] )) {
				$bg_color_overlay .= '--thim-padding-top-content:'.$theme_options_data['thim_padding_top_content'].'px;';
			}

			if ( ! empty( $bg_color ) ) {
				if ( ! empty( $bg_opacity ) ) {
					$bg_color_overlay .= '--thim-overlay-top-header-opacity:' . $bg_opacity . ';'; 
				 }
				 if(isset($theme_options_data['thim_top_bg_gradient'] ) && $theme_options_data['thim_top_bg_gradient'] == false){
					$bg_color_overlay .= '--thim-overlay-top-header-color-bottom:' . $bg_color . ';';
				}
				 $bg_color_overlay .= '--thim-overlay-top-header-color:' . $bg_color . ';'; 
			  }
			// color breacrumn style 2 like color title   
			$bg_color_overlay .= ! empty( $text_color ) ? '--thim-breacrumb-color: '.$text_color.';' :'';
			$bg_color_overlay .= isset($theme_options_data['thim_image_offset_bottom']) ? '--thim-offset-image-bottom:'.$theme_options_data['thim_image_offset_bottom'].'px' : '';
			 
			$top_overlay_style =  '';
			$top_overlay_style .= ( $cate_top_image_src != '' ) ? 'background-image:url(' . $cate_top_image_src . ');' : '';
			// clear background image
			$cate_top_image_src =  '';
			$style_h_3         = true;
		}elseif($top_heading_style == 'normal' &&  get_theme_mod('thim_top_heading_line_title', true) == false){
			$style_content = ' no-line-title';
		}	
		echo ( $bg_color_overlay != '' ) ? '<style>.content-area{'.$bg_color_overlay.'}</style>' : '';

		$top_site_main_style .= ( $cate_top_image_src != '' ) ? 'background-image:url(' . $cate_top_image_src . ');' : '';
	 
		?>

		<div class="top_heading<?php echo $style_heading; ?>_out<?php echo $style_content; ?>">
			<?php if ( get_theme_mod( 'thim_header_position', 'header_overlay' ) != 'header_default' || $hide_title != '1' || $style_h_3 ) {
				$top_site_main = true;
				echo '<div class="top_site_main' . $style_heading . '" style="' . ent2ncr( $top_site_main_style ) . '">';
				echo '<span class="overlay-top-header" style="' . ent2ncr( $top_overlay_style ) . '"></span>';
			}
			if ( $hide_title != '1' || $style_h_3 ) { ?>
				<div class="page-title-wrapper">
					<div class="banner-wrapper container">
						<?php
 						$heading_title = thim_get_page_title( $custom_title, $front_title );
 						echo '<' . $typography . ' class="page-title">' . $heading_title . '</' . $typography . '>';

						if ( ! empty( $subtitle ) ) {
							echo '<div class="banner-description" ' . $sub_title_style . '>' . $subtitle . '</div>';
						}
 
						if ( $style_h_3 && $hide_breadcrumbs != '1' && ! is_front_page() && ! is_404() ) {
							thim_print_breadcrumbs();
						}
						?>
					</div>
				</div>
			<?php }
			if ( $top_site_main ) {
				echo '</div>';
			}
			//Display breadcrumbs
			if ( ! $style_h_3 && $hide_breadcrumbs != '1' && ! is_front_page() && ! is_404() ) {
				thim_print_breadcrumbs();
			}
			?>
		</div>
		<?php
	}
endif;
?>