<?php
/**
 * Plugin Name: Woo Login To See Price And Buy
 * Plugin URI: http://simplerthansimplest.pe.hu/
 * Description: WooCommerce extension to hide product price and add to cart button for guest customers, forcing them to get registered with you.
 * Version: 1.0.0
 * Author: SimplerThanSimplest
 * Author URI: http://simplerthansimplest.pe.hu/
 * Requires at least: 4.0
 * Tested up to: 4.6.1
 *
 * Text Domain: woo-login-to-see-price-and-buy
 * Domain Path: /i18n/languages/
 *
 * @package WOO_LOGIN_TO_SEE_PRICE_AND_BUY
 * @category Core
 * @author SIMPLERTHANSIMPLEST
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WOO_LOGIN_TO_SEE_PRICE_AND_BUY' ) ) :

/**
 * Main WOO_LOGIN_TO_SEE_PRICE_AND_BUY Class.
 *
 * @class WOO_LOGIN_TO_SEE_PRICE_AND_BUY
 * @version	1.0.0
 */
class WOO_LOGIN_TO_SEE_PRICE_AND_BUY {

	/**
	 * WOO_LOGIN_TO_SEE_PRICE_AND_BUY version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var WOO_LOGIN_TO_SEE_PRICE_AND_BUY
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	
	/**
	 * Main WOO_LOGIN_TO_SEE_PRICE_AND_BUY Instance.
	 *
	 * Ensures only one instance of WOO_LOGIN_TO_SEE_PRICE_AND_BUY is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see INSTANTIATE_WOO_LOGIN_TO_SEE_PRICE_AND_BUY()
	 * @return WOO_LOGIN_TO_SEE_PRICE_AND_BUY - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	
	/**
	 * WOO_LOGIN_TO_SEE_PRICE_AND_BUY Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'woo_login_to_see_price_and_buy_loaded' );
	}

	/**
	 * Hook into actions and filters.
	 * @since  1.0.0
	 */
	private function init_hooks() {
		add_filter( 'plugin_action_links_'.WOO_LOGIN_TO_SEE_PRICE_AND_BUY_PLUGIN_BASENAME, array( $this, 'alter_plugin_action_links' ) );
		add_filter( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 5 );
	}

	function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$screen = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if( $screen_id == 'woocommerce_page_wc-settings' && isset($_GET['section']) && $_GET['section'] == 'wltspab_settings' ) {
			$footer_text = 'Thanks for using <b>Woo Login To See Price And Buy</b>.';
		}
		return $footer_text;
	}

	/**
	 * Define WOO_LOGIN_TO_SEE_PRICE_AND_BUY Constants.
	 */
	private function define_constants() {
		$this->define( 'WOO_LOGIN_TO_SEE_PRICE_AND_BUY_PLUGIN_FILE', __FILE__ );
		$this->define( 'WOO_LOGIN_TO_SEE_PRICE_AND_BUY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'WOO_LOGIN_TO_SEE_PRICE_AND_BUY_VERSION', $this->version );
		$this->define( 'WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN', 'woo-login-to-see-price-and-buy' );
		$this->define( 'WOO_LOGIN_TO_SEE_PRICE_AND_BUY_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'WOO_LOGIN_TO_SEE_PRICE_AND_BUY_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	
	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		include_once( 'admin/class-wltspab-render-admin-settings.php' );
		include_once( 'frontend/class-wltspab-hide-product-add-to-cart.php' );
		include_once( 'frontend/class-wltspab-hide-product-price.php' );
		include_once( 'frontend/class-wltspab-render-alternative-html.php' );
	}


	function alter_plugin_action_links( $plugin_links ) {
		$settings_link = '<a href="admin.php?page=wc-settings&tab=products&section=wltspab_settings">Settings</a>';
		array_unshift( $plugin_links, $settings_link );
		return $plugin_links;
	}
	
	/**
	 * Load Localisation files.
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'WOO_LOGIN_TO_SEE_PRICE_AND_BUY_plugin_locale', get_locale(), WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN );
		load_textdomain( WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN, WOO_LOGIN_TO_SEE_PRICE_AND_BUY_PLUGIN_DIR_PATH .'language/'.WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN.'-' . $locale . '.mo' );
		load_plugin_textdomain( WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . '/language' );
	}

}

endif;

/**
 * Main instance of WOO_LOGIN_TO_SEE_PRICE_AND_BUY.
 *
 * Returns the main instance of WOO_LOGIN_TO_SEE_PRICE_AND_BUY to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WOO_LOGIN_TO_SEE_PRICE_AND_BUY
 */
function INSTANTIATE_WOO_LOGIN_TO_SEE_PRICE_AND_BUY() {
	return WOO_LOGIN_TO_SEE_PRICE_AND_BUY::instance();
}

// Global for backwards compatibility.
$GLOBALS['woo_login_to_see_price_and_buy'] = INSTANTIATE_WOO_LOGIN_TO_SEE_PRICE_AND_BUY();
?>