<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WLTSPAB_RENDER_ADMIN_SETTINGS' ) ) :

/**
 * @class WLTSPAB_RENDER_ADMIN_SETTINGS
 * @version	1.0.0
 */
class WLTSPAB_RENDER_ADMIN_SETTINGS {
	
	/**
	 * The single instance of the class.
	 *
	 * @var WLTSPAB_RENDER_ADMIN_SETTINGS
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	
	/**
	 * Main WLTSPAB_RENDER_ADMIN_SETTINGS Instance.
	 *
	 * Ensures only one instance of WLTSPAB_RENDER_ADMIN_SETTINGS is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WLTSPAB_RENDER_ADMIN_SETTINGS()
	 * @return WLTSPAB_RENDER_ADMIN_SETTINGS - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	
	/**
	 * WLTSPAB_RENDER_ADMIN_SETTINGS Constructor.
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
		 * Enqueue Scripts And Styles On Admin Settings Page.
		 */
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
		/**
		 * Making Section And Render Setting Under WooCommerce>>Settings>>Products.
		 */
		add_action( 'woocommerce_get_sections_products', array( $this, 'add_section_to_woocommerce_products_sections' ), 5 );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'woocommerce_get_settings_products_for_wltspab_settings' ), 5, 2 );
		/**
		 * Adding Options To Delete On Deactivation.
		 */
		add_filter( 'woo_login_to_see_price_and_buy_alter_options_to_delete', array( $this, 'woo_login_to_see_price_and_buy_adding_options_to_delete' ), 5, 1 );
		
	}

	function enqueue_scripts() {
		$screen = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if( $screen_id == 'woocommerce_page_wc-settings' && isset($_GET['section']) && $_GET['section'] == 'wltspab_settings' ) {
			wp_register_script( 'wltspab_admin_settings_script', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_PLUGIN_DIR_URL.'/assets/js/wltspab-admin-settings.js', array( 'jquery', 'wp-color-picker' ), WOO_LOGIN_TO_SEE_PRICE_AND_BUY_VERSION, true );
			wp_localize_script( 'wltspab_admin_settings_script', 'wltspab_admin_settings_params', array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
				) );
			wp_enqueue_script( 'wltspab_admin_settings_script' );

			wp_register_style( 'wltspab_admin_settings_style', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_PLUGIN_DIR_URL.'/assets/css/wltspab-admin-settings.css', array(), WOO_LOGIN_TO_SEE_PRICE_AND_BUY_VERSION );
			wp_enqueue_style( 'wltspab_admin_settings_style' );
		}
	}

	function add_section_to_woocommerce_products_sections( $sections ) {
		$sections['wltspab_settings'] = __( 'Login To See Price And Buy', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN );
		return $sections;
	}

	function woocommerce_get_settings_products_for_wltspab_settings( $settings, $current_section ) {
		if( $current_section == 'wltspab_settings' ) {

			$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
			( $myaccount_page_id ) ? $myaccount_page_url = get_permalink( $myaccount_page_id ) : $myaccount_page_url = '';
			
			$settings = apply_filters( 'woocommerce_products_wltspab_settings', array(
				
				/**
				 * Render Setting For Hide Price.
				 */
				'wltspab_settings_hide_price_options_start'	=> array(
					'title'		=> __( 'Hide Price', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'type' 		=> 'title',
					'desc'		=> __( 'Allows to hide product-price for guest customer. You can also show customized text instead of product-price to guest customer. Enter shortcode : {{*wltspab_login_link}} in description to render login link. Use || in case you want to use both text and shortcode to separate them, like : Please login to see price. || {{*wltspab_login_link}}', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'id' 		=> 'wltspab_settings_hide_price_options_start'
				),

				'wltspab_settings_enable_hide_price'	=> array(
					'title'   => __( 'Enable Hide Price', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'desc'    => __( 'Enable product-price to hide for guest customer', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'id'      => 'wltspab_settings_enable_hide_price',
					'default' => 'no',
					'type'    => 'checkbox'
				),

				'wltspab_settings_alternative_to_price'	=> array(
					'title'       => __( 'Description In Place Of Price', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'type'        => 'textarea',
					'desc' => __( 'Description that the guest customer will see in place of price. You and even use your own HTML here. Enter shortcode : {{*wltspab_login_link}} in description to render login link.', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'id'      => 'wltspab_settings_alternative_to_price',
					'default'     => __( 'Please login to see price.', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'desc_tip'    => true,
					'class'		=> 'input-text wide-input',
				),

				'wltspab_settings_alternative_to_price_color'		=> array (
					'title'       => __( 'Color For Description', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'desc' => __( 'The description in place of price will appear in the selected color to guest customer. The color will not be applicable in case of HTML used above.', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'id'		=> 'wltspab_settings_alternative_to_price_color',
					'default'     => '#dd0d0d',
					'class'		=> 'wltspab_settings_color_picker',
					'css'		=> 'width:80px;',
					'type'		=> 'text',
					'desc_tip'		=> true 
				),

				'wltspab_settings_hide_price_options_end'	=> array(
					'type' 	=> 'sectionend',
					'id' 	=> 'wltspab_settings_hide_price_options_end',
				),

				/**
				 * Render Setting For Hide Add To Cart.
				 */
				'wltspab_settings_hide_add_to_cart_options_start'	=> array(
					'title'		=> __( 'Hide Add To Cart', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'type' 		=> 'title',
					'desc'		=> __( 'Allows to hide add-to-cart button for guest customer. You can also show customized text instead of add-to-cart button to guest customer. Enter shortcode : {{*wltspab_login_link}} in description to render login link. Use || in case you want to use both text and shortcode to separate them, like : Please login to buy item. || {{*wltspab_login_link}}', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'id' 		=> 'wltspab_settings_hide_add_to_cart_options_start'
				),

				'wltspab_settings_enable_hide_add_to_cart'	=> array(
					'title'   => __( 'Enable Hide Add To Cart', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'desc'    => __( 'Enable add-to-cart button to hide for guest customer', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'id'      => 'wltspab_settings_enable_hide_add_to_cart',
					'default' => 'no',
					'type'    => 'checkbox'
				),

				'wltspab_settings_alternative_to_add_to_cart'	=> array(
					'title'       => __( 'Description In Place Of Add To Cart', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'type'        => 'textarea',
					'desc' => __( 'Description that the guest customer will see in place of add-to-cart button. You and even use your own HTML here. Enter shortcode : {{*wltspab_login_link}} in description to render login link.', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'id'      => 'wltspab_settings_alternative_to_add_to_cart',
					'default'     => __( 'Please login to buy item.', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'desc_tip'    => true,
					'class'		=> 'input-text wide-input',
				),

				'wltspab_settings_alternative_to_add_to_cart_color'		=> array (
					'title'       => __( 'Color For Description', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'desc' => __( 'The description in place of add-to-cart button will appear in the selected color to guest customer. The color will not be applicable in case of HTML used above.', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'id'		=> 'wltspab_settings_alternative_to_add_to_cart_color',
					'default'     => '#dd0d0d',
					'class'		=> 'wltspab_settings_color_picker',
					'css'		=> 'width:80px;',
					'type'		=> 'text',
					'desc_tip'		=> true 
				),

				'wltspab_settings_hide_add_to_cart_options_end'	=> array(
					'type' 	=> 'sectionend',
					'id' 	=> 'wltspab_settings_hide_add_to_cart_options_end',
				),

				/**
				 * Render Setting For Customize Login Link.
				 */
				'wltspab_settings_custom_login_link_options_start'	=> array(
					'title'		=> __( 'Customize Login Link', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'type' 		=> 'title',
					'desc'		=> __( 'Allows to make your customize login link to be used in place of price and/or add-to-cart button for guest customer. Enter shortcode : {{*wltspab_login_link}} in description to render login link.', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'id' 		=> 'wltspab_settings_custom_login_link_options_start'
				),

				'wltspab_settings_custom_login_link_text'	=> array(
					'title'   => __( 'Text For Login Link', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'desc'    => __( 'Enter text to show on login link for guest customer in place of price and/or add-to-cart button', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'desc_tip'    => true,
					'id'      => 'wltspab_settings_custom_login_link_text',
					'default' => 'Login First',
					'type'    => 'text',
					'class'		=> 'input-text regular-input',
				),

				'wltspab_settings_custom_login_link_url'	=> array(
					'title'   => __( 'URL For Login Link', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'desc'    => __( 'Enter url to use in login link for guest customer in place of price and/or add-to-cart button', WOO_LOGIN_TO_SEE_PRICE_AND_BUY_TEXT_DOMAIN ),
					'desc_tip'    => true,
					'id'      => 'wltspab_settings_custom_login_link_url',
					'default' => $myaccount_page_url,
					'type'    => 'text',
					'class'		=> 'input-text regular-input',
				),

				'wltspab_settings_custom_login_link_options_end'	=> array(
					'type' 	=> 'sectionend',
					'id' 	=> 'wltspab_settings_custom_login_link_options_end',
				),

			));
		}
		return $settings;
	}

	function woo_login_to_see_price_and_buy_adding_options_to_delete( $options_to_delete ) {
		$options_to_delete[] = 'wltspab_settings_hide_price_options_start';
		$options_to_delete[] = 'wltspab_settings_enable_hide_price';
		$options_to_delete[] = 'wltspab_settings_alternative_to_price';
		$options_to_delete[] = 'wltspab_settings_alternative_to_price_color';
		$options_to_delete[] = 'wltspab_settings_hide_price_options_end';

		$options_to_delete[] = 'wltspab_settings_hide_add_to_cart_options_start';
		$options_to_delete[] = 'wltspab_settings_enable_hide_add_to_cart';
		$options_to_delete[] = 'wltspab_settings_alternative_to_add_to_cart';
		$options_to_delete[] = 'wltspab_settings_alternative_to_add_to_cart_color';
		$options_to_delete[] = 'wltspab_settings_hide_add_to_cart_options_end';

		$options_to_delete[] = 'wltspab_settings_custom_login_link_options_start';
		$options_to_delete[] = 'wltspab_settings_custom_login_link_text';
		$options_to_delete[] = 'wltspab_settings_custom_login_link_url';
		$options_to_delete[] = 'wltspab_settings_custom_login_link_options_end';

		return $options_to_delete;
	}

}

endif;

/**
 * Main instance of WLTSPAB_RENDER_ADMIN_SETTINGS.
 * @since  1.0.0
 * @return WLTSPAB_RENDER_ADMIN_SETTINGS
 */
WLTSPAB_RENDER_ADMIN_SETTINGS::instance();
?>