<?php
/*
Plugin Name: WPC Grouped Product for WooCommerce
Plugin URI: https://wpclever.net/
Description: WPC Grouped Product helps you made up standalone products that are presented as a group.
Version: 3.2.5
Author: WPClever
Author URI: https://wpclever.net
Text Domain: wpc-grouped-product
Domain Path: /languages/
Requires at least: 4.0
Tested up to: 6.1
WC requires at least: 3.0
WC tested up to: 7.1
*/

defined( 'ABSPATH' ) || exit;

! defined( 'WOOSG_VERSION' ) && define( 'WOOSG_VERSION', '3.2.5' );
! defined( 'WOOSG_FILE' ) && define( 'WOOSG_FILE', __FILE__ );
! defined( 'WOOSG_URI' ) && define( 'WOOSG_URI', plugin_dir_url( __FILE__ ) );
! defined( 'WOOSG_DIR' ) && define( 'WOOSG_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'WOOSG_REVIEWS' ) && define( 'WOOSG_REVIEWS', 'https://wordpress.org/support/plugin/wpc-grouped-product/reviews/?filter=5' );
! defined( 'WOOSG_CHANGELOG' ) && define( 'WOOSG_CHANGELOG', 'https://wordpress.org/plugins/wpc-grouped-product/#developers' );
! defined( 'WOOSG_DISCUSSION' ) && define( 'WOOSG_DISCUSSION', 'https://wordpress.org/support/plugin/wpc-grouped-product' );
! defined( 'WPC_URI' ) && define( 'WPC_URI', WOOSG_URI );

include 'includes/wpc-dashboard.php';
include 'includes/wpc-menu.php';
include 'includes/wpc-kit.php';

if ( ! function_exists( 'woosg_init' ) ) {
	add_action( 'plugins_loaded', 'woosg_init', 11 );

	function woosg_init() {
		// load text-domain
		load_plugin_textdomain( 'wpc-grouped-product', false, basename( __DIR__ ) . '/languages/' );

		if ( ! function_exists( 'WC' ) || ! version_compare( WC()->version, '3.0', '>=' ) ) {
			add_action( 'admin_notices', 'woosg_notice_wc' );

			return;
		}

		if ( ! class_exists( 'WC_Product_Woosg' ) && class_exists( 'WC_Product' ) ) {
			class WC_Product_Woosg extends WC_Product {
				public function __construct( $product = 0 ) {
					parent::__construct( $product );
				}

				public function get_type() {
					return 'woosg';
				}

				public function add_to_cart_url() {
					$product_id = $this->get_id();

					return apply_filters( 'woocommerce_product_add_to_cart_url', get_permalink( $product_id ), $this );
				}

				public function add_to_cart_text() {
					if ( $this->is_purchasable() && $this->is_in_stock() ) {
						$text = WPCleverWoosg::localization( 'button_select', esc_html__( 'Select options', 'wpc-grouped-product' ) );
					} else {
						$text = WPCleverWoosg::localization( 'button_read', esc_html__( 'Read more', 'wpc-grouped-product' ) );
					}

					return apply_filters( 'woosg_product_add_to_cart_text', $text, $this );
				}

				public function single_add_to_cart_text() {
					$text = WPCleverWoosg::localization( 'button_single', esc_html__( 'Add to cart', 'wpc-grouped-product' ) );

					return apply_filters( 'woosg_product_single_add_to_cart_text', $text, $this );
				}

				public function get_price( $context = 'view' ) {
					if ( ( $context === 'view' ) && ( (float) $this->get_regular_price() == 0 ) ) {
						return '0';
					}

					if ( ( $context === 'view' ) && ( (float) parent::get_price( $context ) == 0 ) ) {
						return '0';
					}

					return parent::get_price( $context );
				}

				// extra functions

				public function has_variables() {
					if ( $items = $this->get_items() ) {
						foreach ( $items as $item ) {
							$item_product = wc_get_product( $item['id'] );

							if ( $item_product && $item_product->is_type( 'variable' ) ) {
								return true;
							}
						}
					}

					return false;
				}

				public function get_items() {
					$product_id = $this->id;
					$data       = array();

					if ( $ids = get_post_meta( $product_id, 'woosg_ids', true ) ) {
						$items = explode( ',', $ids );

						if ( is_array( $items ) && count( $items ) > 0 ) {
							foreach ( $items as $item ) {
								$item_data = explode( '/', $item );
								$data[]    = array(
									'id'  => apply_filters( 'woosg_item_id', absint( isset( $item_data[0] ) ? $item_data[0] : 0 ) ),
									'qty' => apply_filters( 'woosg_item_qty', (float) ( isset( $item_data[1] ) ? $item_data[1] : 0 ) )
								);
							}
						}
					}

					if ( count( $data ) > 0 ) {
						return $data;
					}

					return false;
				}
			}
		}

		if ( ! class_exists( 'WPCleverWoosg' ) ) {
			class WPCleverWoosg {
				public static $settings = array();
				public static $localization = array();
				protected static $image_size = 'woocommerce_thumbnail';

				protected static $instance = null;

				public static function instance() {
					if ( is_null( self::$instance ) ) {
						self::$instance = new self();
					}

					return self::$instance;
				}

				function __construct() {
					self::$settings     = (array) get_option( 'woosg_settings', [] );
					self::$localization = (array) get_option( 'woosg_localization', [] );

					// Init
					add_action( 'init', [ $this, 'init' ] );

					// Settings
					add_action( 'admin_init', [ $this, 'register_settings' ] );
					add_action( 'admin_menu', [ $this, 'admin_menu' ] );

					// Enqueue frontend scripts
					add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 99 );

					// Enqueue backend scripts
					add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

					// Add image to variation
					add_filter( 'woocommerce_available_variation', [ $this, 'available_variation' ], 10, 3 );

					// Backend AJAX search
					add_action( 'wp_ajax_woosg_update_search_settings', [ $this, 'update_search_settings' ] );
					add_action( 'wp_ajax_woosg_get_search_results', [ $this, 'get_search_results' ] );

					// Add to selector
					add_filter( 'product_type_selector', [ $this, 'product_type_selector' ] );

					// Product data tabs
					add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_data_tabs' ] );

					// Product tab
					if ( self::get_setting( 'position', 'above' ) === 'tab' ) {
						add_filter( 'woocommerce_product_tabs', [ $this, 'product_tabs' ] );
					}

					// Product data panels
					add_action( 'woocommerce_product_data_panels', [ $this, 'product_data_panels' ] );
					add_action( 'woocommerce_process_product_meta', [ $this, 'process_product_meta' ] );
					add_action( 'woocommerce_process_product_meta_woosg', [ $this, 'process_product_meta_woosg' ] );

					// Price html
					add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 99, 2 );

					// Price class
					add_filter( 'woocommerce_product_price_class', [ $this, 'product_price_class' ] );

					// Add to cart form & button
					add_action( 'woocommerce_woosg_add_to_cart', [ $this, 'add_to_cart_form' ] );
					add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'add_to_cart_button' ] );

					// Add to cart
					add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 10, 2 );
					add_action( 'woocommerce_add_to_cart', [ $this, 'add_to_cart' ], 10, 6 );
					add_filter( 'woocommerce_get_cart_item_from_session', [
						$this,
						'get_cart_item_from_session'
					], 10, 2 );

					// Cart contents instead of woocommerce_before_calculate_totals, prevent price error on mini-cart
					add_filter( 'woocommerce_get_cart_contents', [ $this, 'get_cart_contents' ] );

					// Admin
					add_filter( 'display_post_states', [ $this, 'display_post_states' ], 10, 2 );

					// Add settings link
					add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );
					add_filter( 'plugin_row_meta', [ $this, 'row_meta' ], 10, 2 );

					// Search filters
					if ( self::get_setting( 'search_sku', 'no' ) === 'yes' ) {
						add_filter( 'pre_get_posts', [ $this, 'search_sku' ], 99 );
					}

					if ( self::get_setting( 'search_exact', 'no' ) === 'yes' ) {
						add_action( 'pre_get_posts', [ $this, 'search_exact' ], 99 );
					}

					if ( self::get_setting( 'search_sentence', 'no' ) === 'yes' ) {
						add_action( 'pre_get_posts', [ $this, 'search_sentence' ], 99 );
					}

					// WPML
					if ( function_exists( 'wpml_loaded' ) ) {
						add_filter( 'woosg_item_id', [ $this, 'wpml_item_id' ], 99 );
					}

					// HPOS compatibility
					add_action( 'before_woocommerce_init', function () {
						if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
							\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WOOSG_FILE, true );
						}
					} );
				}

				function get_settings() {
					return apply_filters( 'woosg_get_settings', self::$settings );
				}

				function get_setting( $name, $default = false ) {
					if ( ! empty( self::$settings ) && isset( self::$settings[ $name ] ) ) {
						$setting = self::$settings[ $name ];
					} else {
						$setting = get_option( '_woosg_' . $name, $default );
					}

					return apply_filters( 'woosg_get_setting', $setting, $name, $default );
				}

				public static function localization( $key = '', $default = '' ) {
					$str = '';

					if ( ! empty( $key ) && ! empty( self::$localization[ $key ] ) ) {
						$str = self::$localization[ $key ];
					} elseif ( ! empty( $default ) ) {
						$str = $default;
					}

					return apply_filters( 'woosg_localization_' . $key, $str );
				}

				function init() {
					self::$image_size = apply_filters( 'woosg_image_size', self::$image_size );

					// shortcode
					add_shortcode( 'woosg', [ $this, 'shortcode' ] );
					add_shortcode( 'woosg_form', [ $this, 'shortcode_form' ] );
				}

				function available_variation( $data, $variable, $variation ) {
					if ( $image_id = $variation->get_image_id() ) {
						$data['woosg_image'] = wp_get_attachment_image( $image_id, self::$image_size );
					}

					return $data;
				}

				function register_settings() {
					// settings
					register_setting( 'woosg_settings', 'woosg_settings' );

					// localization
					register_setting( 'woosg_localization', 'woosg_localization' );
				}

				function admin_menu() {
					add_submenu_page( 'wpclever', esc_html__( 'WPC Grouped Product', 'wpc-grouped-product' ), esc_html__( 'Grouped Product', 'wpc-grouped-product' ), 'manage_options', 'wpclever-woosg', array(
						$this,
						'admin_menu_content'
					) );
				}

				function admin_menu_content() {
					add_thickbox();
					$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'settings';
					?>
                    <div class="wpclever_settings_page wrap">
                        <h1 class="wpclever_settings_page_title">WPC Grouped
                            Product <?php echo esc_attr( WOOSG_VERSION ); ?></h1>
                        <div class="wpclever_settings_page_desc about-text">
                            <p>
                                Thank you for using our plugin! If you are satisfied, please reward it a full five-star
                                <span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span> rating.
                                <br/>
                                <a href="<?php echo esc_url( WOOSG_REVIEWS ); ?>"
                                   target="_blank"><?php esc_html_e( 'Reviews', 'wpc-grouped-product' ); ?></a> | <a
                                        href="<?php echo esc_url( WOOSG_CHANGELOG ); ?>"
                                        target="_blank"><?php esc_html_e( 'Changelog', 'wpc-grouped-product' ); ?></a>
                                | <a href="<?php echo esc_url( WOOSG_DISCUSSION ); ?>"
                                     target="_blank"><?php esc_html_e( 'Discussion', 'wpc-grouped-product' ); ?></a>
                            </p>
                        </div>
						<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) { ?>
                            <div class="notice notice-success is-dismissible">
                                <p><?php esc_html_e( 'Settings updated.', 'wpc-grouped-product' ); ?></p>
                            </div>
						<?php } ?>
                        <div class="wpclever_settings_page_nav">
                            <h2 class="nav-tab-wrapper">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-woosg&tab=how' ) ); ?>"
                                   class="<?php echo esc_attr( $active_tab === 'how' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'How to use?', 'wpc-grouped-product' ); ?>
                                </a>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-woosg&tab=settings' ) ); ?>"
                                   class="<?php echo esc_attr( $active_tab === 'settings' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'Settings', 'wpc-grouped-product' ); ?>
                                </a>
                                <a href="<?php echo admin_url( 'admin.php?page=wpclever-woosg&tab=localization' ); ?>"
                                   class="<?php echo esc_attr( $active_tab === 'localization' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'Localization', 'wpc-grouped-product' ); ?>
                                </a>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-woosg&tab=premium' ) ); ?>"
                                   class="<?php echo esc_attr( $active_tab === 'premium' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>"
                                   style="color: #c9356e">
									<?php esc_html_e( 'Premium Version', 'wpc-grouped-product' ); ?>
                                </a>
                                <a href="<?php echo admin_url( 'admin.php?page=wpclever-kit' ); ?>" class="nav-tab">
									<?php esc_html_e( 'Essential Kit', 'wpc-grouped-product' ); ?>
                                </a>
                            </h2>
                        </div>
                        <div class="wpclever_settings_page_content">
							<?php if ( $active_tab === 'how' ) { ?>
                                <div class="wpclever_settings_page_content_text">
                                    <p>
										<?php esc_html_e( 'When creating the product, please choose product data is "Smart grouped" then you can see the search field to start search and add products.', 'wpc-grouped-product' ); ?>
                                    </p>
                                    <p>
                                        <img src="<?php echo esc_url( WOOSG_URI . 'assets/images/how-01.jpg' ); ?>"/>
                                    </p>
                                </div>
							<?php } elseif ( $active_tab === 'settings' ) {
								$price_format        = self::get_setting( 'price_format', 'from' );
								$position            = self::get_setting( 'position', 'above' );
								$variations_selector = self::get_setting( 'variations_selector', 'default' );
								$show_thumb          = self::get_setting( 'show_thumb', 'yes' );
								$show_description    = self::get_setting( 'show_description', 'no' );
								$show_price          = self::get_setting( 'show_price', 'yes' );
								$selector            = self::get_setting( 'selector', 'quantity' );
								$show_plus_minus     = self::get_setting( 'show_plus_minus', 'no' );
								$link                = self::get_setting( 'link', 'yes' );
								$change_image        = self::get_setting( 'change_image', 'yes' );
								$change_price        = self::get_setting( 'change_price', 'yes' );
								$including_main      = self::get_setting( 'including_main', 'no' );
								$main_price          = self::get_setting( 'main_price', 'zero' );
								?>
                                <form method="post" action="options.php">
                                    <table class="form-table">
                                        <tr class="heading">
                                            <th colspan="2">
												<?php esc_html_e( 'General', 'wpc-grouped-product' ); ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Price format', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[price_format]">
                                                    <option value="normal" <?php selected( $price_format, 'normal' ); ?>><?php esc_html_e( 'Normal price', 'wpc-grouped-product' ); ?></option>
                                                    <option value="from" <?php selected( $price_format, 'from' ); ?>><?php esc_html_e( 'From price', 'wpc-grouped-product' ); ?></option>
                                                    <option value="auto" <?php selected( $price_format, 'auto' ); ?>><?php esc_html_e( 'Auto calculated price', 'wpc-grouped-product' ); ?></option>
                                                    <option value="none" <?php selected( $price_format, 'none' ); ?>><?php esc_html_e( 'None', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Choose the price format for grouped product on the shop/archive page. Using "Auto calculated price" can cause your site slow down.', 'wpc-grouped-product' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr class="heading">
                                            <th colspan="2">
												<?php esc_html_e( 'Grouped products', 'wpc-grouped-product' ); ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Position', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[position]">
                                                    <option value="above" <?php selected( $position, 'above' ); ?>><?php esc_html_e( 'Above add to cart button', 'wpc-grouped-product' ); ?></option>
                                                    <option value="below" <?php selected( $position, 'below' ); ?>><?php esc_html_e( 'Under add to cart button', 'wpc-grouped-product' ); ?></option>
                                                    <option value="tab" <?php selected( $position, 'tab' ); ?>><?php esc_html_e( 'In a new tab', 'wpc-grouped-product' ); ?></option>
                                                    <option value="no" <?php selected( $position, 'no' ); ?>><?php esc_html_e( 'No (hide it)', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Choose the position to show the grouped product list. You also can use the shortcode [woosg] to show the list where you want.', 'wpc-grouped-product' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Variations selector', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[variations_selector]">
                                                    <option value="default" <?php selected( $variations_selector, 'default' ); ?>><?php esc_html_e( 'Default', 'wpc-grouped-product' ); ?></option>
                                                    <option value="woovr" <?php selected( $variations_selector, 'woovr' ); ?>><?php esc_html_e( 'Use WPC Variations Radio Buttons', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                                <span class="description">If you choose "Use WPC Variations Radio Buttons", please install <a
                                                            href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wpc-variations-radio-buttons&TB_iframe=true&width=800&height=550' ) ); ?>"
                                                            class="thickbox"
                                                            title="Install WPC Variations Radio Buttons">WPC Variations Radio Buttons</a> to make it work.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Show thumbnail', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[show_thumb]">
                                                    <option value="yes" <?php selected( $show_thumb, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                                    <option value="no" <?php selected( $show_thumb, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Show short description', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[show_description]">
                                                    <option value="yes" <?php selected( $show_description, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                                    <option value="no" <?php selected( $show_description, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Show price', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[show_price]">
                                                    <option value="yes" <?php selected( $show_price, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                                    <option value="no" <?php selected( $show_price, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Product selector', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[selector]">
                                                    <option value="quantity" <?php selected( $selector, 'quantity' ); ?>><?php esc_html_e( 'Quantity', 'wpc-grouped-product' ); ?></option>
                                                    <option value="checkbox" <?php selected( $selector, 'checkbox' ); ?>><?php esc_html_e( 'Checkbox', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'You still can choose the selector for each grouped product in the product settings.', 'wpc-grouped-product' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Show plus/minus button', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[show_plus_minus]">
                                                    <option value="yes" <?php selected( $show_plus_minus, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                                    <option value="no" <?php selected( $show_plus_minus, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Show the plus/minus button to increase/decrease the quantity.', 'wpc-grouped-product' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Link to individual product', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[link]">
                                                    <option value="yes" <?php selected( $link, 'yes' ); ?>><?php esc_html_e( 'Yes, open in the same tab', 'wpc-grouped-product' ); ?></option>
                                                    <option value="yes_blank" <?php selected( $link, 'yes_blank' ); ?>><?php esc_html_e( 'Yes, open in the new tab', 'wpc-grouped-product' ); ?></option>
                                                    <option value="yes_popup" <?php selected( $link, 'yes_popup' ); ?>><?php esc_html_e( 'Yes, open quick view popup', 'wpc-grouped-product' ); ?></option>
                                                    <option value="no" <?php selected( $link, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                                <span class="description">If you choose "Open quick view popup", please install <a
                                                            href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=woo-smart-quick-view&TB_iframe=true&width=800&height=550' ) ); ?>"
                                                            class="thickbox" title="Install WPC Smart Quick View">WPC Smart Quick View</a> to make it work.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Change image', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[change_image]">
                                                    <option value="yes" <?php selected( $change_image, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                                    <option value="no" <?php selected( $change_image, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Change the main product image when choosing the variation of grouped product.', 'wpc-grouped-product' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Change price', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[change_price]" class="woosg_change_price">
                                                    <option value="yes" <?php selected( $change_price, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                                    <option value="yes_custom" <?php selected( $change_price, 'yes_custom' ); ?>><?php esc_html_e( 'Yes, custom selector', 'wpc-grouped-product' ); ?></option>
                                                    <option value="no" <?php selected( $change_price, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                                                </select> <input type="text" name="woosg_settings[change_price_custom]"
                                                                 value="<?php echo self::get_setting( 'change_price_custom', '.summary > .price' ); ?>"
                                                                 placeholder=".summary > .price"
                                                                 class="woosg_change_price_custom"/>
                                                <span class="description"><?php esc_html_e( 'Change the main product price when choosing the variation of grouped product. It uses JavaScript to change product price so it is very dependent on theme’s HTML. If it cannot find and update the product price, please contact us and we can help you adjust the JS file.', 'wpc-grouped-product' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr class="heading">
                                            <th colspan="2">
												<?php esc_html_e( 'Cart & Checkout', 'wpc-grouped-product' ); ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Include main product', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[including_main]">
                                                    <option value="yes" <?php selected( $including_main, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                                    <option value="no" <?php selected( $including_main, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Include main product on the cart. Helpful when you need to add some extra options for the main product, e.g WPC Frequently Bought Together, WPC Force Sells.', 'wpc-grouped-product' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Main product price', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <select name="woosg_settings[main_price]">
                                                    <option value="zero" <?php selected( $main_price, 'zero' ); ?>><?php esc_html_e( 'Zero price', 'wpc-grouped-product' ); ?></option>
                                                    <option value="price" <?php selected( $main_price, 'price' ); ?>><?php esc_html_e( 'Normal price', 'wpc-grouped-product' ); ?></option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr class="heading">
                                            <th colspan="2">
												<?php esc_html_e( 'Search', 'wpc-grouped-product' ); ?>
                                            </th>
                                        </tr>
										<?php self::search_settings(); ?>
                                        <tr class="submit">
                                            <th colspan="2">
												<?php settings_fields( 'woosg_settings' ); ?>
												<?php submit_button(); ?>
                                            </th>
                                        </tr>
                                    </table>
                                </form>
							<?php } elseif ( $active_tab === 'localization' ) { ?>
                                <form method="post" action="options.php">
                                    <table class="form-table">
                                        <tr class="heading">
                                            <th scope="row"><?php esc_html_e( 'General', 'wpc-grouped-product' ); ?></th>
                                            <td>
												<?php esc_html_e( 'Leave blank to use the default text and its equivalent translation in multiple languages.', 'wpc-grouped-product' ); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'From', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <input type="text" name="woosg_localization[from]" class="regular-text"
                                                       value="<?php echo esc_attr( self::localization( 'from' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'From', 'wpc-grouped-product' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Total', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <input type="text" name="woosg_localization[total]" class="regular-text"
                                                       value="<?php echo esc_attr( self::localization( 'total' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Total:', 'wpc-grouped-product' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Default above text', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <input type="text" class="large-text"
                                                       name="woosg_localization[above_text]"
                                                       value="<?php echo esc_attr( self::localization( 'above_text' ) ); ?>"/>
                                                <span class="description"><?php esc_html_e( 'The default text above products list. You can overwrite it in product settings.', 'wpc-grouped-product' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Default under text', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <input type="text" class="large-text"
                                                       name="woosg_localization[under_text]"
                                                       value="<?php echo esc_attr( self::localization( 'under_text' ) ); ?>"/>
                                                <span class="description"><?php esc_html_e( 'The default text under products list. You can overwrite it in product settings.', 'wpc-grouped-product' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Tab name', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <input type="text" name="woosg_localization[tab]" class="regular-text"
                                                       value="<?php echo esc_attr( self::localization( 'tab' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Grouped products', 'wpc-grouped-product' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Choose an attribute', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="woosg_localization[choose]"
                                                       value="<?php echo esc_attr( self::localization( 'choose' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Choose %s', 'wpc-grouped-product' ); ?>"/>
                                                <span class="description"><?php esc_html_e( 'Use %s to show the attribute name.', 'wpc-grouped-product' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Clear', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text" name="woosg_localization[clear]"
                                                       value="<?php echo esc_attr( self::localization( 'clear' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Clear', 'wpc-grouped-product' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr class="heading">
                                            <th colspan="2">
												<?php esc_html_e( '"Add to cart" button labels', 'wpc-grouped-product' ); ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Shop/archive page', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <div style="margin-bottom: 5px">
                                                    <input type="text" class="regular-text"
                                                           name="woosg_localization[button_select]"
                                                           value="<?php echo esc_attr( self::localization( 'button_select' ) ); ?>"
                                                           placeholder="<?php esc_attr_e( 'Select options', 'wpc-grouped-product' ); ?>"/>
                                                    <span class="description"><?php esc_html_e( 'For purchasable grouped.', 'wpc-grouped-product' ); ?></span>
                                                </div>
                                                <div>
                                                    <input type="text" class="regular-text"
                                                           name="woosg_localization[button_read]"
                                                           value="<?php echo esc_attr( self::localization( 'button_read' ) ); ?>"
                                                           placeholder="<?php esc_attr_e( 'Read more', 'wpc-grouped-product' ); ?>"/>
                                                    <span class="description"><?php esc_html_e( 'For un-purchasable grouped.', 'wpc-grouped-product' ); ?></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Single product page', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="woosg_localization[button_single]"
                                                       value="<?php echo esc_attr( self::localization( 'button_single' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Add to cart', 'wpc-grouped-product' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr class="heading">
                                            <th colspan="2">
												<?php esc_html_e( 'Alert', 'wpc-grouped-product' ); ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Require selection', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <input type="text" class="large-text"
                                                       name="woosg_localization[alert_selection]"
                                                       value="<?php echo esc_attr( self::localization( 'alert_selection' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Please select a purchasable variation for [name] before adding this grouped product to the cart.', 'wpc-grouped-product' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Enforce a selection', 'wpc-grouped-product' ); ?></th>
                                            <td>
                                                <input type="text" class="large-text"
                                                       name="woosg_localization[alert_empty]"
                                                       value="<?php echo esc_attr( self::localization( 'alert_empty' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Please choose at least one of the listed products before adding this grouped product to the cart.', 'wpc-grouped-product' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr class="submit">
                                            <th colspan="2">
												<?php settings_fields( 'woosg_localization' ); ?>
												<?php submit_button(); ?>
                                            </th>
                                        </tr>
                                    </table>
                                </form>
							<?php } elseif ( $active_tab === 'premium' ) { ?>
                                <div class="wpclever_settings_page_content_text">
                                    <p>
                                        Get the Premium Version just $29! <a
                                                href="https://wpclever.net/downloads/grouped-product?utm_source=pro&utm_medium=woosg&utm_campaign=wporg"
                                                target="_blank">https://wpclever.net/downloads/grouped-product</a>
                                    </p>
                                    <p><strong>Extra features for Premium Version:</strong></p>
                                    <ul style="margin-bottom: 0">
                                        <li>- Add more than 3 products to the grouped.</li>
                                        <li>- Get the lifetime update & premium support.</li>
                                    </ul>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php
				}

				function search_settings() {
					$search_sku      = self::get_setting( 'search_sku', 'no' );
					$search_id       = self::get_setting( 'search_id', 'no' );
					$search_exact    = self::get_setting( 'search_exact', 'no' );
					$search_sentence = self::get_setting( 'search_sentence', 'no' );
					$search_same     = self::get_setting( 'search_same', 'no' );
					?>
                    <tr>
                        <th><?php esc_html_e( 'Search limit', 'wpc-grouped-product' ); ?></th>
                        <td>
                            <input name="woosg_settings[search_limit]" type="number" min="1" max="500"
                                   class="woosg_search_limit"
                                   value="<?php echo esc_attr( self::get_setting( 'search_limit', '5' ) ); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Search by SKU', 'wpc-grouped-product' ); ?></th>
                        <td>
                            <select name="woosg_settings[search_sku]" class="woosg_search_sku">
                                <option value="yes" <?php selected( $search_sku, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                <option value="no" <?php selected( $search_sku, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Search by ID', 'wpc-grouped-product' ); ?></th>
                        <td>
                            <select name="woosg_settings[search_id]" class="woosg_search_id">
                                <option value="yes" <?php selected( $search_id, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                <option value="no" <?php selected( $search_id, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                            </select>
                            <span class="description"><?php esc_html_e( 'Search by ID when entering the numeric only.', 'wpc-grouped-product' ); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Search exact', 'wpc-grouped-product' ); ?></th>
                        <td>
                            <select name="woosg_settings[search_exact]" class="woosg_search_exact">
                                <option value="yes" <?php selected( $search_exact, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                <option value="no" <?php selected( $search_exact, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                            </select>
                            <span class="description"><?php esc_html_e( 'Match whole product title or content?', 'wpc-grouped-product' ); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Search sentence', 'wpc-grouped-product' ); ?></th>
                        <td>
                            <select name="woosg_settings[search_sentence]" class="woosg_search_sentence">
                                <option value="yes" <?php selected( $search_sentence, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                <option value="no" <?php selected( $search_sentence, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                            </select>
                            <span class="description"><?php esc_html_e( 'Do a phrase search?', 'wpc-grouped-product' ); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Accept same products', 'wpc-grouped-product' ); ?></th>
                        <td>
                            <select name="woosg_settings[search_same]" class="woosg_search_same">
                                <option value="yes" <?php selected( $search_same, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                <option value="no" <?php selected( $search_same, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                            </select>
                            <span class="description"><?php esc_html_e( 'If yes, a product can be added many times.', 'wpc-grouped-product' ); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Product types', 'wpc-grouped-product' ); ?></th>
                        <td>
							<?php
							$search_types  = self::get_setting( 'search_types', array( 'all' ) );
							$product_types = wc_get_product_types();
							$product_types = array_merge( array( 'all' => esc_html__( 'All', 'wpc-grouped-product' ) ), $product_types );

							$key_pos = array_search( 'variable', array_keys( $product_types ) );

							if ( $key_pos !== false ) {
								$key_pos ++;
								$second_array  = array_splice( $product_types, $key_pos );
								$product_types = array_merge( $product_types, array( 'variation' => esc_html__( ' → Variation', 'wpc-grouped-product' ) ), $second_array );
							}

							echo '<select name="woosg_settings[search_types][]" multiple style="width: 200px; height: 150px;" class="woosg_search_types">';

							foreach ( $product_types as $key => $name ) {
								echo '<option value="' . esc_attr( $key ) . '" ' . ( in_array( $key, $search_types, true ) ? 'selected' : '' ) . '>' . esc_html( $name ) . '</option>';
							}

							echo '</select>';
							?>
                        </td>
                    </tr>
					<?php
				}

				function enqueue_scripts() {
					wp_enqueue_style( 'woosg-frontend', WOOSG_URI . 'assets/css/frontend.css', array(), WOOSG_VERSION );
					wp_enqueue_script( 'woosg-frontend', WOOSG_URI . 'assets/js/frontend.js', array( 'jquery' ), WOOSG_VERSION, true );
					wp_localize_script( 'woosg-frontend', 'woosg_vars', array(
							'change_image'             => self::get_setting( 'change_image', 'yes' ),
							'change_price'             => self::get_setting( 'change_price', 'yes' ),
							'price_selector'           => self::get_setting( 'change_price_custom', '' ),
							'price_format'             => get_woocommerce_price_format(),
							'price_decimals'           => wc_get_price_decimals(),
							'price_thousand_separator' => wc_get_price_thousand_separator(),
							'price_decimal_separator'  => wc_get_price_decimal_separator(),
							'currency_symbol'          => get_woocommerce_currency_symbol(),
							'trim_zeros'               => apply_filters( 'woosg_price_trim_zeros', apply_filters( 'woocommerce_price_trim_zeros', false ) ),
							'total_text'               => self::localization( 'total', esc_html__( 'Total:', 'wpc-grouped-product' ) ),
							'add_to_cart'              => self::localization( 'button_single', esc_html__( 'Add to cart', 'wpc-grouped-product' ) ),
							'select_options'           => self::localization( 'button_select', esc_html__( 'Select options', 'wpc-grouped-product' ) ),
							'alert_selection'          => self::localization( 'alert_selection', esc_html__( 'Please select a purchasable variation for [name] before adding this grouped product to the cart.', 'wpc-grouped-product' ) ),
							'alert_empty'              => self::localization( 'alert_empty', esc_html__( 'Please choose at least one of the listed products before adding this grouped product to the cart.', 'wpc-grouped-product' ) )
						)
					);
				}

				function admin_enqueue_scripts() {
					wp_enqueue_style( 'hint', WOOSG_URI . 'assets/css/hint.css' );
					wp_enqueue_style( 'woosg-backend', WOOSG_URI . 'assets/css/backend.css', array(), WOOSG_VERSION );
					wp_enqueue_script( 'woosg-backend', WOOSG_URI . 'assets/js/backend.js', array(
						'jquery',
						'jquery-ui-dialog',
						'jquery-ui-sortable'
					), WOOSG_VERSION, true );
					wp_localize_script( 'woosg-backend', 'woosg_vars', array(
							'price_decimals'           => wc_get_price_decimals(),
							'price_thousand_separator' => wc_get_price_thousand_separator(),
							'price_decimal_separator'  => wc_get_price_decimal_separator()
						)
					);
				}

				function action_links( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin === $file ) {
						$settings             = '<a href="' . admin_url( 'admin.php?page=wpclever-woosg&tab=settings' ) . '">' . esc_html__( 'Settings', 'wpc-grouped-product' ) . '</a>';
						$links['wpc-premium'] = '<a href="' . admin_url( 'admin.php?page=wpclever-woosg&tab=premium' ) . '">' . esc_html__( 'Premium Version', 'wpc-grouped-product' ) . '</a>';
						array_unshift( $links, $settings );
					}

					return (array) $links;
				}

				function row_meta( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin === $file ) {
						$row_meta = array(
							'support' => '<a href="' . esc_url( WOOSG_DISCUSSION ) . '" target="_blank">' . esc_html__( 'Community support', 'wpc-grouped-product' ) . '</a>',
						);

						return array_merge( $links, $row_meta );
					}

					return (array) $links;
				}

				function add_cart_item_data( $cart_item_data, $product_id ) {
					$item_product = wc_get_product( $product_id );

					if ( $item_product && $item_product->is_type( 'woosg' ) && ( $ids = get_post_meta( $product_id, 'woosg_ids', true ) ) ) {
						// make sure that is grouped
						if ( isset( $_REQUEST['woosg_ids'] ) ) {
							$ids = $_REQUEST['woosg_ids'];
							unset( $_REQUEST['woosg_ids'] );
						}

						$ids = self::clean_ids( $ids );

						if ( ! empty( $ids ) ) {
							$cart_item_data['woosg_ids'] = $ids;
						}
					}

					return $cart_item_data;
				}

				function add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
					if ( ! empty( $cart_item_data['woosg_ids'] ) ) {
						if ( $items = self::get_items( $cart_item_data['woosg_ids'] ) ) {
							foreach ( $items as $item ) {
								$item_id           = $item['id'];
								$item_qty          = $item['qty'];
								$item_variation    = $item['attrs'];
								$item_variation_id = 0;
								$item_product      = wc_get_product( $item_id );

								if ( ! $item_product || ( $item_qty <= 0 ) ) {
									continue;
								}

								if ( $item_product instanceof WC_Product_Variation ) {
									// ensure we don't add a variation to the cart directly by variation ID
									$item_variation_id = $item_id;
									$item_id           = $item_product->get_parent_id();

									if ( empty( $item_variation ) ) {
										$item_variation = $item_product->get_variation_attributes();
									}
								}

								// add to cart
								$item_data = apply_filters( 'woosg_cart_item_data', array(), $item, $product_id );
								$item_key  = WC()->cart->add_to_cart( $item_id, $item_qty * $quantity, $item_variation_id, $item_variation, $item_data );

								if ( $item_key ) {
									WC()->cart->cart_contents[ $item_key ]['woosg_parent_id']   = $product_id;
									WC()->cart->cart_contents[ $item_key ]['woosg_parent_key']  = $cart_item_key;
									WC()->cart->cart_contents[ $cart_item_key ]['woosg_keys'][] = $item_key;
									do_action( 'woosg_add_to_cart_item', $item_key, $item, $cart_item_key, $product_id );
								}
							} // end foreach
						}

						// remove grouped
						$including_main = get_post_meta( $product_id, 'woosg_including_main', true );

						if ( ( ( ! $including_main || ( $including_main === 'default' ) ) && ( self::get_setting( 'including_main', 'no' ) !== 'yes' ) ) || ( $including_main === 'no' ) ) {
							WC()->cart->remove_cart_item( $cart_item_key );
						}
					}
				}

				function get_cart_contents( $cart_contents ) {
					foreach ( $cart_contents as $cart_item_key => $cart_item ) {
						if ( ! empty( $cart_item['woosg_ids'] ) && ( self::get_setting( 'main_price', 'zero' ) === 'zero' ) ) {
							$cart_item['data']->set_price( 0 );
						}

						if ( ! empty( $cart_item['woosg_keys'] ) ) {
							$has_key = false;

							foreach ( $cart_item['woosg_keys'] as $key ) {
								if ( isset( $cart_contents[ $key ] ) ) {
									$has_key = true;
								}
							}

							if ( ! $has_key ) {
								WC()->cart->remove_cart_item( $cart_item_key );
								unset( $cart_contents[ $cart_item_key ] );
							}
						}
					}

					return $cart_contents;
				}

				function get_cart_item_from_session( $cart_item, $item_session_values ) {
					if ( isset( $item_session_values['woosg_ids'] ) && ! empty( $item_session_values['woosg_ids'] ) ) {
						$cart_item['woosg_ids'] = $item_session_values['woosg_ids'];
					}

					return $cart_item;
				}

				function update_search_settings() {
					$settings = (array) get_option( 'woosg_settings', [] );

					$settings['search_limit']    = (int) sanitize_text_field( $_POST['limit'] );
					$settings['search_sku']      = sanitize_text_field( $_POST['sku'] );
					$settings['search_id']       = sanitize_text_field( $_POST['id'] );
					$settings['search_exact']    = sanitize_text_field( $_POST['exact'] );
					$settings['search_sentence'] = sanitize_text_field( $_POST['sentence'] );
					$settings['search_same']     = sanitize_text_field( $_POST['same'] );
					$settings['search_types']    = array_map( 'sanitize_text_field', (array) $_POST['types'] );

					update_option( 'woosg_settings', $settings );
					die();
				}

				function get_search_results() {
					$types       = self::get_setting( 'search_types', array( 'all' ) );
					$keyword     = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';
					$ids         = isset( $_POST['ids'] ) ? self::clean_ids( $_POST['ids'] ) : '';
					$exclude_ids = array();
					$items       = explode( ',', $ids );

					if ( is_array( $items ) && count( $items ) > 2 ) {
						echo '<ul><span>Please use the Premium Version to add more than 3 products to the grouped & get the premium support. Click <a href="https://wpclever.net/downloads/grouped-product?utm_source=pro&utm_medium=woosg&utm_campaign=wporg" target="_blank">here</a> to buy, just $29!</span></ul>';
						die();
					}

					if ( ( self::get_setting( 'search_id', 'no' ) === 'yes' ) && is_numeric( $keyword ) ) {
						// search by id
						$query_args = array(
							'p'         => absint( $keyword ),
							'post_type' => 'product'
						);
					} else {
						$query_args = array(
							'is_woosg'       => true,
							'post_type'      => 'product',
							'post_status'    => array( 'publish', 'private' ),
							's'              => $keyword,
							'posts_per_page' => self::get_setting( 'search_limit', '5' )
						);

						if ( ! empty( $types ) && ! in_array( 'all', $types, true ) ) {
							$product_types = $types;

							if ( in_array( 'variation', $types, true ) ) {
								$product_types[] = 'variable';
							}

							$query_args['tax_query'] = array(
								array(
									'taxonomy' => 'product_type',
									'field'    => 'slug',
									'terms'    => $product_types,
								),
							);
						}

						if ( self::get_setting( 'search_same', 'no' ) !== 'yes' ) {
							if ( is_array( $items ) && count( $items ) > 0 ) {
								foreach ( $items as $item ) {
									$item_data     = explode( '/', $item );
									$exclude_ids[] = absint( isset( $item_data[0] ) ? $item_data[0] : 0 );
								}
							}

							$query_args['post__not_in'] = $exclude_ids;
						}
					}

					$query = new WP_Query( $query_args );

					if ( $query->have_posts() ) {
						echo '<ul>';

						while ( $query->have_posts() ) {
							$query->the_post();
							$item_product = wc_get_product( get_the_ID() );

							if ( ! $item_product || $item_product->is_type( 'woosg' ) ) {
								continue;
							}

							if ( ! $item_product->is_type( 'variable' ) || in_array( 'variable', $types, true ) || in_array( 'all', $types, true ) ) {
								self::product_data_li( $item_product, 0, true );
							}

							if ( $item_product->is_type( 'variable' ) && ( empty( $types ) || in_array( 'all', $types, true ) || in_array( 'variation', $types, true ) ) ) {
								// show all children
								$children = $item_product->get_children();

								if ( is_array( $children ) && count( $children ) > 0 ) {
									foreach ( $children as $child ) {
										$child_product = wc_get_product( $child );
										self::product_data_li( $child_product, 0, true );
									}
								}
							}
						}

						echo '</ul>';
						wp_reset_postdata();
					} else {
						echo '<ul><span>' . sprintf( esc_html__( 'No results found for "%s"', 'wpc-grouped-product' ), esc_html( $keyword ) ) . '</span></ul>';
					}

					die();
				}

				function search_sku( $query ) {
					if ( $query->is_search && isset( $query->query['is_woosg'] ) ) {
						global $wpdb;

						$sku = sanitize_text_field( $query->query['s'] );
						$ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value = %s;", $sku ) );

						if ( ! $ids ) {
							return;
						}

						$posts = array();

						foreach ( $ids as $id ) {
							$post = get_post( $id );

							if ( $post->post_type === 'product_variation' ) {
								$posts[] = $post->post_parent;
							} else {
								$posts[] = $post->ID;
							}
						}

						unset( $query->query['s'], $query->query_vars['s'] );
						$query->set( 'post__in', $posts );
					}
				}

				function search_exact( $query ) {
					if ( $query->is_search && isset( $query->query['is_woosg'] ) ) {
						$query->set( 'exact', true );
					}
				}

				function search_sentence( $query ) {
					if ( $query->is_search && isset( $query->query['is_woosg'] ) ) {
						$query->set( 'sentence', true );
					}
				}

				function product_type_selector( $types ) {
					$types['woosg'] = esc_html__( 'Smart grouped', 'wpc-grouped-product' );

					return $types;
				}

				function product_data_tabs( $tabs ) {
					$tabs['woosg'] = array(
						'label'  => esc_html__( 'Grouped Products', 'wpc-grouped-product' ),
						'target' => 'woosg_settings',
						'class'  => array( 'show_if_woosg' ),
					);

					return $tabs;
				}

				function product_tabs( $tabs ) {
					global $product;

					if ( ( self::get_setting( 'position', 'above' ) === 'tab' ) && $product->is_type( 'woosg' ) ) {
						$tabs['woosg'] = array(
							'title'    => self::localization( 'tab', esc_html__( 'Grouped products', 'wpc-grouped-product' ) ),
							'priority' => 50,
							'callback' => array( $this, 'product_tab_woosg' )
						);
					}

					return $tabs;
				}

				function product_tab_woosg() {
					self::show_items();
				}

				function product_data_panels() {
					global $post;
					$post_id        = $post->ID;
					$including_main = get_post_meta( $post_id, 'woosg_including_main', true );
					$selector       = get_post_meta( $post_id, 'woosg_selector', true );
					?>
                    <div id='woosg_settings' class='panel woocommerce_options_panel woosg_table'>
                        <div id="woosg_search_settings" style="display: none"
                             data-title="<?php esc_html_e( 'Search settings', 'wpc-grouped-product' ); ?>">
                            <table>
								<?php self::search_settings(); ?>
                                <tr>
                                    <th></th>
                                    <td>
                                        <button id="woosg_search_settings_update" class="button button-primary">
											<?php esc_html_e( 'Update Options', 'wpc-grouped-product' ); ?>
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <table>
                            <tr>
                                <th><?php esc_html_e( 'Search', 'wpc-grouped-product' ); ?> (<a
                                            href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-woosg&tab=settings#search' ) ); ?>"
                                            id="woosg_search_settings_btn"><?php esc_html_e( 'settings', 'wpc-grouped-product' ); ?></a>)
                                </th>
                                <td>
                                    <div class="w100">
								<span class="loading"
                                      id="woosg_loading"
                                      style="display: none;"><?php esc_html_e( 'searching...', 'wpc-grouped-product' ); ?></span>
                                        <input type="search" id="woosg_keyword"
                                               placeholder="<?php esc_html_e( 'Type any keyword to search', 'wpc-grouped-product' ); ?>"/>
                                        <div id="woosg_results" class="woosg_results" style="display: none;"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="woosg_tr_space">
                                <th><?php esc_html_e( 'Selected', 'wpc-grouped-product' ); ?></th>
                                <td>
                                    <div class="w100">
                                        <input type="hidden" id="woosg_ids" class="woosg_ids" name="woosg_ids"
                                               value="<?php echo esc_attr( get_post_meta( $post_id, 'woosg_ids', true ) ); ?>"
                                               readonly/>
                                        <div id="woosg_selected" class="woosg_selected">
                                            <ul>
												<?php
												if ( $ids = get_post_meta( $post_id, 'woosg_ids', true ) ) {
													if ( $items = self::get_items( $ids ) ) {
														foreach ( $items as $item ) {
															$item_product = wc_get_product( $item['id'] );

															if ( ! $item_product || $item_product->is_type( 'woosg' ) ) {
																continue;
															}

															self::product_data_li( $item_product, $item['qty'] );
														}
													}
												}
												?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="woosg_tr_space">
                                <th><?php echo esc_html__( 'Regular price', 'wpc-grouped-product' ) . ' (' . get_woocommerce_currency_symbol() . ')'; ?></th>
                                <td>
                                    <span id="woosg_regular_price"></span>
                                    <span class="woocommerce-help-tip"
                                          data-tip="<?php esc_attr_e( 'This price was used for displaying only. Always put a price in the General tab to display the Add to Cart button.', 'wpc-grouped-product' ); ?>"></span>
                                </td>
                            </tr>
                            <tr class="woosg_tr_space">
                                <th><?php esc_html_e( 'Include main product', 'wpc-grouped-product' ); ?></th>
                                <td>
                                    <select name="woosg_including_main">
                                        <option value="default" <?php selected( $including_main, 'default' ); ?>><?php esc_html_e( 'Default', 'wpc-grouped-product' ); ?></option>
                                        <option value="yes" <?php selected( $including_main, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-grouped-product' ); ?></option>
                                        <option value="no" <?php selected( $including_main, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-grouped-product' ); ?></option>
                                    </select>
                                    <span class="woocommerce-help-tip"
                                          data-tip="<?php esc_attr_e( 'Include main product on the cart. Helpful when you need to add some extra options for the main product, e.g WPC Frequently Bought Together, WPC Force Sells.', 'wpc-grouped-product' ); ?>"></span>
                                </td>
                            </tr>
                            <tr class="woosg_tr_space">
                                <th><?php esc_html_e( 'Product selector', 'wpc-grouped-product' ); ?></th>
                                <td>
                                    <select name="woosg_selector">
                                        <option value="default" <?php selected( $selector, 'default' ); ?>><?php esc_html_e( 'Default', 'wpc-grouped-product' ); ?></option>
                                        <option value="quantity" <?php selected( $selector, 'quantity' ); ?>><?php esc_html_e( 'Quantity', 'wpc-grouped-product' ); ?></option>
                                        <option value="checkbox" <?php selected( $selector, 'checkbox' ); ?>><?php esc_html_e( 'Checkbox', 'wpc-grouped-product' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="woosg_tr_space">
                                <th><?php esc_html_e( 'Custom display price', 'wpc-grouped-product' ); ?></th>
                                <td>
                                    <input type="text" name="woosg_custom_price"
                                           value="<?php echo stripslashes( get_post_meta( $post_id, 'woosg_custom_price', true ) ); ?>"/>
                                    E.g: <code>From $10 to $100</code>
                                </td>
                            </tr>
                            <tr class="woosg_tr_space">
                                <th><?php esc_html_e( 'Above text', 'wpc-grouped-product' ); ?></th>
                                <td>
                                    <div class="w100">
                                        <textarea
                                                name="woosg_before_text"><?php echo stripslashes( get_post_meta( $post_id, 'woosg_before_text', true ) ); ?></textarea>
                                    </div>
                                </td>
                            </tr>
                            <tr class="woosg_tr_space">
                                <th><?php esc_html_e( 'Under text', 'wpc-grouped-product' ); ?></th>
                                <td>
                                    <div class="w100">
                                        <textarea
                                                name="woosg_after_text"><?php echo stripslashes( get_post_meta( $post_id, 'woosg_after_text', true ) ); ?></textarea>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
					<?php
				}

				function product_data_li( $product, $qty = 0, $search = false ) {
					$product_id = $product->get_id();

					if ( class_exists( 'WPCleverWoopq' ) && ( get_option( '_woopq_decimal', 'no' ) === 'yes' ) ) {
						$step = '0.000001';
					} else {
						$step = 1;
					}

					$qty_input = '<input type="number" value="' . esc_attr( $qty ) . '" min="0" step="' . esc_attr( $step ) . '"/>';

					if ( $search ) {
						$remove_btn = '<span class="woosg-remove hint--left" aria-label="' . esc_html__( 'Add', 'wpc-grouped-product' ) . '">+</span>';
					} else {
						$remove_btn = '<span class="woosg-remove hint--left" aria-label="' . esc_html__( 'Remove', 'wpc-grouped-product' ) . '">×</span>';
					}

					echo '<li ' . ( ! $product->is_in_stock() ? 'class="out-of-stock"' : '' ) . ' data-id="' . esc_attr( $product_id ) . '" data-price="' . esc_attr( $product->get_price() ) . '"><span class="move"></span><span class="qty hint--right" aria-label="' . esc_html__( 'Default quantity', 'wpc-grouped-product' ) . '">' . $qty_input . '</span> <span class="data"><span class="name">' . strip_tags( $product->get_name() ) . '</span> <span class="info">' . $product->get_price_html() . '</span> ' . ( $product->is_sold_individually() ? '<span class="info">sold individually</span> ' : '' ) . '</span> <span class="type"><a href="' . get_edit_post_link( $product_id ) . '" target="_blank">' . esc_attr( $product->get_type() ) . '<br/>#' . esc_attr( $product_id ) . '</a></span> ' . $remove_btn . '</li>';
				}

				function process_product_meta( $post_id ) {
					if ( isset( $_POST['product-type'] ) && ( $_POST['product-type'] !== 'woosg' ) ) {
						delete_post_meta( $post_id, 'woosg_ids' );
					}
				}

				function process_product_meta_woosg( $post_id ) {
					if ( isset( $_POST['woosg_ids'] ) ) {
						update_post_meta( $post_id, 'woosg_ids', self::clean_ids( $_POST['woosg_ids'] ) );
					}

					if ( isset( $_POST['woosg_including_main'] ) && ( $_POST['woosg_including_main'] !== '' ) ) {
						update_post_meta( $post_id, 'woosg_including_main', sanitize_text_field( $_POST['woosg_including_main'] ) );
					} else {
						delete_post_meta( $post_id, 'woosg_including_main' );
					}

					if ( isset( $_POST['woosg_selector'] ) && ( $_POST['woosg_selector'] !== '' ) ) {
						update_post_meta( $post_id, 'woosg_selector', sanitize_text_field( $_POST['woosg_selector'] ) );
					} else {
						delete_post_meta( $post_id, 'woosg_selector' );
					}

					if ( isset( $_POST['woosg_custom_price'] ) && ( $_POST['woosg_custom_price'] !== '' ) ) {
						update_post_meta( $post_id, 'woosg_custom_price', addslashes( $_POST['woosg_custom_price'] ) );
					} else {
						delete_post_meta( $post_id, 'woosg_custom_price' );
					}

					if ( isset( $_POST['woosg_before_text'] ) && ( $_POST['woosg_before_text'] !== '' ) ) {
						update_post_meta( $post_id, 'woosg_before_text', addslashes( $_POST['woosg_before_text'] ) );
					} else {
						delete_post_meta( $post_id, 'woosg_before_text' );
					}

					if ( isset( $_POST['woosg_after_text'] ) && ( $_POST['woosg_after_text'] !== '' ) ) {
						update_post_meta( $post_id, 'woosg_after_text', addslashes( $_POST['woosg_after_text'] ) );
					} else {
						delete_post_meta( $post_id, 'woosg_after_text' );
					}
				}

				function shortcode( $attrs ) {
					$attrs = shortcode_atts( array( 'id' => null ), $attrs );

					ob_start();
					self::show_items( $attrs['id'] );

					return ob_get_clean();
				}

				function shortcode_form( $attrs ) {
					global $product;

					$attrs      = shortcode_atts( array( 'id' => null ), $attrs );
					$product_id = $attrs['id'];

					if ( ! $product_id ) {
						if ( $product ) {
							$product_id = $product->get_id();
						}
					} else {
						$product = wc_get_product( $product_id );
					}

					if ( ! $product_id || ! $product ) {
						return '';
					}

					ob_start();

					if ( $product->has_variables() ) {
						wp_enqueue_script( 'wc-add-to-cart-variation' );
					}

					self::show_items( $product_id );

					wc_get_template( 'single-product/add-to-cart/simple.php' );

					return ob_get_clean();
				}

				function add_to_cart_form() {
					global $product;

					if ( $product ) {
						$product_id = $product->get_id();
					}

					if ( ! $product_id || ! $product ) {
						return;
					}

					if ( $product->has_variables() ) {
						wp_enqueue_script( 'wc-add-to-cart-variation' );
					}

					if ( ( self::get_setting( 'position', 'above' ) === 'above' ) && apply_filters( 'woosg_show_items', true, $product_id ) ) {
						self::show_items( $product_id );
					}

					wc_get_template( 'single-product/add-to-cart/simple.php' );

					if ( ( self::get_setting( 'position', 'above' ) === 'below' ) && apply_filters( 'woosg_show_items', true, $product_id ) ) {
						self::show_items( $product_id );
					}
				}

				function add_to_cart_button() {
					global $product;

					if ( $product && $product->is_type( 'woosg' ) ) {
						echo '<input name="woosg_ids" class="woosg-ids woosg-ids-' . esc_attr( $product->get_id() ) . '" type="hidden" value="' . esc_attr( get_post_meta( $product->get_id(), 'woosg_ids', true ) ) . '"/>';
					}
				}

				function show_items( $product = null ) {
					$product_id = null;

					if ( ! $product ) {
						global $product;

						if ( $product ) {
							$product_id = $product->get_id();
						}
					} elseif ( is_numeric( $product ) ) {
						$product_id = $product;
						$product    = wc_get_product( $product_id );
					}

					if ( ! $product_id || ! $product || ! $product->is_type( 'woosg' ) ) {
						return;
					}

					// settings
					$selector = self::get_setting( 'selector', 'quantity' );

					if ( ( $_selector = get_post_meta( $product_id, 'woosg_selector', true ) ) && $_selector !== 'default' ) {
						$selector = $_selector;
					}

					$link             = self::get_setting( 'link', 'yes' ) !== 'no';
					$show_thumb       = self::get_setting( 'show_thumb', 'yes' ) !== 'no';
					$show_price       = self::get_setting( 'show_price', 'yes' ) !== 'no';
					$show_description = self::get_setting( 'show_description', 'no' ) === 'yes';
					$show_plus_minus  = self::get_setting( 'show_plus_minus', 'no' ) === 'yes';

					if ( $items = $product->get_items() ) {
						echo '<div class="woosg-wrap" data-id="' . esc_attr( $product_id ) . '">';

						do_action( 'woosg_before_wrap', $product );

						if ( $before_text = apply_filters( 'woosg_before_text', get_post_meta( $product_id, 'woosg_before_text', true ) ?: self::localization( 'above_text' ), $product_id ) ) {
							echo '<div class="woosg_before_text woosg-before-text woosg-text">' . do_shortcode( stripslashes( $before_text ) ) . '</div>';
						}

						do_action( 'woosg_before_table', $product );

						$including_main = get_post_meta( $product_id, 'woosg_including_main', true );

						if ( ( self::get_setting( 'main_price', 'zero' ) === 'zero' ) || ( ( ! $including_main || ( $including_main === 'default' ) ) && ( self::get_setting( 'including_main', 'no' ) !== 'yes' ) ) || ( $including_main === 'no' ) ) {
							$main_price         = 0;
							$main_regular_price = 0;
						} else {
							$main_price         = wc_get_price_to_display( $product );
							$main_regular_price = wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) );
						}
						?>
                        <div class="woosg-table woosg-products"
                             data-variables="<?php echo esc_attr( $product->has_variables() ? 'yes' : 'no' ); ?>"
                             data-price="<?php echo esc_attr( $main_price ); ?>"
                             data-regular-price="<?php echo esc_attr( $main_regular_price ); ?>">
							<?php
							do_action( 'woosg_before_items', $product );

							$order = 1;

							foreach ( $items as $item ) {
								$item_product = wc_get_product( $item['id'] );

								if ( ! $item_product || ( $order > 3 ) || ( $item_product->get_status() !== 'publish' && apply_filters( 'woosg_show_publish_product_only', true ) ) ) {
									continue;
								}

								$item_product_name  = apply_filters( 'woosg_item_product_name', $item_product->get_name(), $item_product, $product );
								$item_price         = apply_filters( 'woosg_item_price', wc_get_price_to_display( $item_product ), $item_product );
								$item_regular_price = apply_filters( 'woosg_item_regular_price', wc_get_price_to_display( $item_product, array( 'price' => $item_product->get_regular_price() ) ), $item_product );
								$item_class         = 'woosg-product';
								$item_qty           = $item['qty'];
								$item_id            = $item_product->is_type( 'variable' ) ? 0 : $item['id'];

								if ( $item_product->is_purchasable() && $item_product->is_in_stock() ) {
									$min = apply_filters( 'woosg_quantity_input_min', 0, $item_product );
									$max = apply_filters( 'woosg_quantity_input_max', $item_product->get_max_purchase_quantity(), $item_product );

									if ( $max < 0 ) {
										$max = 1000;
									}

									if ( $item_qty < $min ) {
										$item_qty = $min;
									}

									if ( ( $max > 0 ) && ( $item_qty > $max ) ) {
										$item_qty = $max;
									}

									if ( $item_qty && ( $selector === 'checkbox' ) ) {
										$item_qty = 1;
									}
								} else {
									$item_class         .= ' woosg-product-unpurchasable';
									$item_price         = 0;
									$item_regular_price = 0;
									$item_qty           = 0;
									$item_id            = - 1;
								}
								?>
                                <div class="<?php echo esc_attr( apply_filters( 'woosg_item_class', $item_class, $item_product, $product ) ); ?>"
                                     data-name="<?php echo esc_attr( $item_product_name ); ?>"
                                     data-id="<?php echo esc_attr( $item_id ); ?>"
                                     data-price="<?php echo esc_attr( $item_price ); ?>"
                                     data-regular-price="<?php echo esc_attr( $item_regular_price ); ?>"
                                     data-qty="<?php echo esc_attr( $item_qty ); ?>"
                                     data-order="<?php echo esc_attr( $order ); ?>">

									<?php
									do_action( 'woosg_before_item', $item_product, $product, $order );

									if ( $selector === 'checkbox' ) {
										?>
                                        <div class="woosg-choose">
											<?php if ( $item_product->is_purchasable() && $item_product->is_in_stock() && $item_qty ) {
												echo '<input class="woosg-checkbox" type="checkbox" checked/>';
											} else {
												echo '<input class="woosg-checkbox" type="checkbox"/>';
											} ?>
                                            <span class="checkmark"></span>
                                        </div>
										<?php
									}

									if ( $show_thumb ) {
										?>
                                        <div class="woosg-thumb">
											<?php
											do_action( 'woosg_before_item_thumb', $item_product, $product, $order );

											if ( $link ) {
												echo '<a class="woosg-product-link' . ( self::get_setting( 'link', 'yes' ) === 'yes_popup' ? ' woosq-link' : '' ) . '" data-id="' . $item['id'] . '" data-context="woosg" href="' . get_permalink( $item['id'] ) . '" ' . ( self::get_setting( 'link', 'yes' ) === 'yes_blank' ? 'target="_blank"' : '' ) . '>';
											} ?>
                                            <div class="woosg-thumb-ori">
												<?php echo wp_kses( apply_filters( 'woosg_item_thumbnail', $item_product->get_image( self::$image_size ), $item_product, $product, $order ), array(
													'img' => array(
														'width'       => array(),
														'height'      => array(),
														'sizes'       => array(),
														'srcset'      => array(),
														'data-src'    => array(),
														'data-srcset' => array(),
														'class'       => array(),
														'src'         => array(),
														'id'          => array(),
														'alt'         => array()
													)
												) ); ?>
                                            </div>
                                            <div class="woosg-thumb-new"></div>
											<?php if ( $link ) {
												echo '</a>';
											}

											do_action( 'woosg_after_item_thumb', $item_product, $product, $order ); ?>
                                        </div><!-- /woosg-thumb -->
										<?php
									} ?>

                                    <div class="woosg-title">
										<?php
										do_action( 'woosg_before_item_name', $item_product, $product, $order );

										echo '<div class="woosg-name woosg-title-inner">';
										$item_name = '';

										if ( $link ) {
											$item_name .= '<a class="woosg-product-link' . ( self::get_setting( 'link', 'yes' ) === 'yes_popup' ? ' woosq-link' : '' ) . '" data-id="' . $item['id'] . '" data-context="woosg" href="' . get_permalink( $item['id'] ) . '" ' . ( self::get_setting( 'link', 'yes' ) === 'yes_blank' ? 'target="_blank"' : '' ) . '>';
										}

										if ( $item_product->is_in_stock() ) {
											$item_name .= $item_product_name;
										} else {
											$item_name .= '<s>' . $item_product_name . '</s>';
										}

										if ( $link ) {
											$item_name .= '</a>';
										}

										echo wp_kses( apply_filters( 'woosg_item_name', $item_name, $item_product, $order ), array(
											'a'    => array(
												'class'   => array(),
												'data-id' => array(),
												'href'    => array(),
												'target'  => array()
											),
											'span' => array(
												'class' => array()
											),
											's'    => array()
										) );
										echo '</div>';

										do_action( 'woosg_after_item_name', $item_product, $product, $order );

										if ( $show_price ) { ?>
                                            <div class="woosg-price">
												<?php do_action( 'woosg_before_item_price', $item_product, $product, $order ); ?>
                                                <div class="woosg-price-ori">
													<?php echo $item_product->get_price_html(); ?>
                                                </div>
                                                <div class="woosg-price-new"></div>
												<?php do_action( 'woosg_after_item_price', $item_product, $product, $order ); ?>
                                            </div>
										<?php }

										if ( $show_description ) {
											echo '<div class="woosg-description">' . apply_filters( 'woosg_item_description', $item_product->get_short_description(), $item_product ) . '</div>';
										}

										if ( $item_product->is_type( 'variable' ) ) {
											$use_woovr = apply_filters( 'woosg_use_woovr', self::get_setting( 'variations_selector', 'default' ) === 'woovr', $item_product, $product );

											if ( $use_woovr && class_exists( 'WPClever_Woovr' ) ) {
												WPClever_Woovr::woovr_variations_form( $item_product );
											} else {
												$attributes           = $item_product->get_variation_attributes();
												$available_variations = $item_product->get_available_variations();
												$variations_json      = wp_json_encode( $available_variations );
												$variations_attr      = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

												if ( is_array( $attributes ) && ( count( $attributes ) > 0 ) ) {
													echo '<form class="variations_form" data-product_id="' . esc_attr( $item_product->get_id() ) . '" data-product_variations="' . esc_attr( $variations_attr ) . '">';
													echo '<div class="variations">';

													foreach ( $attributes as $attribute_name => $options ) { ?>
                                                        <div class="variation">
                                                            <div class="label">
																<?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?>
                                                            </div>
                                                            <div class="select">
																<?php
																$attr     = 'attribute_' . sanitize_title( $attribute_name );
																$selected = isset( $_REQUEST[ $attr ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ $attr ] ) ) ) : $item_product->get_variation_default_attribute( $attribute_name );

																wc_dropdown_variation_attribute_options( array(
																	'options'          => $options,
																	'attribute'        => $attribute_name,
																	'product'          => $item_product,
																	'selected'         => $selected,
																	'show_option_none' => sprintf( self::localization( 'choose', esc_html__( 'Choose %s', 'wpc-grouped-product' ) ), wc_attribute_label( $attribute_name ) )
																) );
																?>
                                                            </div>
                                                        </div>
													<?php }

													echo '<div class="reset"><a class="reset_variations" href="#">' . self::localization( 'clear', esc_html__( 'Clear', 'wpc-grouped-product' ) ) . '</a></div>';
													echo '</div>';
													echo '</form>';

													if ( $show_description ) {
														echo '<div class="woosg-variation-description"></div>';
													}
												}
											}

											echo '<div class="woosg-availability"></div>';

											do_action( 'woosg_after_item_variations', $item_product, $product, $order );
										} else {
											echo '<div class="woosg-availability">' . wc_get_stock_html( $item_product ) . '</div>';
										}
										?>
                                    </div><!-- /woosg-title -->

									<?php
									if ( $selector === 'quantity' ) {
										if ( $item_product->is_purchasable() && $item_product->is_in_stock() ) {
											echo '<div class="' . esc_attr( $show_plus_minus ? 'woosg-quantity woosg-quantity-plus-minus' : 'woosg-quantity' ) . '" data-min="' . esc_attr( $min ) . '" data-max="' . esc_attr( $max ) . '">';

											do_action( 'woosg_before_item_qty', $item_product, $product, $order );

											if ( $show_plus_minus ) {
												echo '<div class="woosg-quantity-input">';
												echo '<span class="woosg-quantity-minus">-</span>';
											}

											$qty_args = array(
												'input_value' => $item_qty,
												'min_value'   => $min,
												'max_value'   => $max,
												'woosg_qty'   => array(
													'input_value' => $item_qty,
													'min_value'   => $min,
													'max_value'   => $max
												),
												'classes'     => array(
													'input-text',
													'woosg-qty',
													'qty',
													'text'
												),
												'input_name'  => 'woosg_qty_' . $order
											);

											woocommerce_quantity_input( $qty_args, $item_product );

											if ( $show_plus_minus ) {
												echo '<span class="woosg-quantity-plus">+</span>';
												echo '</div>';
											}

											do_action( 'woosg_after_item_qty', $item_product, $product, $order );

											echo '</div><!-- /woosg-quantity -->';
										} else {
											echo '<div class="' . esc_attr( $show_plus_minus ? 'woosg-quantity woosg-quantity-plus-minus' : 'woosg-quantity' ) . '" data-min="' . esc_attr( $item_qty ) . '" data-max="' . esc_attr( $item_qty ) . '">';

											do_action( 'woosg_before_item_qty', $item_product, $product, $order );

											if ( $show_plus_minus ) {
												echo '<div class="woosg-quantity-input">';
												echo '<span class="woosg-quantity-minus">-</span>';
											}

											echo '<div class="quantity"><input type="number" class="input-text qty text" value="' . esc_attr( $item_qty ) . '" readonly/></div>';

											if ( $show_plus_minus ) {
												echo '<span class="woosg-quantity-plus">+</span>';
												echo '</div>';
											}

											do_action( 'woosg_after_item_qty', $item_product, $product, $order );

											echo '</div><!-- /woosg-quantity -->';
										}
									}

									do_action( 'woosg_after_item', $item_product, $product, $order );
									?>
                                </div><!-- /woosg-product -->
								<?php
								$order ++;
							}

							do_action( 'woosg_after_items', $product );
							?>
                        </div>
						<?php
						do_action( 'woosg_before_total', $product );

						echo '<div class="woosg_total woosg-total woosg-text"></div>';

						do_action( 'woosg_before_alert', $product );

						echo '<div class="woosg-alert woosg-text" style="display: none"></div>';

						do_action( 'woosg_after_table', $product );

						if ( $after_text = apply_filters( 'woosg_after_text', get_post_meta( $product_id, 'woosg_after_text', true ) ?: self::localization( 'under_text' ), $product_id ) ) {
							echo '<div class="woosg_after_text woosg-after-text woosg-text">' . do_shortcode( stripslashes( $after_text ) ) . '</div>';
						}

						do_action( 'woosg_after_wrap', $product );

						echo '</div>';
					}
				}

				function get_price_html( $price, $product ) {
					if ( $product->is_type( 'woosg' ) ) {
						$product_id   = $product->get_id();
						$custom_price = get_post_meta( $product_id, 'woosg_custom_price', true );

						if ( ! empty( $custom_price ) ) {
							return $custom_price;
						}

						switch ( self::get_setting( 'price_format', 'from' ) ) {
							case 'none':
								return '';
							case 'from':
								return self::localization( 'from', esc_html__( 'From', 'wpc-grouped-product' ) ) . ' ' . wc_price( $product->get_price() );
							case 'auto':
								$regular_price = $sale_price = 0;

								if ( $ids = get_post_meta( $product_id, 'woosg_ids', true ) ) {
									if ( $items = self::get_items( $ids ) ) {
										foreach ( $items as $item ) {
											$item_product = wc_get_product( $item['id'] );

											if ( $item_product ) {
												if ( $item_product->is_type( 'variable' ) ) {
													$regular_price += wc_get_price_to_display( $item_product, array(
														'price' => $item_product->get_variation_regular_price(),
														'qty'   => $item['qty']
													) );

													if ( $item_sale_price = $item_product->get_variation_sale_price() ) {
														$sale_price += wc_get_price_to_display( $item_product, array(
															'price' => $item_sale_price,
															'qty'   => $item['qty']
														) );
													} else {
														$sale_price += wc_get_price_to_display( $item_product, array(
															'price' => $item_product->get_variation_regular_price(),
															'qty'   => $item['qty']
														) );
													}
												} else {
													$regular_price += wc_get_price_to_display( $item_product, array(
														'price' => $item_product->get_regular_price(),
														'qty'   => $item['qty']
													) );

													if ( $item_sale_price = $item_product->get_sale_price() ) {
														$sale_price += wc_get_price_to_display( $item_product, array(
															'price' => $item_sale_price,
															'qty'   => $item['qty']
														) );
													} else {
														$sale_price += wc_get_price_to_display( $item_product, array(
															'price' => $item_product->get_regular_price(),
															'qty'   => $item['qty']
														) );
													}
												}
											}
										}

										if ( $sale_price && ( $sale_price < $regular_price ) ) {
											return wc_format_sale_price( wc_price( $regular_price ), wc_price( $sale_price ) );
										} else {
											return wc_price( $regular_price );
										}
									}
								}
						}
					}

					return $price;
				}

				function product_price_class( $class ) {
					global $product;

					if ( $product && $product->is_type( 'woosg' ) ) {
						$class .= ' woosg-price-' . $product->get_id();
					}

					return $class;
				}

				function display_post_states( $states, $post ) {
					if ( ( 'product' == get_post_type( $post->ID ) ) && ( $product = wc_get_product( $post->ID ) ) && $product->is_type( 'woosg' ) ) {
						$count = 0;

						if ( $ids = get_post_meta( $post->ID, 'woosg_ids', true ) ) {
							if ( $items = self::get_items( $ids ) ) {
								$count = count( $items );
							}
						}

						$states[] = apply_filters( 'woosg_post_states', '<span class="woosg-state">' . sprintf( esc_html__( 'Grouped (%s)', 'wpc-grouped-product' ), $count ) . '</span>', $count, $product );
					}

					return $states;
				}

				function get_items( $ids ) {
					$data = array();
					$ids  = self::clean_ids( $ids );

					if ( ! empty( $ids ) ) {
						$items = explode( ',', $ids );

						if ( is_array( $items ) && count( $items ) > 0 ) {
							foreach ( $items as $item ) {
								$item_data = explode( '/', $item );
								$data[]    = array(
									'id'    => apply_filters( 'woosg_item_id', absint( isset( $item_data[0] ) ? $item_data[0] : 0 ) ),
									'qty'   => apply_filters( 'woosg_item_qty', (float) ( isset( $item_data[1] ) ? $item_data[1] : 0 ) ),
									'attrs' => isset( $item_data[2] ) ? (array) json_decode( rawurldecode( $item_data[2] ) ) : array()
								);
							}
						}
					}

					if ( count( $data ) > 0 ) {
						return apply_filters( 'woosg_get_items', $data );
					}

					return false;
				}

				function clean_ids( $ids ) {
					return apply_filters( 'woosg_clean_ids', $ids );
				}

				function wpml_item_id( $id ) {
					return apply_filters( 'wpml_object_id', $id, 'product', true );
				}
			}

			return WPCleverWoosg::instance();
		}
	}
}

if ( ! function_exists( 'woosg_notice_wc' ) ) {
	function woosg_notice_wc() {
		?>
        <div class="error">
            <p><strong>WPC Grouped Product</strong> requires WooCommerce version 3.0 or greater.</p>
        </div>
		<?php
	}
}
