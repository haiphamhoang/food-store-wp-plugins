<?php
/**
 * Shortcodes
 *
 * @package FoodStore/Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * FoodStore Shortcodes class.
 */
class WFS_Shortcodes {

  /**
   * Init shortcodes.
   */
  public static function init() {
    $shortcodes = array(
      'foodstore' => __CLASS__ . '::foodstore_page',
    );

    foreach ( $shortcodes as $shortcode => $function ) {
      add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
    }
  }

  /**
   * Show Foodstore Page.
   *
   * @param array $atts Attributes.
   * @return string
   */
  public static function foodstore_page( $atts = array() ) {

    global $shortcode_args;

    $default_args = array(
      'items_per_category'    => -1,
      'post_type'             => 'product',
      'post_status'           => 'publish',
      'category'              => '',
      'show_search'           => 'yes',
      'catalog_mode'          => get_option( '_wfs_catalog_mode', 'no' ),
      'orderby'               => 'id',
      'order'                 => 'ASC',
      'product_service_type'  => '',
    );

    $shortcode_args = wp_parse_args( $atts, $default_args );
    $category_ids   = wfs_render_shortcode_cats( $shortcode_args );

    ob_start();

    wfs_start();

    wfs_get_template( 'wfs-categories.php', 
      array(
        'shortcode_args' => $shortcode_args,
        'category_ids'   => $category_ids,
      ) 
    );

    wfs_get_template( 'wfs-products.php', 
      array(
        'shortcode_args' => $shortcode_args,
        'category_ids'   => $category_ids,
      )
    );

    wfs_end();
    
    return ob_get_clean();
  } 
}