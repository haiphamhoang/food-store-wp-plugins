<?php
/**
 * The template for displaying order option in popup wfsmodal
 *
 * This template can be overridden by copying it to yourtheme/food-store
 *
 * @package FoodStore/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
?>
<?php do_action( 'foodstore_before_service_hours', 'pickup' ); ?>
<div class="wfs-service-time-wrapper">
  <span class="wfs-time-label-text <?php echo $pickup_class; ?>">
    <?php 
    /* translators: %1s: get service label */
    printf( __( 'Select %1s Time', 'food-store' ), wfs_get_service_label('pickup') );
    ?>
  </span>
  <?php wfs_render_service_hours( 'pickup' ); ?>
</div>