<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WLTSPAB_HIDE_PRODUCT_PRICE' ) ) :

/**
 * @class WLTSPAB_HIDE_PRODUCT_PRICE
 * @version	1.0.0
 */
class WLTSPAB_HIDE_PRODUCT_PRICE {
	
	/**
	 * The single instance of the class.
	 *
	 * @var WLTSPAB_HIDE_PRODUCT_PRICE
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	
	/**
	 * Main WLTSPAB_HIDE_PRODUCT_PRICE Instance.
	 *
	 * Ensures only one instance of WLTSPAB_HIDE_PRODUCT_PRICE is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WLTSPAB_HIDE_PRODUCT_PRICE()
	 * @return WLTSPAB_HIDE_PRODUCT_PRICE - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	
	/**
	 * WLTSPAB_HIDE_PRODUCT_PRICE Constructor.
	 */
	public function __construct() {
		$enable_hide_price  = get_option( 'wltspab_settings_enable_hide_price', false );
		if( $enable_hide_price == 'yes' ) {
			$this->init_hooks();
		} 
	}

	/**
	 * Hook into actions and filters.
	 * @since  1.0.0
	 */
	private function init_hooks() {
		/**
		 * Remove Price From Shop Page.
		 */
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'remove_woocommerce_template_loop_price' ), 9.99 );
		/**
		 * Remove Price From Single Page Of All Product Type.
		 */
		add_action( 'woocommerce_single_product_summary', array( $this, 'remove_woocommerce_template_single_price' ), 9.99 );
		/**
		 * Remove Price From Variable Product Single Page During Variation Change.
		 */
		add_filter( 'woocommerce_available_variation', array( $this, 'remove_woocommerce_available_variation_price' ), 5, 3 );
	}

	function remove_woocommerce_template_loop_price() {
		$is_user_logged_in = is_user_logged_in();
		$is_user_logged_in = apply_filters( 'woo_login_to_see_price_and_buy_alter_is_user_logged_in', $is_user_logged_in );
		if( !$is_user_logged_in ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			do_action( 'woo_login_to_see_price_and_buy_alternative_of_product_price' );
		}
	}

	function remove_woocommerce_template_single_price() {
		$is_user_logged_in = is_user_logged_in();
		$is_user_logged_in = apply_filters( 'woo_login_to_see_price_and_buy_alter_is_user_logged_in', $is_user_logged_in );
		if( !$is_user_logged_in ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			do_action( 'woo_login_to_see_price_and_buy_alternative_of_product_price' );
		}
	}

	function remove_woocommerce_available_variation_price( $available_variation_data, $_product, $variation ) {
		$is_user_logged_in = is_user_logged_in();
		$is_user_logged_in = apply_filters( 'woo_login_to_see_price_and_buy_alter_is_user_logged_in', $is_user_logged_in );
		if( !$is_user_logged_in ) {
			$available_variation_data['price_html'] = '';
			$available_variation_data = apply_filters( 'woo_login_to_see_price_and_buy_alter_available_variation_data', $available_variation_data, $_product, $variation );
		}
		return $available_variation_data;
	}
	
}

endif;

/**
 * Main instance of WLTSPAB_HIDE_PRODUCT_PRICE.
 * @since  1.0.0
 * @return WLTSPAB_HIDE_PRODUCT_PRICE
 */
WLTSPAB_HIDE_PRODUCT_PRICE::instance();
?>