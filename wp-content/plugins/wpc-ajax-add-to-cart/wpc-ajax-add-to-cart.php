<?php
/**
 * Plugin Name: WPC AJAX Add to Cart for WooCommerce
 * Plugin URI: https://wpclever.net/
 * Description: AJAX add to cart for WooCommerce products.
 * Version: 1.5.5
 * Author: WPClever
 * Author URI: https://wpclever.net
 * Text Domain: wpc-ajax-add-to-cart
 * Domain Path: /languages/
 * Requires at least: 4.0
 * Tested up to: 6.1
 * WC requires at least: 3.0
 * WC tested up to: 7.1
 */

defined( 'ABSPATH' ) || exit;

! defined( 'WOOAA_VERSION' ) && define( 'WOOAA_VERSION', '1.5.5' );
! defined( 'WOOAA_URI' ) && define( 'WOOAA_URI', plugin_dir_url( __FILE__ ) );
! defined( 'WOOAA_REVIEWS' ) && define( 'WOOAA_REVIEWS', 'https://wordpress.org/support/plugin/wpc-ajax-add-to-cart/reviews/?filter=5' );
! defined( 'WOOAA_CHANGELOG' ) && define( 'WOOAA_CHANGELOG', 'https://wordpress.org/plugins/wpc-ajax-add-to-cart/#developers' );
! defined( 'WOOAA_DISCUSSION' ) && define( 'WOOAA_DISCUSSION', 'https://wordpress.org/support/plugin/wpc-ajax-add-to-cart/' );
! defined( 'WPC_URI' ) && define( 'WPC_URI', WOOAA_URI );

include 'includes/wpc-dashboard.php';
include 'includes/wpc-menu.php';
include 'includes/wpc-kit.php';

