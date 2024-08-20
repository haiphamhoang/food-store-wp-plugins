<?php
/**
 * Handle frontend scripts
 *
 * @package FoodStore/Classes
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Frontend scripts class.
 */
class WFS_Frontend_Scripts {

  /**
   * Contains an array of script handles registered by WFS.
   *
   * @var array
   */
  private static $scripts = array();

  /**
   * Contains an array of script handles registered by WFS.
   *
   * @var array
   */
  private static $styles = array();

  /**
   * Contains an array of script handles localized by WFS.
   *
   * @var array
   */
  private static $wp_localize_scripts = array();

  /**
   * Hook in methods.
   */
  public static function init() {
    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_foodstore_scripts' ) );
    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_checkout_scripts' ) );
    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_wc_single_scripts' ) );
  }

  /**
   * Get styles for the frontend.
   *
   * @author Automatic
   * @author WP Scripts
   * @since 1.0.0
   * @return array
   */
  public static function get_styles() {
    return apply_filters(
      'wfs_enqueue_styles',
      array(
        'wfs-bootstrap' => array(
          'src'     => self::get_asset_url( 'assets/css/foodstore-base.css' ),
          'deps'    => '',
          'version' => WFS_VERSION,
          'media'   => 'all',
          'has_rtl' => false,
        ),
        'jquery-toast' => array(
          'src'     => self::get_asset_url( 'assets/css/jquery.toast.css' ),
          'deps'    => '',
          'version' => WFS_VERSION,
          'media'   => 'all',
          'has_rtl' => false,
        ),
        'wfs-modal' => array(
          'src'     => self::get_asset_url( 'assets/css/wfs-modal.css' ),
          'deps'    => '',
          'version' => WFS_VERSION,
          'media'   => 'all',
          'has_rtl' => false,
        ),
        'wfs-icons' => array(
          'src'     => self::get_asset_url( 'assets/css/foodstore-icons.css' ),
          'deps'    => '',
          'version' => WFS_VERSION,
          'media'   => 'all',
          'has_rtl' => false,
        ),
        'wfs-general' => array(
          'src'     => self::get_asset_url( 'assets/css/foodstore-style.css' ),
          'deps'    => '',
          'version' => WFS_VERSION,
          'media'   => 'all',
          'has_rtl' => false,
        ),
        'wfs-variations' => array(
          'src'     => self::get_asset_url( 'assets/css/foodstore-variations.css' ),
          'deps'    => '',
          'version' => WFS_VERSION,
          'media'   => 'all',
          'has_rtl' => false,
        ),
        'wfs-responsive' => array(
          'src'     => self::get_asset_url( 'assets/css/foodstore-responsive.css' ),
          'deps'    => '',
          'version' => WFS_VERSION,
          'media'   => 'all',
          'has_rtl' => false,
        ),
      )
    );
  
  }

  /**
   * Register all WFS scripts.
   *
   * @author Automatic
   * @author WP Scripts
   * @since 1.0.0
   */
  private static function register_scripts() {

    $register_scripts = array(
      'wfs-bootstrap' => array(
        'src'     => self::get_asset_url( 'assets/js/frontend/wfs-bootstrap.js' ),
        'deps'    => array( 'jquery' ),
        'version' => WFS_VERSION,
      ),
      'wfs-modal' => array(
        'src'     => self::get_asset_url( 'assets/js/frontend/wfs-modal.js' ),
        'deps'    => array( 'jquery' ),
        'version' => WFS_VERSION,
      ),
      'wfs-sticky' => array(
        'src'     => self::get_asset_url( 'assets/js/frontend/wfs-sticky.js' ),
        'deps'    => array( 'jquery' ),
        'version' => WFS_VERSION,
      ),
      'jquery-toast' => array(
        'src'     => self::get_asset_url( 'assets/js/frontend/jquery.toast.js' ),
        'deps'    => array( 'jquery' ),
        'version' => WFS_VERSION,
      ),
      'wfs-quantity-changer' => array(
        'src'     => self::get_asset_url( 'assets/js/frontend/quantity-changer.js' ),
        'deps'    => array( 'jquery' ),
        'version' => WFS_VERSION,
      ),
      'wfs' => array(
        'src'     => self::get_asset_url( 'assets/js/frontend/wfs.js' ),
        'deps'    => array( 'jquery' ),
        'version' => WFS_VERSION,
      ),
      'wfs-variations' => array(
        'src'     => self::get_asset_url( 'assets/js/frontend/wfs-variations.js' ),
        'deps'    => array( 'jquery', 'wfs' ),
        'version' => WFS_VERSION,
      ),
      'wfs-checkout' => array(
        'src'     => self::get_asset_url( 'assets/js/frontend/wfs-checkout.js' ),
        'deps'    => array( 'jquery' ),
        'version' => WFS_VERSION,
      ),
    );

    // Check Laze Load Settings
    $register_scripts['wfs-lazy-load'] = array(
      'src'     => self::get_asset_url( 'assets/js/frontend/wfs-lozad.js' ),
      'deps'    => array(),
      'version' => WFS_VERSION,
    );

    foreach ( $register_scripts as $name => $props ) {
      self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
    }
    
  }

