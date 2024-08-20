<?php
/**
 * The template for displaying delivery order option in popup wfsmodal
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
<?php do_action( 'foodstore_before_service_hours', 'delivery' ); ?>
<div class="wfs-service-time-wrapper">
	<span class="wfs-time-label-text <?php echo $delivery_class; ?>">
  <?php
  	/* translators: %1s: get service label */
    printf( __( 'Select %1s Time', 'food-store' ), wfs_get_service_label('delivery') );
  ?>
  </span>
  <?php wfs_render_service_hours( 'delivery' ); ?>
</div>