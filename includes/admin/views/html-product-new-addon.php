<?php
/**
 * Product New Addon.
 *
 * @package FoodStore/Admin
 */

defined( 'ABSPATH' ) || exit;

$post_id        = get_the_ID();
$product_addons = get_post_meta( $post_id, '_wfs_product_addon', true );
$uuid           = time();
$category_name  = '';


if( is_array( $product_addons ) && !empty( $product_addons ) ) :
  foreach( $product_addons as $key => $product_addon ) :
    $addon_category_id = isset( $product_addon['category'] ) ? $product_addon['category'] : '';
    if( !empty( $addon_category_id ) ) {
      
      $addon_category = get_term_by( 'id', absint( $addon_category_id ), 'product_addon' );
      
      if( !is_wp_error( $addon_category ) ) {
        $category_name = $addon_category->name;
      }
      
    }
  ?>
  <!-- Saved Product Addon Starts -->
  <div class="wfs-addon">
    <h3>
      <strong><?php echo $category_name; ?></strong>
      <a href="#" class="remove_row delete alignright"><span class="dashicons dashicons-remove"></span></a>
    </h3>

    <div class="wfs-addon-metabox">
      <div class="wfs-addon-category">
        <div class="wfs-addons-selection">
          <select name="_wfs_addons[<?php echo $key; ?>][category]" class="_wfs_addon_select">
            <option value=""><?php _e( 'Select Addon', 'food-store' ); ?></option>
          
            <?php
              $get_addons = wfs_get_all_addons();
              $fooditem_id = get_the_ID();
              foreach( $get_addons as $get_addon ) {
                if( $get_addon->parent == 0 ) {
                  echo '<option value="' . $get_addon->term_id . '" '. selected( $addon_category_id, $get_addon->term_id ) .' >' . $get_addon->name . '</option>';
                }
              }
            ?>
          </select>
          
          <button type="button" class="button load-wfs-addon" data-uuid="<?php echo $key; ?>" >
            <?php esc_html_e( 'Add', 'food-store' ); ?>
          </button>

        </div>

        <?php do_action( 'wfs_addons_after_selection', $key, $post_id ); ?>
        
      </div>

      <div class="wfs-addon-items-wrapper">
        <?php 
          //Get saved product addons
          include 'html-product-updated-addon-category.php'; 
        ?>
      </div>
    </div>
  </div>
  <!--Saved Product Addon Ends -->
  <?php
  endforeach;
else :
  ?>
  <!-- Product Addon Starts -->
  <div class="wfs-addon">
    <h3>
      <strong><?php esc_html_e( 'Select Addon', 'food-store' ); ?></strong>
      <a href="#" class="remove_row delete alignright"><span class="dashicons dashicons-remove"></span></a>
    </h3>

    <div class="wfs-addon-metabox">
      <div class="wfs-addon-category">
        <div class="wfs-addons-selection">
          <select name="_wfs_addons[<?php echo $uuid; ?>][category]" class="_wfs_addon_select">
            <option value=""><?php _e( 'Select Addon', 'food-store' ); ?></option>
          
            <?php
              $get_addons = wfs_get_all_addons();
              $fooditem_id = get_the_ID();
              foreach( $get_addons as $get_addon ) {
                if( $get_addon->parent == 0 ) {
                  echo '<option value="' . $get_addon->term_id . '">' . $get_addon->name . '</option>';
                }
              }
            ?>
          </select>
          
          <button type="button" class="button load-wfs-addon" data-uuid="<?php echo $uuid; ?>"  >
            <?php esc_html_e( 'Add', 'food-store' ); ?>
          </button>
        </div>

        <?php do_action( 'wfs_addons_after_selection', $uuid, $post_id ); ?>

      </div>

      <div class="wfs-addon-msg">
        <?php esc_html_e( 'Please select a addon first!', 'food-store' ); ?>
      </div>

      <div class="wfs-addon-items-wrapper"></div>
    </div>
  </div>
  <!--Product Addon Ends -->
  <?php
endif;
?>