  /**
   * Return asset URL.
   *
   * @author Automatic
   * @author WP Scripts
   * @since 1.0.0
   * @param string $path Assets path.
   * @return string
   */
  private static function get_asset_url( $path ) {
    return apply_filters( 'wfs_get_asset_url', plugins_url( $path, WFS_PLUGIN_FILE ), $path );
  }

  /**
   * Register a script for use.
   *
   * @author Automatic
   * @uses   wp_register_script()
   * @param  string   $handle    Name of the script. Should be unique.
   * @param  string   $path      Full URL of the script, or path of the script relative to the WordPress root directory.
   * @param  string[] $deps      An array of registered script handles this script depends on.
   * @param  string   $version   String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
   * @param  boolean  $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
   */
  private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = WFS_VERSION, $in_footer = true ) {
    self::$scripts[] = $handle;
    wp_register_script( $handle, $path, $deps, $version, $in_footer );
  }

  /**
   * Register and enqueue a script for use.
   *
   * @author Automatic
   * @uses   wp_enqueue_script()
   * @param  string   $handle    Name of the script. Should be unique.
   * @param  string   $path      Full URL of the script, or path of the script relative to the WordPress root directory.
   * @param  string[] $deps      An array of registered script handles this script depends on.
   * @param  string   $version   String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
   * @param  boolean  $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
   */
  private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = WFS_VERSION, $in_footer = true ) {
    if ( ! in_array( $handle, self::$scripts, true ) && $path ) {
      self::register_script( $handle, $path, $deps, $version, $in_footer );
    }
    wp_enqueue_script( $handle );
  }

  /**
   * Register a style for use.
   *
   * @author Automatic
   * @uses   wp_register_style()
   * @param  string   $handle  Name of the stylesheet. Should be unique.
   * @param  string   $path    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
   * @param  string[] $deps    An array of registered stylesheet handles this stylesheet depends on.
   * @param  string   $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
   * @param  string   $media   The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
   * @param  boolean  $has_rtl If has RTL version to load too.
   */
  private static function register_style( $handle, $path, $deps = array(), $version = WFS_VERSION, $media = 'all', $has_rtl = false ) {
    self::$styles[] = $handle;
    wp_register_style( $handle, $path, $deps, $version, $media );

    if ( $has_rtl ) {
      wp_style_add_data( $handle, 'rtl', 'replace' );
    }
  }

  /**
   * Register and enqueue a styles for use.
   *
   * @author Automatic
   * @uses   wp_enqueue_style()
   * @param  string   $handle  Name of the stylesheet. Should be unique.
   * @param  string   $path    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
   * @param  string[] $deps    An array of registered stylesheet handles this stylesheet depends on.
   * @param  string   $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
   * @param  string   $media   The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
   * @param  boolean  $has_rtl If has RTL version to load too.
   */
  private static function enqueue_style( $handle, $path = '', $deps = array(), $version = WFS_VERSION, $media = 'all', $has_rtl = false ) {
    if ( ! in_array( $handle, self::$styles, true ) && $path ) {
      self::register_style( $handle, $path, $deps, $version, $media, $has_rtl );
    }
    wp_enqueue_style( $handle );
  }

  /**
   * Register/Enqueue frontend scripts
   *
   * @author Automatic
   * @author WP Scripts
   * @since 1.0.0
   */
  public static function load_foodstore_scripts() {
    
    
    //if ( $enable_asap ) {
      //Load script on checkout page only
      if ( is_checkout() ) {
        self::register_scripts();
        self::enqueue_script( 'wfs-bootstrap' );
        self::enqueue_script( 'wfs-checkout' );

        $params = array(
          'ajaxurl'               => WFS()->ajax_url(),
          'enable_asap'           => get_option( '_wfs_enable_asap' ),
          'checkout_url'          => wc_get_checkout_url(),
          'asap_label'            => get_option( '_wfs_asap_label', true ),
          'default_service_type'  => wfs_get_default_service_type(),
        );

        wp_localize_script( 'wfs-checkout', 'wfsCheckoutParams', $params );
      }
    //}
    
    
    if ( ! wfs_is_foodstore_page() ) 
      return;
    
    wp_enqueue_script( 'jquery-ui-core');

    self::register_scripts();

    // CSS Styles.
    $enqueue_styles = self::get_styles();
    if ( $enqueue_styles ) {
      foreach ( $enqueue_styles as $handle => $args ) {
        if ( ! isset( $args['has_rtl'] ) ) {
          $args['has_rtl'] = false;
        }

        self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'], $args['has_rtl'] );
      }
    }

    self::enqueue_script( 'wfs-bootstrap' );
    self::enqueue_script( 'wfs-modal' );
    self::enqueue_script( 'wfs-sticky' );
    self::enqueue_script( 'jquery-toast' );
    self::enqueue_script( 'wfs-quantity-changer' );
    self::enqueue_script( 'wfs' );
    self::enqueue_script( 'wfs-variations' );

    // Checking admin settings for lazy loading
    $lazy_loading = get_option( '_wfs_enable_lazy_loading', 'yes' );
    if( 'yes' === $lazy_loading )
      self::enqueue_script( 'wfs-lazy-load' );

    $params = array(

      // URLs
      'ajaxurl'               => WFS()->ajax_url(),
      'cart_url'              => wc_get_cart_url(),
      'checkout_url'          => wc_get_checkout_url(),

      // Admin Options  
      'sticky_category_list'  => get_option( '_wfs_listing_sidebar_is_sticky', true ),
      'purchase_redirect'     => get_option( '_wfs_purchase_redirect', 'checkout' ),
      'item_title_popup'      => get_option( '_wfs_enable_title_popup', 'no' ),
      'service_option'        => get_option( '_wfs_enable_service', 'yes' ),
      'service_modal_option'  => get_option( '_wfs_service_modal_option', 'auto' ),
      'enable_asap_option'    => get_option( '_wfs_enable_asap', true ),
      'asap_label'            => wfs_get_asap_label(),
      
      // Create Nonce
      'product_modal_nonce'   => wp_create_nonce( 'product-modal' ),
      'add_to_cart_nonce'     => wp_create_nonce( 'add-to-cart' ),
      'update_cart_nonce'     => wp_create_nonce( 'update-cart-item' ),
      'empty_cart_nonce'      => wp_create_nonce( 'empty-cart' ),
      'remove_item_nonce'     => wp_create_nonce( 'product-remove-cart' ),

      // Messages
      'cart_success_message'  => esc_html__( 'Item added to cart', 'food-store' ),
      'cart_empty_message'    => esc_html__( 'Cart has been cleared', 'food-store' ),
      'cart_process_message'  => wfs_cart_processing_message(),
      'add_to_cart_text'      => wfs_modal_add_to_cart_text(),
      'update_service_text'   => esc_html__( 'Update', 'food-store' ),
      'please_wait_text'      => esc_html__( 'Wait..', 'food-store' ),
      'store_closed_message'  => wfs_get_store_closed_message(),
      'invalid_number_error'  => esc_html__( 'Please enter a valid number', 'food-store' ),

      // Service Options
      'service_type'          => isset( $_COOKIE['service_type'] ) ? sanitize_text_field( $_COOKIE['service_type'] ) : '',
      'service_time'          => isset( $_COOKIE['service_time'] ) ? sanitize_text_field( $_COOKIE['service_time'] ) : '',

      // Other Values
      'scroll_offset'         => apply_filters( 'wfs_scroll_top_offset', 0 ),
      'sidebar_offset'        => apply_filters( 'wfs_sidebar_top_offset', 20 ),
      'error_loaderBg'        => '#B4161B',
      'enable_cart_empty_confirmation' => wfs_render_cart_confirmation() ? 'yes' : 'no',

      // Check for modal filters
      'open_default_modal'   => apply_filters( 'wfs_apply_default_modal', 'yes' ),
    );

    

    wp_localize_script( 'wfs', 'wfs_script', $params );
  }

  /**
   * Register/Enqueue checkout page scripts
   *
   * @author WP Scripts
   * @since 1.0.0
   */
  public static function load_checkout_scripts() {

    if( is_checkout() ) {
      wp_enqueue_style( 'wfs-checkout', self::get_asset_url( 'assets/css/foodstore-checkout-style.css' ), '', WFS_VERSION, 'all' );
      wp_enqueue_style( 'wfs-bootstrap', self::get_asset_url( 'assets/css/foodstore-base.css' ), '', WFS_VERSION, 'all' );
    }
  }

  /**
   * Register/Enqueue WooCommerce ProductSingle Page Scripts
   *
   * @author WP Scripts
   * @since 1.0.0
   */
  public static function load_wc_single_scripts() {

    if( is_product() ) {
      wp_enqueue_style( 'wfs-variations', self::get_asset_url( 'assets/css/foodstore-variations.css' ), '', WFS_VERSION, 'all' );
      wp_enqueue_script( 'wfs-variations', self::get_asset_url( 'assets/js/frontend/wfs-variations.js' ), array( 'jquery'), WFS_VERSION, true );
    }
  }

  /**
   * Localize frontend scripts
   *
   * @author Automatic
   * @author WP Scripts
   * @since 1.0.0
   */
  private static function localize_script( $handle ) {
    
    if ( ! in_array( $handle, self::$wp_localize_scripts, true ) && wp_script_is( $handle ) ) {
      
      $data = self::get_script_data( $handle );

      if ( ! $data ) {
        return;
      }

      $name = str_replace( '-', '_', $handle ) . '_params';
      self::$wp_localize_scripts[] = $handle;
      wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
    }
  }
}

WFS_Frontend_Scripts::init();