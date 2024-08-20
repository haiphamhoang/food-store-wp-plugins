<?php
/**
 * FoodStore Advanced Settings
 *
 * @package FoodStore/Admin
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WFS_Settings_Advanced', false ) ) {
  return new WFS_Settings_Advanced();
}

/**
 * WFS_Settings_Advanced.
 */
class WFS_Settings_Advanced extends WFS_Settings_Page {

  /**
   * Constructor.
   */
  public function __construct() {
    
    $this->id    = 'advanced';
    $this->label = __( 'Advanced', 'food-store' );

    //Tools related action
    add_action( 'foodstore_admin_field_tools_setting' , array( $this, 'tools_setting' ), 10 );
    add_filter( 'foodstore_admin_settings_sanitize_option__wfs_tools_setting', array( $this, 'sanitize_tools_setting_option' ), 10, 3 );
    add_action( 'admin_init', array( $this, 'wfs_export_settings' ), 10 );
    add_action( 'admin_init', array( $this, 'wfs_import_settings' ), 10 );
    add_filter( 'admin_body_class', array( $this, 'wfs_tools_body_settings_class' ), 10 );
    parent::__construct();
  }

  /**
   * Get sections.
   *
   * @return array
   */
  public function get_sections() {
    
    $sections = array(
      ''      => __( 'Advanced', 'food-store' ),
      'tools' => __( 'Tools', 'food-store' ),
    );
    return apply_filters( 'foodstore_get_sections_' . $this->id, $sections );
  }

  /**
   * Get settings array.
   *
   * @param string $current_section Current section name.
   * @return array
   */
  public function get_settings( $current_section = '' ) {

    if( 'tools' == $current_section ) {
      
      $settings = apply_filters(
        
        'foodstore_tools_settings',
        
        array(

          array(
            'title'     => __( 'Tools', 'food-store' ),
            'type'      => 'title',
            'id'        => 'tools_options',
          ),

          array(
            'type'    => 'tools_setting',
            'id'      => '_wfs_tools_setting',
          ),

          array(
            'type'      => 'sectionend',
            'id'        => 'tools_options',
          ),

        )
      );
    }
    else {
      $settings = apply_filters(
      
        'foodstore_advanced_settings',
      
        array(

          array(
            'title'     => __( 'Advanced Settings', 'food-store' ),
            'type'      => 'title',
            'id'        => 'advanced_options',
          ),

          array(
            'title'     => __( 'Other Product Types', 'food-store' ),
            'desc'      => __( 'Keep other product options like <i>Grouped</i>, <i>External</i>, <i>Virtual</i> etc.', 'food-store' ),
            'id'        => '_wfs_adv_keep_other_product_types',
            'default'   => 'no',
            'type'      => 'checkbox',
          ),

          array(
            'title'     => __( 'Purge Settings !!', 'food-store' ),
            'desc'      => __( 'Remove Food Store data when plugin is deactivated.', 'food-store' ),
            'id'        => '_wfs_adv_remove_data_on_uninstall',
            'default'   => 'no',
            'type'      => 'checkbox',
          ),
          
          array(
            'type'      => 'sectionend',
            'id'        => 'advanced_options',
          ),
        )
      );
    }

    

    return apply_filters( 'foodstore_get_settings_' . $this->id, $settings, $current_section );
  }

  /**
   * Get settings fields for Tools
   *
   * @since 1.4
   * @return void
   */
  public function tools_setting( $value ) {
    $option_value = (array) WFS_Admin_Settings::get_option( $value['id'] );
    $description = WFS_Admin_Settings::get_field_description( $value );
    include_once dirname( __DIR__, 1 ) . '/views/html-admin-tools-settings.php';
  }

  /**
   * Export settings in a JSON file
   */
  public function wfs_export_settings() {

    if( !isset( $_POST['wfs_export_settings'] ) )
      return;
  
    if( empty( $_POST['wfs_export_nonce'] ) )
		  return;

	  if( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wfs_export_nonce'] ) ), 'wfs_export_nonce' ) )
		  return;

	  if( ! current_user_can( 'manage_options' ) )
		  return;

    //Execute the DB query to fetch the settings data
    global $wpdb;
    $option_name = '_wfs_';
    $settings = [];
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE '%s' ", $option_name . '%' ) );
    if( is_array( $results ) && !empty( $results ) ) {
      foreach( $results as $key => $result ) {
        $option_name  = isset( $result->option_name ) ? $result->option_name : '';
        $option_value = isset( $result->option_value ) ? $result->option_value : '';
        $settings[$key][$option_name] = $option_value;
      }
    }
	  nocache_headers();
	  header( 'Content-Type: application/json; charset=utf-8' );
	  header( 'Content-Disposition: attachment; filename=foodstore-settings-export-' . date( 'm-d-Y' ) . '.json' );
	  header( "Expires: 0" );

	  echo json_encode( $settings );
	  exit;
  }

  /**
   * Import FoodStore settings from a JSON file
   *
   * @return void
   */
  public function wfs_import_settings() {

    if( !isset( $_POST['wfs_import_settings'] ) )
      return;
  
    if( empty( $_POST['wfs_import_nonce'] ) )
      return;

    if( ! wp_verify_nonce( sanitize_text_field( $_POST['wfs_import_nonce'] ), 'wfs_import_nonce' ) )
		  return;

	  if( ! current_user_can( 'manage_options' ) )
		  return;

    $import_file = sanitize_file_name( $_FILES['import_file']['tmp_name'] );

	  if( empty( $import_file ) ) {
		  wp_die( esc_html__( 'Please upload a valid json file to import', 'food-store' ) );
	  }

    //Get settings from JSON file
    $settings = json_decode( file_get_contents( $_FILES['import_file']['tmp_name'] ) );
    if( is_array( $settings ) && !empty( $settings ) ) {
      foreach( $settings as $setting ) {
        foreach( $setting as $key => $value ) {
          $value = maybe_unserialize( $value );
          update_option( $key, $value );
        }
      }
      wp_safe_redirect( admin_url( 'admin.php?page=wfs-settings&tab=advanced&section=tools&wfs-message=settings-imported' ) );
      exit;
    }
  }

  /**
   * Output the settings.
   */
  public function output() {
    
    global $current_section;

    $settings = $this->get_settings( $current_section );
    WFS_Admin_Settings::output_fields( $settings );
  }

  /**
   * Save settings.
   */
  public function save() {
    
    global $current_section;

    $settings = $this->get_settings( $current_section );

    WFS_Admin_Settings::save_fields( $settings );

    if ( $current_section ) {
      do_action( 'foodstore_update_options_' . $this->id . '_' . $current_section );
    }
  }

  /**
   * Add body class to admin tools section
   * @param array $classes
   * @since 1.4.6.2
   * @return mixed
   */
  public function wfs_tools_body_settings_class( $classes ) {
    if( ( isset( $_GET['page'] ) && $_GET['page'] == 'wfs-settings' ) 
    && ( isset( $_GET['section'] ) && $_GET['section'] == 'tools' ) ) {
      $classes .= 'wfs-tools-settings';
    }
    return $classes;
  }

}

return new WFS_Settings_Advanced();