<?php
/**
 * Admin View: Food Store Child Addon
 *
 * @package FoodStore
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

ob_start();

$parent_addon_id = isset( $_POST['parent_addon_id'] ) ? absint( $_POST['parent_addon_id'] ) : NULL;
$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : NULL;
$uuid = isset( $_POST['uuid'] ) ? absint( $_POST['uuid'] ) : NULL;

if( is_null( $parent_addon_id ) || is_null( $product_id ) ) {
  return;
}

?>
<table class="wfs-addon-items">
  <thead>
    <tr>
      <th class="select_addon select_all_addons">
        <input type="checkbox" class="wfs-select-all"><strong><?php esc_html_e( 'Enable', 'food-store' ); ?></strong>
      </th>
      <th class="addon_name">
        <strong><?php esc_html_e( 'Addon Name', 'food-store' ); ?></strong>
      </th>
      <th class="addon_price">
        <strong><?php esc_html_e( 'Price', 'food-store' ); ?></strong>
      </th>
    </tr>
  </thead>
  <tbody>
    <?php
      $get_addons = wfs_get_addons( $parent_addon_id );
      if ( is_array( $get_addons ) && !empty( $get_addons ) ) :
        foreach( $get_addons as $key => $get_addon ) :
          $addon_item_id    = $get_addon->term_id;
          $addon_item_name  = $get_addon->name;
          $addon_slug       = $get_addon->slug;
          $addon_price      = get_term_meta( $addon_item_id, '_wfs_addon_item_price', true );
        ?>
        <tr>
          <td class="select_addon">
            <input type="checkbox" class="wfs-addon-item" name="_wfs_addons[<?php echo $uuid ?>][items][]" value="<?php echo esc_attr( $addon_item_id ); ?>" <?php checked( true, true ); ?>>
          </td>
          <td class="addon_name">
            <?php echo esc_html( $addon_item_name ); ?>
          </td>
          <td class="addon_price">
            <?php echo wc_price( $addon_price ); ?>
          </td>
        <?php
        endforeach;
      endif;
    ?>
  </tbody>
</table>
<?php
$html = ob_get_clean();
echo $html;