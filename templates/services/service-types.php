<?php
/**
 * The template for displaying product popup wfsmodal
 *
 * This template can be overridden by copying it to yourtheme/food-store
 *
 * @package FoodStore/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$enable_delivery = ( get_option( '_wfs_enable_delivery' ) == 'yes' ) ? true : false;
$enable_pickup   = ( get_option( '_wfs_enable_pickup' ) == 'yes' ) ? true : false ;
$enable_asap     = ( get_option( '_wfs_enable_asap' ) == 'yes' ) ? true : false; 
?>

<?php apply_filters( 'wfs_service_notice_area', '' ); ?>

<?php if ( $enable_delivery && $enable_pickup ) : ?>

<ul class="nav nav-tabs" id="wfsTab" role="tablist">

  <li class="nav-item">
    <a class="nav-link active" id="pickup-tab" data-toggle="tab"  role="tab" aria-controls="pickup" aria-selected="true">
      <?php echo wfs_get_service_label('pickup'); ?>
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link" id="delivery-tab" data-toggle="tab"  role="tab" aria-controls="delivery" aria-selected="false">
      <?php echo wfs_get_service_label('delivery'); ?>
    </a>
  </li>

</ul>
<?php endif; ?>

<!-- Message area for service related errors -->
<div class="foodstore_service_error inactive"></div>

<div class="tab-content wfs-service-tab">

  <?php if ( $enable_pickup ) : ?>
    
    <div class="tab-pane service-tab-pane active" data-service-type="pickup" id="pickup" role="tabpanel" aria-labelledby="pickup-tab">

      <?php 
        $pickup_store_time = wfs_get_store_timing( 'pickup' );
        $pickup_class      = !empty( $pickup_store_time ) ? '' : 'wfs-d-none';
      ?>
      
      <?php if( $enable_asap ) : ?>
        
        <!-- Render ASAP option -->
        <?php do_action( 'foodstore_asap_block', 'pickup' ); ?>

        <!-- Pickup ASAP block -->
        <div class="tab-content">
          
          <div class="tab-pane pickup-tab-pane active" id="pickup_asap" role="tabpanel" aria-labelledby="pickup_asap-tab"></div>
          
          <div class="tab-pane pickup-tab-pane" id="pickup_schedule" role="tabpanel" aria-labelledby="pickup_schedule-tab">
            <?php wfs_render_schedule_pickup( $pickup_class ); ?>
          </div>
        </div>
      <?php else: ?>
        <?php wfs_render_schedule_pickup( $pickup_class ); ?>
      <?php endif; ?>
    </div>

  <?php endif; ?>

  <?php if ( $enable_delivery ) : ?>

    <div class="tab-pane service-tab-pane <?php if(!$enable_pickup) echo 'active'; ?>" data-service-type="delivery" id="delivery" role="tabpanel" aria-labelledby="delivery-tab">

      <?php 
        $delivery_store_time = wfs_get_store_timing( 'delivery' );
        $delivery_class      = !empty( $delivery_store_time ) ? '' : 'wfs-d-none';  
      ?>

      <?php if( $enable_asap ) : ?>
        <?php do_action( 'foodstore_asap_block', 'delivery' ); ?>
        <div class="tab-content">
          
          <div class="tab-pane delivery-tab-pane active" id="delivery_asap" role="tabpanel" aria-labelledby="delivery_asap-tab">
            <?php do_action('foodstore_before_asap'); ?>
          </div>
          
          <div class="tab-pane delivery-tab-pane" id="delivery_schedule" role="tabpanel" aria-labelledby="delivery_schedule-tab">
            <?php wfs_render_schedule_delivery( $delivery_class ); ?>
          </div>
        </div>
      <?php else: ?>
        <?php wfs_render_schedule_delivery( $delivery_class ); ?>
      <?php endif; ?>

    </div>

  <?php endif; ?>

</div>