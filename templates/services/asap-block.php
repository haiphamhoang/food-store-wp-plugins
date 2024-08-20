<?php
/**
 * The template for displaying ASAP option in popup wfsmodal
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
<div class="wfs-asap-block-wrapper asap-block-<?php echo $service_type; ?>">
  <h4 class="wfs-asap-label"><?php wfs_schedule_order_html(); ?></h4>
  <!-- Nav group starts -->
  <ul class="nav-tabs nav wfs-schedule-nav-group" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" data-toggle="tab"  role="tab" aria-controls="<?php echo $service_type; ?>_asap" aria-selected="true"><?php echo wfs_get_asap_label(); ?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab"  role="tab" aria-controls="<?php echo $service_type; ?>_schedule" aria-selected="false"><?php echo wfs_get_order_later_label(); ?></a>
    </li>
  </ul>
  <!-- Nav group ends -->
</div>