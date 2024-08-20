<?php
/**
 * FoodStore Meta Boxes
 *
 *
 * @package FoodStore\Admin\Meta Boxes
 */

defined( 'ABSPATH' ) || exit;


  class WFS_Admin_Meta_Boxes {

    /**
     * Is meta boxes saved once?
     *
     * @var boolean
     */
	  private static $saved_meta_boxes = false;

    /**
     * Meta box error messages.
     *
     * @var array
     */
	  public static $meta_box_errors = array();

    /**
     * Constructor.
     */
    public function __construct() {
      add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
      
      // Error handling (for showing errors from meta boxes on next page load).
      add_action( 'admin_notices', array( $this, 'output_errors' ) );
		  add_action( 'shutdown', array( $this, 'save_errors' ) );
      add_action( 'admin_init', array( $this, 'remove_addons_metabox'), 90 );
    }

  /**
	 * Add an error message.
	 *
	 * @param string $text Error to add.
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 */
	public function save_errors() {
		update_option( 'foodstore_meta_box_errors', self::$meta_box_errors );
	}

    /**
     * Check if we're saving, the trigger an action based on the post type.
     *
     * @param  int    $post_id Post ID.
     * @param  object $post Post object.
     */
    public function save_meta_boxes( $post_id, $post ) {

      $post_id = absint( $post_id );

		  // $post_id and $post are required
		  if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			  return;
		  }

      // Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
		  if ( empty( $_POST['post_ID'] ) || absint( $_POST['post_ID'] ) !== $post_id ) {
			  return;
		  }

      // Check user has permission to edit.
		  if ( ! current_user_can( 'edit_post', $post_id ) ) {
			  return;
		  }

      self::$saved_meta_boxes = true;

      //Save selected addons meta boxes
      $this->save_selected_addons( $post_id, $post );

      //Save addons category meta boxes
      $this->save_addons_category( $post_id, $post );

      // Hook to allow users to save any custom fields.
      do_action( 'wfs_save_product', $post_id, $post );

    }

    /**
     * Save addons meta boxes.
     *
     * @param  int    $post_id Post ID.
     * @param  object $post Post object.
     */
    public function save_addons_category( $post_id, $post ) {
      
      $addon_categories = isset( $_POST['addon_category'] ) ? $_POST['addon_category'] : [];
      $product_addon_data = [];

      if( is_array( $addon_categories ) && !empty( $addon_categories ) ) {

        foreach( $addon_categories as $key => $addon_category ) {
          
          $addon_name = !empty( $addon_category['name'] ) ? $addon_category['name'] : '';
          $addon_type = !empty( $addon_category['type'] ) ? sanitize_text_field( $addon_category['type'] ) : 'multiple';

          if( empty( $addon_name ) )
            return;

          $parent_addon = wp_insert_term( $addon_name, 'product_addon', array( 'parent' => 0, 'slug' => sanitize_title( $addon_name ) ) );

          if ( !is_wp_error( $parent_addon ) ) {

            if ( !empty( $parent_addon[ 'term_id' ] ) ) {
              
              $term_id = isset( $parent_addon[ 'term_id' ] ) ? absint( $parent_addon[ 'term_id' ] ) : NULL;

              if( !is_null( $term_id ) ) {
                
                update_term_meta( $term_id, '_wfs_addon_selection_option',  $addon_type );

                wp_set_post_terms( $post_id, $term_id, 'product_addon', true );

                $parent_addon[$key]['category'] = $term_id;
                $product_addon_data[$key]['category'] = $term_id;

                if ( !empty( $addon_category['addon_name'] ) && count( $addon_category['addon_name'] ) > 0 ) {
                  
                  foreach( $addon_category['addon_name'] as $k => $child_addon ) {
                    
                    $term_name = !empty( $child_addon ) ? $child_addon : '';

                    if( empty( $term_name ) ) 
                      return;

                    $term_price = !empty( $addon_category['addon_price'][$k] ) ? $addon_category['addon_price'][$k] : '';

                    $child_terms = wp_insert_term( $term_name, 'product_addon', array( 'parent' => $term_id, 'slug' => sanitize_title( $term_name ) ) );

                    if( !is_wp_error( $child_terms ) ) {
                      $child_term_id = isset( $child_terms[ 'term_id' ] ) ? absint( $child_terms[ 'term_id' ] ) : NULL;

                      if( !is_null( $child_term_id ) ) {
                        update_term_meta( $child_term_id, '_wfs_addon_item_price',  $term_price );
                        wp_set_post_terms( $post_id, $child_term_id, 'product_addon', true );
                        $product_addon_data[$key]['items'][] = $child_terms['term_id'];
                      }
                    }
                  }
                }
              }
            }
          }
        }
        $this->update_product_addon_items( $post_id, $product_addon_data );
      }
    }

    /**
     * Save selected addons meta boxes.
     *
     * @param  int    $post_id Post ID.
     * @param  object $post Post object.
     */
    public function save_selected_addons( $post_id, $post ) {
      
      $selected_items = isset( $_POST['_wfs_addons'] ) ? $_POST['_wfs_addons'] : [];
      $formatted_addons_array = [];
      $unique_addons_array = [];
      $saved_addons = [];
      $addon_terms_array = [];

      if( !empty( $selected_items ) ) {

        //Double check whether category is selected or not
        foreach( $selected_items as $key => $selected_item ) {
          
          if( isset( $selected_item['category']) && !empty( $selected_item['category'] ) ) {
            
            //Check unique category is always there with their items
            if( !in_array( $selected_item['category'], $unique_addons_array) ) {
              $unique_addons_array[]    = $selected_item['category'];
              $addon_terms_array[]      = $selected_item['items'];
              $formatted_addons_array[$key]['category'] = $selected_item['category'];
              $formatted_addons_array[$key]['items']    = $selected_item['items'];
            }

          }
          
        }

        if ( !empty( $addon_terms_array ) ) {
          foreach( $addon_terms_array as $addon_terms_list ) {
            foreach( $addon_terms_list as $addon_term ) {
              if( !in_array( $addon_term, $saved_addons ) ) {
                $saved_addons[] = $addon_term;
              }
            }
          }
        }
        
        //Get post terms
        $addon_categories = wp_get_post_terms( $post_id, 'product_addon', array( 'fields' => 'ids' ) );

        $addons_to_be_removed = array_diff( $addon_categories, $saved_addons );

        if( !empty( $addons_to_be_removed ) ) {
          wp_remove_object_terms( $post_id, $addons_to_be_removed, 'product_addon' );
        }

        //Let other developer use this hook to save custom data
        $formatted_addons_array = apply_filters( 'wfs_before_save_selected_addons', $formatted_addons_array, $post_id, $_POST );

        update_post_meta( $post_id, '_wfs_product_addon', $formatted_addons_array );
        wp_set_post_terms( $post_id, $saved_addons, 'product_addon', true );
      }
      
    }

    /**
     * Update product addon items.
     *
     * @param  int    $post_id Post ID.
     * @param  array  $product_addon_data Product addon data.
     */
    public function update_product_addon_items( $post_id, $product_addon_data ) {

      if( empty( $post_id ) )
        return;

      if( !empty( $product_addon_data ) ) {

        $saved_addons = [];
        //fetch product addon items
        $product_addon_items = get_post_meta( $post_id, '_wfs_product_addon', true );
        $product_addon_items = !empty( $product_addon_items ) ? maybe_unserialize( $product_addon_items ) : [];

        $items_to_saved = array_merge( $product_addon_items, $product_addon_data );

        update_post_meta( $post_id, '_wfs_product_addon', $items_to_saved );

      }
    }


    /**
     * Show any stored error messages.
     */
	  public function output_errors() {
		  $errors = array_filter( (array) get_option( 'foodstore_meta_box_errors' ) );

		  if ( ! empty( $errors ) ) {

			  echo '<div id="foodstore_errors" class="error notice is-dismissible">';

			  foreach ( $errors as $error ) {
				  echo '<p>' . wp_kses_post( $error ) . '</p>';
			  }

			  echo '</div>';

			  // Clear.
			  delete_option( 'foodstore_meta_box_errors' );
		  }
	  }

    /**
     * Remove addons metabox from admin product edit page.
     */
    public function remove_addons_metabox() {
      remove_meta_box( 'product_addondiv', 'product', 'normal' );
    }

  }

  new WFS_Admin_Meta_Boxes();