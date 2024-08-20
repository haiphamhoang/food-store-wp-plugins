<?php 
/**
 * Admin View: Tools Settings
 *
 * @package FoodStore
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

?>

<div class="wrap foodstore wfs-tools-settings">

  <?php do_action( 'wfs_tools_import_export_before' ); ?>

  <!-- Export Settings -->
  <div class="postbox">
    <h3>
      <span><?php esc_html_e( 'Export Settings', 'food-store' ); ?></span>
    </h3>
    <div class="inside">
      <p><?php esc_html_e( 'Export the Food Store settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'food-store' ); ?></p>
      <p><input type="hidden" name="wfs_action" value="wfs_export_settings" /></p>
			<p>
		    <?php wp_nonce_field( 'wfs_export_nonce', 'wfs_export_nonce' ); ?>
				<?php submit_button( __( 'Export', 'food-store' ), 'secondary', 'wfs_export_settings', false ); ?>
			</p>
    </div>
  </div>

  <!-- Import Settings -->
  <div class="postbox">
    <h3>
      <span><?php esc_html_e( 'Import Settings', 'food-store' ); ?></span>
    </h3>
    <div class="inside">
      <p><?php esc_html_e( 'Import all Food Store settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'food-store' ); ?></p>
      <p>
			  <input type="file" name="import_file" accept="application/JSON"/>
			</p>
			<p>
			  <input type="hidden" name="wfs_action" value="import_settings" />
				<?php wp_nonce_field( 'wfs_import_nonce', 'wfs_import_nonce' ); ?>
				<?php submit_button( __( 'Import', 'food-stote' ), 'secondary', 'wfs_import_settings', false ); ?>
			</p>
    </div>
  </div>
  <?php do_action( 'wfs_tools_import_export_after' ); ?>
</div>
