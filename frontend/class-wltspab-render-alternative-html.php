<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WLTSPAB_RENDER_ALTERNATIVE_HTML' ) ) :

/**
 * @class WLTSPAB_RENDER_ALTERNATIVE_HTML
 * @version	1.0.0
 */
class WLTSPAB_RENDER_ALTERNATIVE_HTML {
	
	/**
	 * The single instance of the class.
	 *
	 * @var WLTSPAB_RENDER_ALTERNATIVE_HTML
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	
	/**
	 * Main WLTSPAB_RENDER_ALTERNATIVE_HTML Instance.
	 *
	 * Ensures only one instance of WLTSPAB_RENDER_ALTERNATIVE_HTML is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WLTSPAB_RENDER_ALTERNATIVE_HTML()
	 * @return WLTSPAB_RENDER_ALTERNATIVE_HTML - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	
	/**
	 * WLTSPAB_RENDER_ALTERNATIVE_HTML Constructor.
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 * @since  1.0.0
	 */
	private function init_hooks() {
		/**
		 * Enqueue Scripts And Styles On Frontend.
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
		/**
		 * Render Alternative HTML For Hidden Add To Cart.
		 */
		add_action( 'woo_login_to_see_price_and_buy_alternative_of_add_to_cart', array( $this, 'woo_login_to_see_price_and_buy_alternative_of_add_to_cart' ), 5 );
		/**
		 * Render Alternative HTML For Hidden Price.
		 */
		add_action( 'woo_login_to_see_price_and_buy_alternative_of_product_price', array( $this, 'woo_login_to_see_price_and_buy_alternative_of_product_price' ), 5 );
	}

	function enqueue_scripts() {
		if( !is_admin() && ( is_shop() || is_product() ) ) {
			wp_register_style( 'wltspab_woo_frontend_style', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_PLUGIN_DIR_URL.'/assets/css/wltspab-woo-frontend.css', array(), WOO_LOGIN_TO_SEE_PRICE_AND_BUY_VERSION );
			wp_enqueue_style( 'wltspab_woo_frontend_style' );
		}
	}

	function woo_login_to_see_price_and_buy_alternative_of_add_to_cart() {
		$custom_class = 'wltspab_altr_add_to_cart';
		$alternative_hide_add_to_cart_color = get_option( 'wltspab_settings_alternative_to_add_to_cart_color', '#dd0d0d' );
		$alternative_hide_add_to_cart  = get_option( 'wltspab_settings_alternative_to_add_to_cart', '' );
		$alternative_hide_add_to_cart = trim( $alternative_hide_add_to_cart, ' ' );

		if( !$alternative_hide_add_to_cart ) { return; }

		$alternative_hide_add_to_cart = explode( '||', $alternative_hide_add_to_cart );
		
		ob_start();
		echo '<div class="wltspab_wrapper_div">';
		do_action( 'before_woo_login_to_see_price_and_buy_alternative_of_add_to_cart' );
		foreach ($alternative_hide_add_to_cart as $textValue) {
			$textValue = trim( $textValue, ' ' );
			if( $textValue == '{{*wltspab_login_link}}' ) {
				echo $this->get_login_link();
			}
			else {
				echo '<span class="'.$custom_class.'" style="color:'.$alternative_hide_add_to_cart_color.'">';
					echo $textValue;
				echo '</span>';
			}
		}
		do_action( 'after_woo_login_to_see_price_and_buy_alternative_of_add_to_cart' );
		echo '</div>';
		$html_to_render = ob_get_clean();

		$html_to_render = apply_filters( 'woo_login_to_see_price_and_buy_alter_text_in_place_of_add_to_cart', $html_to_render );
		echo $html_to_render;
	}

	function woo_login_to_see_price_and_buy_alternative_of_product_price() {
		$custom_class = 'wltspab_altr_price';
		$alternative_hide_price_color = get_option( 'wltspab_settings_alternative_to_price_color', '#dd0d0d' );
		$alternative_hide_price  = get_option( 'wltspab_settings_alternative_to_price', '' );
		$alternative_hide_price = trim( $alternative_hide_price, ' ' );

		if( !$alternative_hide_price ) { return; }

		$alternative_hide_price = explode( '||', $alternative_hide_price );

		ob_start();
		echo '<div class="wltspab_wrapper_div">';
		do_action( 'before_woo_login_to_see_price_and_buy_alternative_of_product_price' );
		foreach ($alternative_hide_price as $textValue) {
			$textValue = trim( $textValue, ' ' );
			if( $textValue == '{{*wltspab_login_link}}' ) {
				echo $this->get_login_link();
			}
			else {
				echo '<span class="'.$custom_class.'" style="color:'.$alternative_hide_price_color.'">';
					echo $textValue;
				echo '</span>';
			}
		}
		do_action( 'after_woo_login_to_see_price_and_buy_alternative_of_product_price' );
		echo '</div>';
		$html_to_render = ob_get_clean();

		$html_to_render = apply_filters( 'woo_login_to_see_price_and_buy_alter_text_in_place_of_product_price', $html_to_render );
		echo $html_to_render;
	}

	/**
	 * Render Customize Login Link.
	 */
	function get_login_link() {
		$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
		( $myaccount_page_id ) ? $myaccount_page_url = get_permalink( $myaccount_page_id ) : $myaccount_page_url = '';
		
		$login_text = get_option( 'wltspab_settings_custom_login_link_text', 'Login First' );
		$login_url = get_option( 'wltspab_settings_custom_login_link_url', $myaccount_page_url );

		$login_link_class = 'button wltspab_custom_login_link';
		$login_link_class = apply_filters( 'woo_login_to_see_price_and_buy_alter_custom_login_link_classes', $login_link_class );

		$login_link = '<a class="'.$login_link_class.'" href="'.$login_url.'">'.$login_text.'</a>';
		$login_link = apply_filters( 'woo_login_to_see_price_and_buy_alter_custom_login_link', $login_link, $login_text, $login_url );

		return $login_link;
	}

}

endif;

/**
 * Main instance of WLTSPAB_RENDER_ALTERNATIVE_HTML.
 * @since  1.0.0
 * @return WLTSPAB_RENDER_ALTERNATIVE_HTML
 */
WLTSPAB_RENDER_ALTERNATIVE_HTML::instance();
?>