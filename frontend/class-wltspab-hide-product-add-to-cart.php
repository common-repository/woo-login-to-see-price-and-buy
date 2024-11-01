<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WLTSPAB_HIDE_PRODUCT_ADD_TO_CART' ) ) :

/**
 * @class WLTSPAB_HIDE_PRODUCT_ADD_TO_CART
 * @version	1.0.0
 */
class WLTSPAB_HIDE_PRODUCT_ADD_TO_CART {
	
	/**
	 * The single instance of the class.
	 *
	 * @var WLTSPAB_HIDE_PRODUCT_ADD_TO_CART
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	
	/**
	 * Main WLTSPAB_HIDE_PRODUCT_ADD_TO_CART Instance.
	 *
	 * Ensures only one instance of WLTSPAB_HIDE_PRODUCT_ADD_TO_CART is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WLTSPAB_HIDE_PRODUCT_ADD_TO_CART()
	 * @return WLTSPAB_HIDE_PRODUCT_ADD_TO_CART - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	
	/**
	 * WLTSPAB_HIDE_PRODUCT_ADD_TO_CART Constructor.
	 */
	public function __construct() {
		$enable_hide_add_to_cart  = get_option( 'wltspab_settings_enable_hide_add_to_cart', false );
		if( $enable_hide_add_to_cart == 'yes' ) {
			$this->init_hooks();
		} 
	}

	/**
	 * Hook into actions and filters.
	 * @since  1.0.0
	 */
	private function init_hooks() {
		/**
		 * Remove Add To Cart From Shop Page.
		 */
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'remove_woocommerce_template_loop_add_to_cart' ), 9.99 );
		/**
		 * Remove Add To Cart From Single Page Of All Product Type .
		 */
		add_action( 'woocommerce_simple_add_to_cart', array( $this, 'remove_woocommerce_simple_add_to_cart' ), 29.99 );
		add_action( 'woocommerce_grouped_add_to_cart', array( $this, 'remove_remove_woocommerce_grouped_add_to_cart' ), 29.99 );
		add_action( 'woocommerce_single_variation', array( $this, 'remove_woocommerce_single_variation_add_to_cart_button' ), 19.99 );
		add_action( 'woocommerce_external_add_to_cart', array( $this, 'remove_woocommerce_external_add_to_cart' ), 29.99 );
	}

	function remove_woocommerce_template_loop_add_to_cart() {
		$is_user_logged_in = is_user_logged_in();
		$is_user_logged_in = apply_filters( 'woo_login_to_see_price_and_buy_alter_is_user_logged_in', $is_user_logged_in );
		if( !$is_user_logged_in ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			do_action( 'woo_login_to_see_price_and_buy_alternative_of_add_to_cart' );
		}
	}

	function remove_woocommerce_simple_add_to_cart() {
		$is_user_logged_in = is_user_logged_in();
		$is_user_logged_in = apply_filters( 'woo_login_to_see_price_and_buy_alter_is_user_logged_in', $is_user_logged_in );
		if( !$is_user_logged_in ) {
			remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
			do_action( 'woo_login_to_see_price_and_buy_alternative_of_add_to_cart' );
		}
	}

	function remove_woocommerce_grouped_add_to_cart() {
		$is_user_logged_in = is_user_logged_in();
		$is_user_logged_in = apply_filters( 'woo_login_to_see_price_and_buy_alter_is_user_logged_in', $is_user_logged_in );
		if( !$is_user_logged_in ) {
			remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
			do_action( 'woo_login_to_see_price_and_buy_alternative_of_add_to_cart' );
		}
	}

	function remove_woocommerce_single_variation_add_to_cart_button() {
		$is_user_logged_in = is_user_logged_in();
		$is_user_logged_in = apply_filters( 'woo_login_to_see_price_and_buy_alter_is_user_logged_in', $is_user_logged_in );
		if( !$is_user_logged_in ) {
			remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
			do_action( 'woo_login_to_see_price_and_buy_alternative_of_add_to_cart' );
		}
	}

	function remove_woocommerce_external_add_to_cart() {
		$is_user_logged_in = is_user_logged_in();
		$is_user_logged_in = apply_filters( 'woo_login_to_see_price_and_buy_alter_is_user_logged_in', $is_user_logged_in );
		if( !$is_user_logged_in ) {
			remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
			do_action( 'woo_login_to_see_price_and_buy_alternative_of_add_to_cart' );
		}
	}
	
}

endif;

/**
 * Main instance of WLTSPAB_HIDE_PRODUCT_ADD_TO_CART.
 * @since  1.0.0
 * @return WLTSPAB_HIDE_PRODUCT_ADD_TO_CART
 */
WLTSPAB_HIDE_PRODUCT_ADD_TO_CART::instance();
?>