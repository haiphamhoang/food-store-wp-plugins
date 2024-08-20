<?php
/**
 * FoodStore - Shipping related functions and actions.
 *
 * @package FoodStore/Classes
 * @since   1.0
 */

defined( 'ABSPATH' ) || exit;

class WFS_Shipping {

  /** 
   * Constructor of Shipping Class
   */
  public function __construct() {
    
    $auto_shipping_selection = get_option( '_wfs_enable_shipping_selection', 'yes' );

    if( 'yes' == $auto_shipping_selection ) {
      add_filter( 'woocommerce_package_rates', array( $this , 'wfs_service_based_shipping' ), 10, 2 );
      
      //Disable shipping cache
      add_filter( 'transient_shipping-transient-version', function($value, $name) { return false; }, 10, 2 );

    }
  }

  /**
   * Set default shipping methods based on the selected service
   *
   * @author FoodStore
   * @since 1.1
   * @return void
   */
  public function wfs_service_based_shipping( $rates, $package ) {
    // Get choosen service type
    if( function_exists( 'wfs_get_default_service_type' ) ) 
      $service = wfs_get_default_service_type();
    else 
      $service = $_COOKIE['service_type'];

    $service = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : $service;

    if ( ! empty( $rates ) ) { 
      
      $new_rates = array();

      if( $service == 'pickup' ) {
        
        foreach( $rates as $key => $rate ) {
          
          if ('local_pickup' === $rate->method_id || 'legacy_local_pickup' === $rate->method_id ) {
            $new_rates[$key] = $rate;
          }
        }

        return ( ! empty($new_rates) ? $new_rates : $rates );

      } else if( $service == 'delivery' ) {
        
        foreach( $rates as $key => $rate ) {
          
          if ('local_pickup' != $rate->method_id && 'legacy_local_pickup' != $rate->method_id ) {
            $new_rates[$key] = $rate;
          }
        }
        
        return ( ! empty($new_rates) ? $new_rates : $rates );
      }
    }
  }
}

new WFS_Shipping();