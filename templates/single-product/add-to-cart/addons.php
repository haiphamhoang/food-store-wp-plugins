<?php
/**
 * Product Addons
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/addons.php.
 *
 * @package FoodStore/Templates
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

$product_id = $product->get_id();

$cart_product = !empty( $cart_key ) ? WC()->cart->get_cart_item( $cart_key ) : array();
$cart_addons = isset( $cart_product['addons'] ) ? $cart_product['addons'] : array();

$product_disable_notes = get_post_meta( $product_id, '_wfs_disable_instruction', true );
$product_disable_notes = ( $product_disable_notes == 'yes' ) ? true : false ;

$global_enable_notes = get_option( '_wfs_enable_special_note', true );
$global_enable_notes = ( $global_enable_notes == 'yes' ) ? true : false ;


if ( !empty( $product_id ) ) {

  //Get saved addons from postmeta
  $selected_addons = get_post_meta( $product_id, '_wfs_product_addon', true );
  $selected_addon_ids = [];
  $selected_addon_category = '';
  $child_ids = $child_ids_list = [];

  if( is_array( $selected_addons ) && !empty( $selected_addons ) ) {
    $selected_addon_ids = wp_list_pluck( $selected_addons, 'category' );
    $child_ids          = wp_list_pluck( $selected_addons, 'items' );
  }

  //Get an array of all child addons saved under post meta
  if( is_array( $child_ids ) && !empty( $child_ids ) ) {
    foreach( $child_ids as $child_lists ) {
      foreach( $child_lists as $child_id ) {
        $child_ids_list[] = $child_id;
      }
    }
  }
  
  $addon_categories = wp_get_post_terms( $product_id, 'product_addon' );
  
  $addon_child_cats = array();
  $category_name_slug = '';
  $var = '';

  
  if( is_array( $selected_addons ) && !empty( $selected_addons ) ) {
    echo '<div class="wfs-item-addons-container">';
    foreach( $selected_addons as $key => $addon_category ) {
      $addon_category = isset( $addon_category['category'] ) ? $addon_category['category'] : '';
      $addon_items    = isset( $selected_addons[$key]['items'] ) ? $selected_addons[$key]['items'] : array();
      //Lets get the parent category details by id
      $parent_category      = get_term_by( 'id', (int) $addon_category, 'product_addon' );
      $parent_id            = $addon_category;
      
      $parent_category_slug = isset( $parent_category->slug ) ? $parent_category->slug : '';
      $parent_category_name = isset( $parent_category->name ) ? $parent_category->name : '';

      $class  = ( $var == $parent_category_name ) ? 'same' : '';
      $var    = $parent_category_name;

      if( !empty( $parent_category_name ) && !empty( $addon_items ) ) : ?>
        <div class="wfs-addons-element-block">
          <h6 class="wfs-addon-category-title"><?php echo $parent_category_name; ?></h6>
      <?php endif;

      do_action( 'wfs_after_addon_category_title', $product_id, $key, $parent_category );

      $child_categories = get_terms( 'product_addon', array( 'parent' => $parent_id, 'orderby' => 'slug', 'hide_empty' => false ) );

      if( is_array( $child_categories ) && !empty( $child_categories ) ) {
        
        foreach( $child_categories as $child_category ) {

          if( in_array( $child_category->term_id, $child_ids_list ) ) {
            $category_slug  = $child_category->slug;
            $category_name  = $child_category->name;

            $category_price = get_term_meta( $child_category->term_id, '_wfs_addon_item_price', true );
            $category_price = $category_price != '' ? wfs_get_addon_price( $product, $category_price ) : '0.00';

            $class  = ( $var == $parent_category_name ) ? 'same' : '';
            $var    = $parent_category_name;

            $choice = wfs_get_term_choice( $parent_id );
            $field_name  = ( $choice == 'radio' ) ? $parent_category_slug : $category_slug;

            $check_addon_in_cart = wfs_check_addon_in_cart( $field_name, $category_slug, $cart_addons );
            $selected = $check_addon_in_cart ? 'checked' : '';

          ?>
          <div class="wfs-addon-category">
            <label for="<?php echo $category_slug; ?>">
              <input class="wfs-addon-type" id="<?php echo $category_slug; ?>" class="wfs-addon-field" name="<?php echo $field_name; ?>" <?php echo $selected; ?> type="<?php echo $choice; ?>" value="<?php echo $category_slug; ?>" data-attrs="<?php echo $addon_category->term_id . '|' . $category_price . '|' . $choice; ?>" >
              <span><?php echo $category_name; ?></span>
            </label>
            <span><?php echo '&nbsp;+&nbsp;' . wc_price( $category_price ); ?></span>
          </div><!-- wfs-addon-category -->
          <?php
          }
        }

        echo '</div><!-- /wfs-addons-element-block -->';

      }
      
    }

    echo '</div><!-- /wfs-item-addons-container -->';
  }

  echo apply_filters( 'wfs_before_products_note', '', $product_id );

  if ( ! $product_disable_notes && $global_enable_notes ) { ?>

    <div class="wfs-special-instruction-wrapper">
      <p class="wfs-special-note-label"><?php _e( 'Special Note', 'food-store' ); ?></p>
      <textarea id="special_note" name="special_note" rows="3" cols="10" placeholder="<?php echo apply_filters( 'wfs_special_note_placeholder', __( 'Special notes if any.. eg: Need extra sauce..', 'food-store' ) ); ?>"></textarea>
    </div>

  <?php }
}