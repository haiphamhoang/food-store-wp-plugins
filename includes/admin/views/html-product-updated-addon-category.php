<?php
/**
 * Get Product Addons.
 *
 * @package FoodStore/Admin
 */

defined( 'ABSPATH' ) || exit;

$post_id        = get_the_ID();
$get_all_addons = wfs_get_addons();

if ( is_array( $get_all_addons ) && !empty( $get_all_addons ) ) :
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
      $term_childrens     = get_term_children( $addon_category_id, 'product_addon' );
      $product_addon_list = isset( $product_addon['items'] ) ? $product_addon['items'] : [];

      if( is_array( $term_childrens ) && !empty( $term_childrens ) ) :
        foreach( $term_childrens as $addon_item_id ) :
          $is_selected      = in_array( $addon_item_id, $product_addon_list ) ? $addon_item_id : '';
          $product_addon    = get_term_by( 'id', absint( $addon_item_id ), 'product_addon' );
          $addon_item_name  = $product_addon->name;
          $addon_slug       = $product_addon->slug;
          $addon_price      = get_term_meta( $addon_item_id, '_wfs_addon_item_price', true );
          $uuid             = time();
          ?>
          <tr>
              <td class="select_addon">
                <input type="checkbox" class="wfs-addon-item" name="_wfs_addons[<?php echo $key; ?>][items][]"  value="<?php echo esc_attr( $addon_item_id ); ?>" <?php checked( $is_selected, $addon_item_id ); ?>>
              </td>
              <td class="addon_name">
                <?php echo esc_html( $addon_item_name ); ?>
              </td>
              <td class="addon_price">
                <?php echo wc_price( $addon_price ); ?>
              </td>
            </tr>
          <?php
        endforeach;
      endif;
    ?>
  </tbody>
</table>
<?php
endif;