if ( ! class_exists( 'WPCleverWooaa' ) ) {
	class WPCleverWooaa {
		protected static $instance = null;
		protected static $settings = array();

		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function __construct() {
			self::$settings = (array) get_option( 'wooaa_settings', [] );

			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 99 );
			add_action( 'admin_init', [ $this, 'register_settings' ] );
			add_action( 'admin_menu', [ $this, 'admin_menu' ] );
			add_action( 'wp_ajax_wooaa_add_to_cart_variable', [ $this, 'add_to_cart_variable' ] );
			add_action( 'wp_ajax_nopriv_wooaa_add_to_cart_variable', [ $this, 'add_to_cart_variable' ] );
			add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );
			add_filter( 'plugin_row_meta', [ $this, 'row_meta' ], 10, 2 );
		}

		function enqueue_scripts() {
			wp_enqueue_script( 'wooaa-frontend', WOOAA_URI . 'assets/js/frontend.js', array(
				'jquery',
				'wc-add-to-cart'
			), WOOAA_VERSION, true );
			wp_localize_script( 'wooaa-frontend', 'wooaa_vars', array(
					'ajax_url'      => admin_url( 'admin-ajax.php' ),
					'product_types' => implode( ',', (array) self::get_setting( 'product_types', array( 'all' ) ) )
				)
			);
		}

		function register_settings() {
			// settings
			register_setting( 'wooaa_settings', 'wooaa_settings' );
		}

		function admin_menu() {
			add_submenu_page( 'wpclever', 'WPC AJAX Add to Cart', 'AJAX Add to Cart', 'manage_options', 'wpclever-wooaa', array(
				$this,
				'admin_menu_content'
			) );
		}

		function admin_menu_content() {
			$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'settings';
			?>
            <div class="wpclever_settings_page wrap">
                <h1 class="wpclever_settings_page_title"><?php echo 'WPC AJAX Add to Cart ' . WOOAA_VERSION; ?></h1>
                <div class="wpclever_settings_page_desc about-text">
                    <p>
						<?php printf( esc_html__( 'Thank you for using our plugin! If you are satisfied, please reward it a full five-star %s rating.', 'wpc-ajax-add-to-cart' ), '<span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span>' ); ?>
                        <br/>
                        <a href="<?php echo esc_url( WOOAA_REVIEWS ); ?>"
                           target="_blank"><?php esc_html_e( 'Reviews', 'wpc-ajax-add-to-cart' ); ?></a> | <a
                                href="<?php echo esc_url( WOOAA_CHANGELOG ); ?>"
                                target="_blank"><?php esc_html_e( 'Changelog', 'wpc-ajax-add-to-cart' ); ?></a>
                        | <a href="<?php echo esc_url( WOOAA_DISCUSSION ); ?>"
                             target="_blank"><?php esc_html_e( 'Discussion', 'wpc-ajax-add-to-cart' ); ?></a>
                    </p>
                </div>
				<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) { ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php esc_html_e( 'Settings updated.', 'wpc-ajax-add-to-cart' ); ?></p>
                    </div>
				<?php } ?>
                <div class="wpclever_settings_page_nav">
                    <h2 class="nav-tab-wrapper">
                        <a href="<?php echo admin_url( 'admin.php?page=wpclever-wooaa&tab=settings' ); ?>"
                           class="<?php echo esc_attr( $active_tab === 'settings' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
							<?php esc_html_e( 'Settings', 'wpc-ajax-add-to-cart' ); ?>
                        </a>
                        <a href="<?php echo admin_url( 'admin.php?page=wpclever-kit' ); ?>" class="nav-tab">
							<?php esc_html_e( 'Essential Kit', 'wpc-ajax-add-to-cart' ); ?>
                        </a>
                    </h2>
                </div>
                <div class="wpclever_settings_page_content">
					<?php if ( $active_tab === 'settings' ) { ?>
                        <form method="post" action="options.php">
                            <table class="form-table">
                                <tr class="heading">
                                    <th colspan="2">
										<?php esc_html_e( 'General', 'wpc-ajax-add-to-cart' ); ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Enable for product types', 'wpc-ajax-add-to-cart' ); ?></th>
                                    <td>
										<?php
										$product_types = (array) self::get_setting( 'product_types', array( 'all' ) );
										$types         = array_merge( array( 'all' => esc_html__( 'All', 'wpc-ajax-add-to-cart' ) ), wc_get_product_types() );

										echo '<select name="wooaa_settings[product_types][]" multiple style="width: 200px; height: 150px;">';

										foreach ( $types as $key => $name ) {
											echo '<option value="' . esc_attr( $key ) . '" ' . ( in_array( $key, $product_types, true ) ? 'selected' : '' ) . '>' . esc_html( $name ) . '</option>';
										}

										echo '</select>';
										?>
                                    </td>
                                </tr>
                                <tr class="submit">
                                    <th colspan="2">
										<?php settings_fields( 'wooaa_settings' ); ?>
										<?php submit_button(); ?>
                                    </th>
                                </tr>
                            </table>
                        </form>
					<?php } ?>
                </div>
            </div>
			<?php
		}

		function add_to_cart_variable() {
			if ( ! isset( $_POST['product_id'] ) ) {
				return;
			}

			$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
			$product           = wc_get_product( $product_id );
			$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
			$product_status    = get_post_status( $product_id );
			$variation_id      = $_POST['variation_id'];
			$variation         = $_POST['variation'];

			if ( $product && 'variation' === $product->get_type() ) {
				$variation_id = $product_id;
				$product_id   = $product->get_parent_id();

				if ( empty( $variation ) ) {
					$variation = $product->get_variation_attributes();
				}
			}

			if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );

				if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
					wc_add_to_cart_message( array( $product_id => $quantity ), true );
				}

				WC_AJAX::get_refreshed_fragments();
			} else {
				$data = array(
					'error'       => true,
					'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
				);

				wp_send_json( $data );
			}

			die();
		}

		function action_links( $links, $file ) {
			static $plugin;

			if ( ! isset( $plugin ) ) {
				$plugin = plugin_basename( __FILE__ );
			}

			if ( $plugin === $file ) {
				$settings = '<a href="' . admin_url( 'admin.php?page=wpclever-wooaa&tab=settings' ) . '">' . esc_html__( 'Settings', 'wpc-ajax-add-to-cart' ) . '</a>';
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
					'support' => '<a href="' . esc_url( WOOAA_DISCUSSION ) . '" target="_blank">' . esc_html__( 'Community support', 'wpc-ajax-add-to-cart' ) . '</a>',
				);

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

		public static function get_settings() {
			return apply_filters( 'wooaa_get_settings', self::$settings );
		}

		public static function get_setting( $name, $default = false ) {
			if ( ! empty( self::$settings ) && isset( self::$settings[ $name ] ) ) {
				$setting = self::$settings[ $name ];
			} else {
				$setting = get_option( 'wooaa_' . $name, $default );
			}

			return apply_filters( 'wooaa_get_setting', $setting, $name, $default );
		}
	}

	return WPCleverWooaa::instance();
}
