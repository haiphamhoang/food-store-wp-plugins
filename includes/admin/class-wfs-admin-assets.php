<?php
/**
 * Load assets
 *
 * @package FoodStore/Admin
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'WFS_Admin_Assets', false ) ) :

  /**
   * WFS_Admin_Assets Class.
   */
  class WFS_Admin_Assets {

    /**
     * Hook in tabs.
     */
    public function __construct() {
      add_filter( 'woocommerce_screen_ids', array( $this, 'woocommerce_screen') );
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    }

    /**
     * Add settings page Screen ID to WooCommerce screen IDs
     */
    public function woocommerce_screen( $screens ) {
      $screens[] = 'toplevel_page_wfs-settings';
      return $screens;
    }

    /**
     * Enqueue styles.
     */
    public function admin_styles() {
      
      global $wp_scripts;

      $screen    = get_current_screen();
      $screen_id = $screen ? $screen->id : '';

      $version = defined( 'WFS_VERSION' ) ? WFS_VERSION : '1.0';

      // Register admin styles.
      wp_register_style( 'wfs_admin_menu_styles', WFS()->plugin_url() . '/assets/css/menu.css', array(), $version );
      wp_register_style( 'jquery_timepicker', WFS()->plugin_url() . '/assets/css/jquery.timepicker.css', array(), $version );
      wp_register_style( 'wfs_admin_styles', WFS()->plugin_url() . '/assets/css/admin.css', array(), $version );
      wp_register_style( 'wfs_admin_order_styles', WFS()->plugin_url() . '/assets/css/foodstore-admin-order.css', array(), $version );

      // Sitewide menu CSS.
      wp_enqueue_style( 'wp-color-picker' );
      wp_enqueue_style( 'jquery_timepicker' );
      wp_enqueue_style( 'wfs_admin_menu_styles' );
      wp_enqueue_style( 'wfs_admin_styles' );

      // Admin Order page CSS.
      if ( in_array( str_replace( 'edit-', '', $screen_id ), wc_get_order_types( 'order-meta-boxes' ) ) ) {
        wp_enqueue_style( 'wfs_admin_order_styles' );
      }

      // Placeholder style.
		  wp_register_style( 'admin-foodstore-inline', false ); // phpcs:ignore
		  wp_enqueue_style( 'admin-foodstore-inline' );

      if ( isset( $_GET['page'] ) && 'wfs-settings' == $_GET['page'] ) {
        //Get admin color scheme upcoming fix
        $select2_color_scheme = "
          html body.wp-admin.toplevel_page_wfs-settings .select2-dropdown{ border-color: #007cba }
          html body.wp-admin.toplevel_page_wfs-settings .select2-dropdown--below { box-shadow: 0 0 0 1px #007cba, 0 2px 1px rgba(0, 0, 0, 0.1); }
          html body.wp-admin.toplevel_page_wfs-settings .select2-dropdown--above { box-shadow: 0 0 0 1px #007cba, 0 -2px 1px rgba(0, 0, 0, 0.1); }
          html body.wp-admin.toplevel_page_wfs-settings .select2-selection--single .select2-selection__rendered:hover { color: #007cba; }
          html body.wp-admin.toplevel_page_wfs-settings .select2-container.select2-container--focus .select2-selection--single,
		      html body.wp-admin.toplevel_page_wfs-settings .select2-container.select2-container--open .select2-selection--single,
		      html body.wp-admin.toplevel_page_wfs-settings .select2-container.select2-container--open .select2-selection--multiple {
			      border-color: #007cba;
			      box-shadow: 0 0 0 1px #007cba;
		      }
          html body.wp-admin.toplevel_page_wfs-settings .select2-container--default
		      .select2-results__option--highlighted[aria-selected],
		      html body.wp-admin.toplevel_page_wfs-settings .select2-container--default
		      .select2-results__option--highlighted[data-selected] {
            background-color:#007cba;
          }
          ";
        wp_add_inline_style( 'admin-foodstore-inline', $select2_color_scheme );
      }

    }

    /**
     * Enqueue scripts.
     */
    public function admin_scripts() {
      
      global $wp_query, $post;

      $version = defined( 'WFS_VERSION' ) ? WFS_VERSION : '1.0';

      $screen       = get_current_screen();
      $screen_id    = $screen ? $screen->id : '';
      $wc_screen_id = sanitize_title( __( 'FoodStore', 'food-store' ) );

      wp_register_script( 'jquery-timepicker', WFS()->plugin_url() . '/assets/js/admin/jquery.timepicker.js', array( 'jquery' ), '1.11.14', true );
      wp_register_script( 'jquery-tiptip', WFS()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.js', array( 'jquery' ), '1.0.0', true );
      wp_register_script( 'wfs-admin', WFS()->plugin_url() . '/assets/js/admin/foodstore-admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'jquery-timepicker', 'wp-color-picker' ), $version );
      wp_register_script( 'wfs-admin-order', WFS()->plugin_url() . '/assets/js/admin/foodstore-admin-order.js', array( 'jquery' ), $version );
      wp_register_script( 'wfs-admin-metabox', WFS()->plugin_url() . '/assets/js/admin/foodstore-admin-metaboxes.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip'  ), $version );

      if( $screen_id == 'toplevel_page_wfs-settings' ) {
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery-timepicker' );
        wp_enqueue_script( 'jquery-tiptip' );
        wp_enqueue_script( 'wfs-admin' );
      }

      //Admin metabox js for addon category
      if ( in_array( $screen_id, array( 'product', 'edit-product' ) ) ) {
        wp_enqueue_script( 'wfs-admin-metabox' );

        $params = array(
					'post_id'                       => isset( $post->ID ) ? $post->ID : '',
					'ajax_url'                      => admin_url( 'admin-ajax.php' ),
          'remove_row_confirmation'       => esc_html__( 'Are you sure to remove this row?', 'food-store' ),
          'parent_addon_selection_error'  => esc_html__( 'Please select addon first', 'food-store' ),
          'fetching_addon_msg'            => esc_html__( 'Fetching addons.', 'food-store' ),
          'add_addon_text'                => esc_html__( 'Add', 'food-store' ),
          'create_addon_category_text'    => esc_html__( 'Create New Addon Category', 'food-store' ),
          'select_addon_text'             => esc_html__( 'Select Addon', 'food-store' ),
          'category_already_selected'     => esc_html__( 'This category is already selected', 'food-store' ),
				);

				wp_localize_script( 'wfs-admin-metabox', 'wfs_admin_addon_params', $params );
      }


      if ( in_array( str_replace( 'edit-', '', $screen_id ), wc_get_order_types( 'order-meta-boxes' ) ) ) {
        wp_enqueue_script( 'wfs-admin-order' );
        wp_localize_script(
					'wfs-admin-order',
					'wfs_admin_order_params',
					array(
						'payment_status'          => esc_html__( 'Payment Status', 'food-store' ),
					)
				);
      }
    }
  }

endif;

return new WFS_Admin_Assets();