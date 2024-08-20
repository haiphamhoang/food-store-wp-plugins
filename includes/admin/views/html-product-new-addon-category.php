<?php
/**
 * Admin View: Food Store Addon Category
 *
 * @package FoodStore
 */


defined( 'ABSPATH' ) || exit;

$row = isset( $_POST['i'] ) ?  absint( $_POST['i'] ) : 0;
$addon_selection_types = wfs_get_addon_selection_types();
$uuid = isset( $_POST['uuid'] ) ? sanitize_text_field( $_POST['uuid'] ) : '';

?>

<!-- New Addon Catefory Form Starts -->
<div class="wfs-addon wfs-metabox create-new-addon">
  <h3>
		<div class="tips sort" data-tip="<?php esc_html_e( 'Drag Drop to reorder the addon categories.', 'food-store' );?>"></div>
		<strong class="addon_category_name">
			<?php esc_html_e( 'Create New Addon Category', 'food-store' ); ?>
		</strong>
    <a href="#" class="remove_row delete alignright"><?php esc_html_e( 'Remove', 'food-store' ) ?></a>
	</h3>

  <div class="wfs-metabox-content">
    <div class="wfs-metabox-content-wrapper">
      
      <div class="wfs-col-6 addon-category">
        <table class="form-table addon-category-table">
          <thead>
            <tr>
              <th scope="row">
                <?php esc_html_e( 'Addon Category:', 'default' ); ?>
              </th>
              <th scope="row">
                <?php esc_html_e( 'Type:', 'default' ); ?>
              </th>
            </tr>
          </thead>
          <tbody>
            <td>
              <input type="text" name="addon_category[<?php echo $uuid; ?>][name]" id="" class="wfs-input addon-category-name" placeholder="<?php esc_html_e( 'Addon Category Name', 'food-store' ); ?>">
            </td>
            <td>
              <select name="addon_category[<?php echo $uuid; ?>][type]" class="wfs-input addon-category-type">
                <?php foreach( $addon_selection_types as $addon_selection_type ) : ?>
                  <option value="<?php echo $addon_selection_type; ?>"><?php echo $addon_selection_type; ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tbody>
        </table>
      </div>

      <div class="wfs-col-6 addon-items">
        <table class="form-table addon-category-table">
          <thead>
            <tr>
              <th scope="row">
                <?php esc_html_e( 'Addon Items:', 'food-store' ); ?>
              </th>
              <th scope="row" class="addon-price-symbol">
                <?php 
                /* translators: %s: get_woocommerce_currency_symbol() get price symbol */
                echo sprintf( __( 'Price (%s)', 'food-store' ), get_woocommerce_currency_symbol() ); ?>
              </th>
              <th scope="row">&nbsp</th>
            </tr>
          </thead>
          <tbody>
            <tr class="addon-items-row">
              <td class="wfs-input-element">
                <input type="text" name="addon_category[<?php echo $uuid; ?>][addon_name][]" class="wfs-input" placeholder="<?php esc_html_e( 'Addon Item Name', 'food-store' ); ?>">
              </td>
              <td>                
                <input type="text" name="addon_category[<?php echo $uuid; ?>][addon_price][]" class="wfs-input wfs-addon-price" placeholder="9.99">
              </td>
              <td>
                <span class="remove wfs-addon-cat">
                  <span class="dashicons dashicons-dismiss"></span>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="clear"></div>
    </div>
    <div class="toolbar-bottom toolbar">
      <button type="button" class="button button-primary add-new-addon alignright add-addon-multiple-item"> + <?php esc_html_e( 'Add New', 'food-store' ); ?></button>
    </div>
  </div>
</div>
<!-- New Addon Catefory Form Ends -->