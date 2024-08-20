<?php
/**
 * Admin View: Food Store Product Tab Addons
 *
 * @package FoodStore
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

ob_start();
?>


<div id="addon_options" class="panel woocommerce_options_panel hidden">
    
  <!-- Create new addon -->
  <p class="form-field">
    <button type="button" class="button add-new-addon-category alignright"><span class="dashicons dashicons-plus-alt addon-icons"></span><?php _e( 'Create New Addon', 'wfs' ); ?></button>
  </p>
  <!-- Create new addon -->

  <!-- Addon Category starts here -->
  <div class="wfs-addons">
    <?php include 'html-product-new-addon.php';  ?>
  </div>
  <!-- Addon Category ends here-->

  <!-- Add new addon starts here -->
  <p class="form-field">
    <button type="button" data-item-id="32" class="button button-primary add-new-addon alignright">
        <span class="dashicons dashicons-plus addon-icons"></span>
				Add New			</button>
  </p>
  <!-- Add new addon ends here -->

</div>

<?php
$html = ob_get_clean();
echo $html;