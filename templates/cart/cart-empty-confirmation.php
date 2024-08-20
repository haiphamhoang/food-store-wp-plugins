<?php
/**
 * The template for displaying clear cart confirmation message
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

<!-- Food Store Modal -->
<div class="wfsmodal micromodal-slide" id="wfsConfirmationModal" aria-hidden="true">
  <div class="wfsmodal-dialog" tabindex="-1" data-micromodal-close>
    <div class="wfsmodal-container" role="dialog" aria-labelledby="wfsConfirmationModal-title">
      
      <header class="wfsmodal-header">
        <h5 class="wfsmodal-title" id="wfsServiceModal-title">
          <?php _e('Confirmation', 'food-store'); ?>
        </h5>
        <button type="button" class="modal__close" aria-label="Close" data-micromodal-close></button>
      </header>

      <div class="wfsmodal-body">
        <h5><?php echo $confirmation_message; ?></h5>
        <div class="confirmation-actions-wrapper">
          <button type="button" data-action="cancel" class="fs-btn-md fs-btn-primary"><?php _e('Cancel', 'food-store'); ?></button>
          <button type="button" data-action="yes" class="fs-btn-md fs-btn-primary"><?php _e('Yes', 'food-store'); ?></button>
        </div>
      </div>

    </div>
  </div>
</div>