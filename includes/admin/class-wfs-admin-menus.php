<?php
/**
 * Setup menus in WP admin.
 *
 * @package FoodStore\Admin
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WFS_Admin_Menus', false ) ) {
  return new WFS_Admin_Menus();
}

/**
 * WFS_Admin_Menus Class.
 */
class WFS_Admin_Menus {

  /**
   * Hook in tabs.
   */
  public function __construct() {
    
    // Add menus.
    add_action( 'admin_menu', array( $this, 'wfs_menu' ), 60 );

    // Handle saving settings earlier than load-{page} hook to avoid race conditions in conditional menus.
    add_action( 'wp_loaded', array( $this, 'save_settings' ) );

    //Show Order online submenu from admin menu bar
    add_action( 'admin_bar_menu', array( $this, 'admin_bar_menus' ), 31 );
  }

  /**
   * Add menu item.
   */
  public function wfs_menu() {

    add_menu_page( __( 'Food Store', 'food-store' ), __( 'Food Store', 'food-store' ), 'manage_woocommerce', 'wfs-settings', array( $this, 'wfs_settings_page' ), null, '55.5' );

    add_submenu_page( 'wfs-settings', __( 'General', 'food-store' ), __( 'General', 'food-store' ), 'manage_woocommerce', 'wfs-settings', array( $this, 'wfs_settings_page' ) );

    add_submenu_page( 'wfs-settings', __( 'Services', 'food-store' ), __( 'Services', 'food-store' ), 'manage_woocommerce', admin_url( 'admin.php?page=wfs-settings&tab=services' ) );

    add_submenu_page( 'wfs-settings', __( 'Layout & Styling', 'food-store' ), __( 'Layout & Styling', 'food-store' ), 'manage_woocommerce', admin_url( 'admin.php?page=wfs-settings&tab=styling' ) );

    add_submenu_page( 'wfs-settings', __( 'Advanced', 'food-store' ), __( 'Advanced', 'food-store' ), 'manage_woocommerce', admin_url( 'admin.php?page=wfs-settings&tab=advanced' ) );

  }

  /**
   * Init the status page.
   */
  public function wfs_settings_page() {
    WFS_Admin_Settings::output();
  }

  /**
   * Handle saving of settings.
   *
   * @return void
   */
  public function save_settings() {
    global $current_tab, $current_section;

    // We should only save on the settings page.
    if ( ! is_admin() || ! isset( $_GET['page'] ) || 'wfs-settings' !== $_GET['page'] ) { 
      return;
    }

    // Include settings pages.
    WFS_Admin_Settings::get_settings_pages();

    // Get current tab/section.
    $current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) );
    $current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) );

    // Save settings if data has been posted.
    if ( '' !== $current_section && apply_filters( "foodstore_save_settings_{$current_tab}_{$current_section}", ! empty( $_POST['save'] ) ) ) {
      WFS_Admin_Settings::save();
    } elseif ( '' === $current_section && apply_filters( "foodstore_save_settings_{$current_tab}", ! empty( $_POST['save'] ) ) ) {
      WFS_Admin_Settings::save();
    }
  }

  /**
	 * Add the "Visit Order Online" link in admin bar main menu.
	 *
	 * @since 1.4.8.3
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 */
	public function admin_bar_menus( $wp_admin_bar ) {
		if ( ! is_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		// Show only when the user is a member of this site, or they're a super admin.
		if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
			return;
		}

		// Don't display when shop page is the same of the page on front.
		if ( intval( get_option( 'page_on_front' ) ) === wfs_get_page_id( 'order_online' ) ) {
			return;
		}

		// Add an option to visit the order online page.
		$wp_admin_bar->add_node(
			array(
				'parent' => 'site-name',
				'id'     => 'view-order-online-page',
				'title'  => __( 'Visit Order Online', 'food-store' ),
				'href'   => wfs_get_page_permalink( 'order_online' ),
			)
		);
	}
}
return new WFS_Admin_Menus();