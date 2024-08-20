<?php
/**
 * Display notices in admin
 *
 * @package FoodStore\Admin
 * @version 1.4
 */

 defined( 'ABSPATH' ) || exit;

 /**
 * WFS_Admin_Notices Class.
 */
class WFS_Admin_Notices {

  /**
	 * Constructor.
	 */
  public static function init() {
    add_action( 'admin_init', array( __CLASS__, 'show_import_notices' ), 20 );
  }

  /**
   * Show notice if import has been done
   */
  public static function show_import_notices() {
    if ( ! empty( $_GET['wfs-message'] ) && ( $_GET['wfs-message'] == 'settings-imported' )  ) {
      /* translators: %s: admin.php?page=wfs-settings settings page url */
      $message = sprintf( __( 'Food Store data imported successfully. %s', 'food-store' ), '<a href="' . esc_url( admin_url( 'admin.php?page=wfs-settings' ) ) . '">' . __( 'View Settings', 'food-store' ) . '</a>' );
      echo '<div class="notice notice-success is-dismissible"><p>' . wp_kses_post( $message ) . '</p></div>';
    }
  }
}

WFS_Admin_Notices::init();